<?php

namespace App\Http\Controllers;

use App\Models\CourseOpening;
use App\Models\CourseOpeningStudent;
use App\Models\CourseStudentPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CoursePaymentController extends Controller
{
    /**
     * Panel de pagos de una apertura.
     */
    public function index(CourseOpening $courseOpening)
    {
        $courseOpening->load([
            'course', 'branch',
            'enrollments.client',
            'enrollments.employee',
            'enrollments.payments',
        ]);

        // Totales globales
        $summary = $this->buildSummary($courseOpening);

        return view('course-openings.payments.index', compact('courseOpening', 'summary'));
    }

    /**
     * Generar cuotas automáticamente para todos (o un estudiante) de la apertura.
     */
    public function generate(Request $request, CourseOpening $courseOpening)
    {
        $data = $request->validate([
            'payment_type'         => 'required|in:unico,mensual,por_sesion,semanal,personalizado',
            'installments'         => 'nullable|integer|min:1|max:60',
            'unit_price'           => 'required|numeric|min:0',
            'first_due_date'       => 'nullable|date',
            'enrollment_ids'       => 'nullable|array',
            'enrollment_ids.*'     => 'exists:course_opening_student,id',
            'overwrite'            => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            // Actualizar tipo de pago en la apertura
            $courseOpening->update([
                'payment_type'          => $data['payment_type'],
                'payment_installments'  => $data['installments'] ?? null,
                'payment_unit_price'    => $data['unit_price'],
            ]);

            $enrollmentIds = $data['enrollment_ids']
                ?? $courseOpening->enrollments()->pluck('id')->toArray();

            foreach ($enrollmentIds as $enrollmentId) {
                $enrollment = CourseOpeningStudent::find($enrollmentId);
                if (!$enrollment) continue;

                if ($request->boolean('overwrite')) {
                    $enrollment->payments()->where('status', 'pendiente')->delete();
                }

                $this->createInstallments($courseOpening, $enrollment, $data);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al generar pagos: ' . $e->getMessage());
        }

        return back()->with('success', 'Cuotas generadas correctamente.');
    }

    /**
     * Registrar un pago (parcial o total) sobre una cuota.
     */
    public function pay(Request $request, CourseStudentPayment $payment)
    {
        $data = $request->validate([
            'amount_paid'    => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:efectivo,transferencia,tarjeta,yape,plin,otro',
            'paid_at'        => 'required|date',
            'reference'      => 'nullable|string|max:100',
            'notes'          => 'nullable|string|max:300',
        ]);

        $totalPaid = $payment->amount_paid + $data['amount_paid'];

        $status = match(true) {
            $totalPaid >= $payment->amount_due => 'pagado',
            $totalPaid > 0                     => 'parcial',
            default                            => 'pendiente',
        };

        $payment->update([
            'amount_paid'    => min($totalPaid, $payment->amount_due),
            'status'         => $status,
            'payment_method' => $data['payment_method'],
            'paid_at'        => $status === 'pagado' ? $data['paid_at'] : $payment->paid_at,
            'reference'      => $data['reference'] ?? $payment->reference,
            'notes'          => $data['notes'] ?? $payment->notes,
            'recorded_by'    => auth()->id(),
        ]);

        // Sincronizar estado general del enrollment
        $this->syncEnrollmentPaymentStatus($payment->enrollment);

        return back()->with('success', 'Pago registrado correctamente.');
    }

    /**
     * Marcar cuota como becado/anulado.
     */
    public function updateStatus(Request $request, CourseStudentPayment $payment)
    {
        $data = $request->validate([
            'status' => 'required|in:becado,anulado,pendiente,vencido',
            'notes'  => 'nullable|string|max:300',
        ]);

        $payment->update([
            'status'      => $data['status'],
            'notes'       => $data['notes'] ?? $payment->notes,
            'recorded_by' => auth()->id(),
        ]);

        $this->syncEnrollmentPaymentStatus($payment->enrollment);

        return back()->with('success', 'Estado actualizado.');
    }

    /**
     * Agregar pago/ajuste manual.
     */
    public function store(Request $request, CourseOpening $courseOpening)
    {
        $data = $request->validate([
            'course_opening_student_id' => 'required|exists:course_opening_student,id',
            'concept'        => 'required|string|max:200',
            'amount_due'     => 'required|numeric|min:0',
            'amount_paid'    => 'nullable|numeric|min:0',
            'due_date'       => 'nullable|date',
            'paid_at'        => 'nullable|date',
            'payment_method' => 'nullable|in:efectivo,transferencia,tarjeta,yape,plin,otro',
            'reference'      => 'nullable|string|max:100',
            'notes'          => 'nullable|string',
            'status'         => 'required|in:pendiente,pagado,parcial,becado,anulado',
        ]);

        $amountPaid = $data['amount_paid'] ?? 0;

        CourseStudentPayment::create([
            'course_opening_student_id' => $data['course_opening_student_id'],
            'course_opening_id'         => $courseOpening->id,
            'payment_type'              => 'ajuste',
            'concept'                   => $data['concept'],
            'amount_due'                => $data['amount_due'],
            'amount_paid'               => $amountPaid,
            'status'                    => $data['status'],
            'due_date'                  => $data['due_date'] ?? null,
            'paid_at'                   => $data['paid_at'] ?? null,
            'payment_method'            => $data['payment_method'] ?? null,
            'reference'                 => $data['reference'] ?? null,
            'notes'                     => $data['notes'] ?? null,
            'recorded_by'               => auth()->id(),
        ]);

        $enrollment = CourseOpeningStudent::find($data['course_opening_student_id']);
        $this->syncEnrollmentPaymentStatus($enrollment);

        return back()->with('success', 'Pago manual registrado.');
    }

    public function destroy(CourseStudentPayment $payment)
    {
        $enrollment = $payment->enrollment;
        $payment->delete();
        $this->syncEnrollmentPaymentStatus($enrollment);

        return back()->with('success', 'Pago eliminado.');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function createInstallments(
        CourseOpening $opening,
        CourseOpeningStudent $enrollment,
        array $data
    ): void {
        $unitPrice   = (float) $data['unit_price'];
        $type        = $data['payment_type'];
        $firstDue    = isset($data['first_due_date']) ? Carbon::parse($data['first_due_date']) : null;
        $installments = (int) ($data['installments'] ?? 1);

        match($type) {
            'unico' => $this->createSingle($opening, $enrollment, $unitPrice, $firstDue),

            'mensual' => $this->createPeriodic(
                $opening, $enrollment, $unitPrice, $installments, $firstDue, 'mensual', 'month', 1
            ),

            'semanal' => $this->createPeriodic(
                $opening, $enrollment, $unitPrice, $installments, $firstDue, 'semanal', 'week', 1
            ),

            'por_sesion' => $this->createPeriodic(
                $opening, $enrollment, $unitPrice,
                $opening->total_sessions, $firstDue, 'por_sesion', 'week', 0
            ),

            default => null,
        };
    }

    private function createSingle(CourseOpening $opening, CourseOpeningStudent $enrollment, float $amount, ?Carbon $dueDate): void
    {
        CourseStudentPayment::firstOrCreate(
            [
                'course_opening_student_id' => $enrollment->id,
                'course_opening_id'         => $opening->id,
                'payment_type'              => 'unico',
                'installment_number'        => null,
            ],
            [
                'concept'    => 'Pago único',
                'amount_due' => $amount,
                'amount_paid'=> 0,
                'status'     => 'pendiente',
                'due_date'   => $dueDate,
            ]
        );
    }

    private function createPeriodic(
        CourseOpening $opening,
        CourseOpeningStudent $enrollment,
        float $unitPrice,
        int $count,
        ?Carbon $firstDue,
        string $type,
        string $unit,
        int $addPerIteration
    ): void {
        $due = $firstDue ? $firstDue->copy() : ($opening->start_date ? $opening->start_date->copy() : now());

        $conceptMap = [
            'mensual'    => 'Mes',
            'semanal'    => 'Semana',
            'por_sesion' => 'Sesión',
        ];
        $conceptPrefix = $conceptMap[$type] ?? 'Cuota';

        for ($i = 1; $i <= $count; $i++) {
            CourseStudentPayment::firstOrCreate(
                [
                    'course_opening_student_id' => $enrollment->id,
                    'course_opening_id'         => $opening->id,
                    'payment_type'              => $type,
                    'installment_number'        => $i,
                ],
                [
                    'concept'    => "{$conceptPrefix} {$i}",
                    'amount_due' => $unitPrice,
                    'amount_paid'=> 0,
                    'status'     => 'pendiente',
                    'due_date'   => $due->copy(),
                ]
            );

            if ($addPerIteration > 0) {
                $unit === 'month' ? $due->addMonth() : $due->addWeek();
            }
        }
    }

    private function syncEnrollmentPaymentStatus(CourseOpeningStudent $enrollment): void
    {
        $payments = $enrollment->payments()->whereNotIn('status', ['anulado'])->get();
        if ($payments->isEmpty()) return;

        $allPaid  = $payments->every(fn($p) => in_array($p->status, ['pagado', 'becado']));
        $anyPaid  = $payments->some(fn($p) => in_array($p->status, ['pagado', 'parcial']));
        $anyOverdue = $payments->some(fn($p) => $p->status === 'vencido');

        $status = match(true) {
            $allPaid    => 'pagado',
            $anyOverdue => 'parcial',
            $anyPaid    => 'parcial',
            default     => 'pendiente',
        };

        $enrollment->update(['payment_status' => $status]);
    }

    private function buildSummary(CourseOpening $courseOpening): array
    {
        $payments = CourseStudentPayment::where('course_opening_id', $courseOpening->id)
            ->whereNotIn('status', ['anulado'])
            ->get();

        return [
            'total_due'      => $payments->sum('amount_due'),
            'total_paid'     => $payments->sum('amount_paid'),
            'total_pending'  => $payments->where('status', 'pendiente')->sum(fn($p) => $p->amount_due - $p->amount_paid),
            'total_overdue'  => $payments->where('status', 'vencido')->sum(fn($p) => $p->amount_due - $p->amount_paid),
            'count_paid'     => $payments->where('status', 'pagado')->count(),
            'count_pending'  => $payments->whereIn('status', ['pendiente', 'parcial'])->count(),
            'count_overdue'  => $payments->where('status', 'vencido')->count(),
            'count_becado'   => $payments->where('status', 'becado')->count(),
        ];
    }
}