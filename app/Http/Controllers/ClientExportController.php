<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class ClientExportController extends Controller
{
    // ── Colores ────────────────────────────────────────────────────────────────
    const VIOLET       = 'FF7C3AED';
    const VIOLET_LIGHT = 'FFEDE9FE';
    const BLUE         = 'FF2563EB';
    const BLUE_LIGHT   = 'FFDBEAFE';
    const AMBER        = 'FFD97706';
    const AMBER_LIGHT  = 'FFFEF3C7';
    const GREEN        = 'FF059669';
    const GREEN_LIGHT  = 'FFD1FAE5';
    const RED          = 'FFDC2626';
    const RED_LIGHT    = 'FFFEE2E2';
    const GRAY_DARK    = 'FF374151';
    const GRAY_MED     = 'FF6B7280';
    const GRAY_LIGHT   = 'FFF9FAFB';
    const WHITE        = 'FFFFFFFF';

    public function export(Request $request)
    {
        // ── Datos de la base de datos ─────────────────────────────────────────
        $clients = Client::all();

        $stats = [
            'total'      => $clients->count(),
            'frecuente'  => $clients->where('client_mode', 'frecuente')->count(),
            'ocasional'  => $clients->where('client_mode', 'ocasional')->count(),
            'vip'        => $clients->where('client_type', 'vip')->count(),
            'recurrente' => $clients->where('client_type', 'recurrente')->count(),
            'nuevo'      => $clients->where('client_type', 'nuevo')->count(),
            'inactivo'   => $clients->where('client_type', 'inactivo')->count(),
            'unico'      => $clients->where('client_type', 'unico')->count(),
        ];

        $bySource = $clients->groupBy('acquisition_source')
            ->map(fn($g) => [
                'total'     => $g->count(),
                'frecuente' => $g->where('client_mode', 'frecuente')->count(),
                'ocasional' => $g->where('client_mode', 'ocasional')->count(),
            ]);

        $byDept = $clients->whereNotNull('department')
            ->groupBy('department')
            ->map(fn($g) => [
                'total'     => $g->count(),
                'frecuente' => $g->where('client_mode', 'frecuente')->count(),
                'ocasional' => $g->where('client_mode', 'ocasional')->count(),
            ])
            ->sortByDesc(fn($v) => $v['total'])
            ->take(15);

        $byGender = $clients->whereNotNull('gender')
            ->groupBy('gender')
            ->map(fn($g) => [
                'total'     => $g->count(),
                'frecuente' => $g->where('client_mode', 'frecuente')->count(),
                'ocasional' => $g->where('client_mode', 'ocasional')->count(),
            ]);

        // ── Crear Spreadsheet ─────────────────────────────────────────────────
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('Dynasty')
            ->setTitle('Reporte de Clientes Dynasty')
            ->setSubject('Clientes')
            ->setDescription('Exportado desde Dynasty CRM');

        // ── HOJA 1: Resumen General ───────────────────────────────────────────
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Resumen General');
        $this->buildResumen($sheet1, $stats);

        // ── HOJA 2: Listado Completo ──────────────────────────────────────────
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Clientes');
        $this->buildListado($sheet2, $clients);

        // ── HOJA 3: Por Captación ─────────────────────────────────────────────
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Captación');
        $this->buildCaptacion($sheet3, $bySource, $stats['total']);

        // ── HOJA 4: Por Ubicación ─────────────────────────────────────────────
        $sheet4 = $spreadsheet->createSheet();
        $sheet4->setTitle('Por Ubicación');
        $this->buildUbicacion($sheet4, $byDept, $stats['total']);

        // ── HOJA 5: Por Género ────────────────────────────────────────────────
        $sheet5 = $spreadsheet->createSheet();
        $sheet5->setTitle('Por Género');
        $this->buildGenero($sheet5, $byGender, $stats['total']);

        // ── HOJA 6: Frecuentes vs Ocasionales ────────────────────────────────
        $sheet6 = $spreadsheet->createSheet();
        $sheet6->setTitle('Frecuentes vs Ocasionales');
        $this->buildModoDetalle($sheet6, $clients);

        // ── Descargar ─────────────────────────────────────────────────────────
        $spreadsheet->setActiveSheetIndex(0);
        $filename = 'clientes_dynasty_' . now()->format('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    // ── Helpers de estilo ──────────────────────────────────────────────────────

    private function hStyle(string $bgColor, int $size = 11): array
    {
        return [
            'font'      => ['bold' => true, 'color' => ['argb' => self::WHITE], 'size' => $size, 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE5E7EB']]],
        ];
    }

    private function cellStyle(string $textColor = self::GRAY_DARK, string $bg = self::GRAY_LIGHT,
                                bool $bold = false, string $align = 'left'): array
    {
        return [
            'font'      => ['bold' => $bold, 'color' => ['argb' => $textColor], 'size' => 10, 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bg]],
            'alignment' => ['horizontal' => $align === 'center'
                                ? Alignment::HORIZONTAL_CENTER
                                : Alignment::HORIZONTAL_LEFT,
                            'vertical'   => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE5E7EB']]],
        ];
    }

    private function titleRow($sheet, string $range, string $text, string $bgColor, int $size = 14): void
    {
        $sheet->mergeCells($range);
        [$start] = explode(':', $range);
        $sheet->setCellValue($start, $text);
        $sheet->getStyle($range)->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => self::WHITE], 'size' => $size, 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
    }

    // ── Hoja 1: Resumen ────────────────────────────────────────────────────────

    private function buildResumen($sheet, array $stats): void
    {
       $sheet->setShowGridlines(false);

        // Título
        $this->titleRow($sheet, 'A1:H1', 'REPORTE DE CLIENTES — DYNASTY', self::VIOLET, 16);
        $sheet->getRowDimension(1)->setRowHeight(40);

        $sheet->mergeCells('A2:H2');
        $sheet->setCellValue('A2', 'Generado el ' . now()->format('d/m/Y H:i'));
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['size' => 10, 'color' => ['argb' => self::GRAY_MED], 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::VIOLET_LIGHT]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);

        // Sección KPIs
        $this->titleRow($sheet, 'A4:H4', 'MÉTRICAS GENERALES', self::VIOLET_LIGHT, 11);
        $sheet->getStyle('A4')->getFont()->setColor(new Color(self::VIOLET));
        $sheet->getRowDimension(4)->setRowHeight(24);

        $kpis = [
            ['Total',      $stats['total'],      self::VIOLET],
            ['Frecuentes', $stats['frecuente'],  self::VIOLET],
            ['Ocasionales',$stats['ocasional'],  self::BLUE],
            ['VIP',        $stats['vip'],        self::AMBER],
            ['Recurrentes',$stats['recurrente'], '7E22CE'],
            ['Nuevos',     $stats['nuevo'],      self::BLUE],
            ['Inactivos',  $stats['inactivo'],   self::GRAY_MED],
            ['Únicos',     $stats['unico'],      self::RED],
        ];
        $cols = ['A','B','C','D','E','F','G','H'];
        foreach ($kpis as $i => [$label, $value, $color]) {
            $col = $cols[$i];
            $sheet->setCellValue("{$col}5", $label);
            $sheet->getStyle("{$col}5")->applyFromArray($this->hStyle('FF' . ltrim($color, 'FF'), 9));
            $sheet->getRowDimension(5)->setRowHeight(28);

            $sheet->setCellValue("{$col}6", $value);
            $sheet->getStyle("{$col}6")->applyFromArray([
                'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF' . ltrim($color, 'FF')], 'name' => 'Arial'],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE5E7EB']]],
            ]);
            $sheet->getRowDimension(6)->setRowHeight(36);
            $sheet->getColumnDimension($col)->setWidth(14);
        }

        // Sección modos
        $this->titleRow($sheet, 'A8:D8', 'DISTRIBUCIÓN POR MODO', self::VIOLET_LIGHT, 11);
        $sheet->getStyle('A8')->getFont()->setColor(new Color(self::VIOLET));
        $sheet->getRowDimension(8)->setRowHeight(24);

        $modos = [
            ['Frecuente', $stats['frecuente'], self::VIOLET_LIGHT, self::VIOLET],
            ['Ocasional', $stats['ocasional'], self::BLUE_LIGHT,   self::BLUE],
        ];
        foreach ($modos as $i => [$label, $count, $bg, $textColor]) {
            $r = 9 + $i;
            $pct = $stats['total'] > 0 ? round($count / $stats['total'] * 100, 1) : 0;
            $sheet->setCellValue("A{$r}", $label);
            $sheet->getStyle("A{$r}")->applyFromArray($this->cellStyle(self::GRAY_DARK, $bg, true));
            $sheet->setCellValue("B{$r}", $count);
            $sheet->getStyle("B{$r}")->applyFromArray($this->cellStyle($textColor, $bg, true, 'center'));
            $sheet->setCellValue("C{$r}", "{$pct}%");
            $sheet->getStyle("C{$r}")->applyFromArray($this->cellStyle(self::GRAY_MED, $bg, false, 'center'));
            $sheet->getRowDimension($r)->setRowHeight(26);
        }

        // Sección tipos
        $this->titleRow($sheet, 'E8:H8', 'DISTRIBUCIÓN POR TIPO', self::VIOLET_LIGHT, 11);
        $sheet->getStyle('E8')->getFont()->setColor(new Color(self::VIOLET));

        $tipos = [
            ['VIP',        $stats['vip'],        self::AMBER_LIGHT,  self::AMBER],
            ['Recurrente', $stats['recurrente'],  self::VIOLET_LIGHT, self::VIOLET],
            ['Nuevo',      $stats['nuevo'],       self::BLUE_LIGHT,   self::BLUE],
            ['Inactivo',   $stats['inactivo'],    self::GRAY_LIGHT,   self::GRAY_MED],
            ['Único',      $stats['unico'],       self::RED_LIGHT,    self::RED],
        ];
        foreach ($tipos as $i => [$label, $count, $bg, $textColor]) {
            $r = 9 + $i;
            $pct = $stats['total'] > 0 ? round($count / $stats['total'] * 100, 1) : 0;
            $sheet->setCellValue("E{$r}", $label);
            $sheet->getStyle("E{$r}")->applyFromArray($this->cellStyle(self::GRAY_DARK, $bg, true));
            $sheet->setCellValue("F{$r}", $count);
            $sheet->getStyle("F{$r}")->applyFromArray($this->cellStyle($textColor, $bg, true, 'center'));
            $sheet->setCellValue("G{$r}", "{$pct}%");
            $sheet->getStyle("G{$r}")->applyFromArray($this->cellStyle(self::GRAY_MED, $bg, false, 'center'));
            $sheet->getRowDimension($r)->setRowHeight(26);
        }
    }

    // ── Hoja 2: Listado completo ───────────────────────────────────────────────

    private function buildListado($sheet, $clients): void
    {
      $sheet->setShowGridlines(false);
        $this->titleRow($sheet, 'A1:M1', 'LISTADO COMPLETO DE CLIENTES', self::VIOLET, 13);
        $sheet->getRowDimension(1)->setRowHeight(32);

        $sheet->mergeCells('A2:M2');
        $sheet->setCellValue('A2', 'Total: ' . $clients->count() . ' clientes · Exportado ' . now()->format('d/m/Y H:i'));
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['size' => 9, 'color' => ['argb' => self::GRAY_MED], 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::VIOLET_LIGHT]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(18);

        $headers = ['#','Nombre','Apellido','Teléfono','Email','Modo','Tipo','Departamento','Distrito','Fuente','Etiquetas','Notas','Registrado'];
        $widths   = [5, 16, 16, 14, 24, 12, 12, 16, 18, 14, 22, 26, 14];
        $cols     = range('A', 'M');

        foreach ($headers as $i => $h) {
            $col = $cols[$i];
            $sheet->setCellValue("{$col}3", $h);
            $sheet->getStyle("{$col}3")->applyFromArray($this->hStyle(self::VIOLET));
            $sheet->getColumnDimension($col)->setWidth($widths[$i]);
        }
        $sheet->getRowDimension(3)->setRowHeight(28);

        $row = 4;
        foreach ($clients as $idx => $client) {
            $bg = $idx % 2 === 0 ? self::WHITE : self::GRAY_LIGHT;

            $modeColor = $client->client_mode === 'frecuente' ? self::VIOLET : self::BLUE;
            $typeColors = [
                'vip'        => self::AMBER,
                'recurrente' => self::VIOLET,
                'nuevo'      => self::BLUE,
                'inactivo'   => self::GRAY_MED,
                'unico'      => self::RED,
            ];
            $typeColor = $typeColors[$client->client_type] ?? self::GRAY_MED;

            $data = [
                $idx + 1,
                $client->first_name,
                $client->last_name,
                $client->phone ?? '',
                $client->email ?? '',
                $client->client_mode_label,
                $client->client_type_label,
                $client->department ?? '',
                $client->district ?? '',
                $client->acquisition_label,
                $client->tags ?? '',
                $client->notes ?? '',
                $client->created_at?->format('d/m/Y') ?? '',
            ];

            foreach ($data as $i => $value) {
                $col = $cols[$i];
                $sheet->setCellValue("{$col}{$row}", $value);

                // Colorear Modo y Tipo
                if ($i === 5) {
                    $sheet->getStyle("{$col}{$row}")->applyFromArray($this->cellStyle($modeColor, $bg, true, 'center'));
                } elseif ($i === 6) {
                    $sheet->getStyle("{$col}{$row}")->applyFromArray($this->cellStyle($typeColor, $bg, true, 'center'));
                } else {
                    $sheet->getStyle("{$col}{$row}")->applyFromArray($this->cellStyle(self::GRAY_DARK, $bg));
                }
            }
            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;
        }

        // Freeze header
        $sheet->freezePane('A4');
        // Auto filter
        $sheet->setAutoFilter("A3:M3");
    }

    // ── Hoja 3: Por Captación ──────────────────────────────────────────────────

    private function buildCaptacion($sheet, $bySource, int $total): void
    {
       $sheet->setShowGridlines(false);
        $this->titleRow($sheet, 'A1:F1', 'REPORTE POR FUENTE DE CAPTACIÓN', self::BLUE, 13);
        $sheet->getRowDimension(1)->setRowHeight(32);

        $sheet->mergeCells('A2:F2');
        $sheet->setCellValue('A2', 'Análisis de canales de adquisición de clientes');
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['size' => 9, 'italic' => true, 'color' => ['argb' => self::GRAY_MED], 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::BLUE_LIGHT]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(18);

        $headers = ['Canal','Clientes','% del Total','Frecuentes','Ocasionales','Tasa Frec.'];
        $widths   = [20, 12, 14, 14, 14, 14];
        foreach ($headers as $i => $h) {
            $col = chr(65 + $i);
            $sheet->setCellValue("{$col}3", $h);
            $sheet->getStyle("{$col}3")->applyFromArray($this->hStyle(self::BLUE));
            $sheet->getColumnDimension($col)->setWidth($widths[$i]);
        }
        $sheet->getRowDimension(3)->setRowHeight(28);

        $labelMap = [
            'instagram' => '📸 Instagram',
            'facebook'  => '👥 Facebook',
            'tiktok'    => '🎵 TikTok',
            'google'    => '🔍 Google',
            'referido'  => '🤝 Referido',
            'walk_in'   => '🚶 Walk-in',
            'whatsapp'  => '💬 WhatsApp',
            'otro'      => 'Otro',
        ];
        $bgs = [self::VIOLET_LIGHT, self::BLUE_LIGHT, self::RED_LIGHT, self::GREEN_LIGHT,
                self::AMBER_LIGHT, self::GRAY_LIGHT, self::GREEN_LIGHT, self::GRAY_LIGHT];

        $row = 4;
        $grandTotal = 0;
        foreach ($labelMap as $idx => [$key, $label]) {
            // Fix: $labelMap is key=>label not key=>[key,label]
        }

        $row = 4;
        $grandTotal = 0;
        foreach ($labelMap as $key => $label) {
            $data   = $bySource->get($key, ['total' => 0, 'frecuente' => 0, 'ocasional' => 0]);
            $count  = $data['total'];
            $frec   = $data['frecuente'];
            $ocas   = $data['ocasional'];
            $pct    = $total > 0 ? round($count / $total * 100, 1) : 0;
            $tasaF  = $count > 0 ? round($frec / $count * 100, 1) : 0;
            $bg     = $bgs[array_search($key, array_keys($labelMap)) % count($bgs)];
            $grandTotal += $count;

            $vals = [$label, $count, "{$pct}%", $frec, $ocas, "{$tasaF}%"];
            foreach ($vals as $i => $v) {
                $col = chr(65 + $i);
                $sheet->setCellValue("{$col}{$row}", $v);
                $align = $i === 0 ? 'left' : 'center';
                $bold  = in_array($i, [0, 1]);
                $color = match($i) {
                    1 => self::BLUE,
                    2 => self::GRAY_MED,
                    3 => self::VIOLET,
                    4 => self::BLUE,
                    5 => self::GREEN,
                    default => self::GRAY_DARK,
                };
                $sheet->getStyle("{$col}{$row}")->applyFromArray($this->cellStyle($color, $bg, $bold, $align));
            }
            $sheet->getRowDimension($row)->setRowHeight(24);
            $row++;
        }

        // Fila total
        $sheet->setCellValue("A{$row}", 'TOTAL');
        $sheet->setCellValue("B{$row}", $grandTotal);
        $sheet->getStyle("A{$row}:F{$row}")->applyFromArray($this->hStyle(self::BLUE, 10));
    }

    // ── Hoja 4: Por Ubicación ──────────────────────────────────────────────────

    private function buildUbicacion($sheet, $byDept, int $total): void
    {
      $sheet->setShowGridlines(false);
        $this->titleRow($sheet, 'A1:E1', 'REPORTE POR UBICACIÓN', self::GREEN, 13);
        $sheet->getRowDimension(1)->setRowHeight(32);

        $sheet->mergeCells('A2:E2');
        $sheet->setCellValue('A2', 'Distribución geográfica de clientes');
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['size' => 9, 'italic' => true, 'color' => ['argb' => self::GRAY_MED], 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::GREEN_LIGHT]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(18);

        $headers = ['Departamento','Total','% Total','Frecuentes','Ocasionales'];
        $widths   = [22, 12, 12, 14, 14];
        foreach ($headers as $i => $h) {
            $col = chr(65 + $i);
            $sheet->setCellValue("{$col}3", $h);
            $sheet->getStyle("{$col}3")->applyFromArray($this->hStyle(self::GREEN));
            $sheet->getColumnDimension($col)->setWidth($widths[$i]);
        }
        $sheet->getRowDimension(3)->setRowHeight(28);

        $row = 4;
        foreach ($byDept as $dept => $data) {
            $pct = $total > 0 ? round($data['total'] / $total * 100, 1) : 0;
            $bg  = $row % 2 === 0 ? self::WHITE : self::GRAY_LIGHT;

            $sheet->setCellValue("A{$row}", $dept);
            $sheet->getStyle("A{$row}")->applyFromArray($this->cellStyle(self::GRAY_DARK, $bg, true));
            $sheet->setCellValue("B{$row}", $data['total']);
            $sheet->getStyle("B{$row}")->applyFromArray($this->cellStyle(self::GREEN, $bg, true, 'center'));
            $sheet->setCellValue("C{$row}", "{$pct}%");
            $sheet->getStyle("C{$row}")->applyFromArray($this->cellStyle(self::GRAY_MED, $bg, false, 'center'));
            $sheet->setCellValue("D{$row}", $data['frecuente']);
            $sheet->getStyle("D{$row}")->applyFromArray($this->cellStyle(self::VIOLET, $bg, false, 'center'));
            $sheet->setCellValue("E{$row}", $data['ocasional']);
            $sheet->getStyle("E{$row}")->applyFromArray($this->cellStyle(self::BLUE, $bg, false, 'center'));

            $sheet->getRowDimension($row)->setRowHeight(24);
            $row++;
        }

        if ($byDept->isEmpty()) {
            $sheet->mergeCells("A4:E4");
            $sheet->setCellValue('A4', 'Sin datos de ubicación registrados');
            $sheet->getStyle('A4')->applyFromArray($this->cellStyle(self::GRAY_MED, self::GRAY_LIGHT, false, 'center'));
        }
    }

    // ── Hoja 5: Por Género ─────────────────────────────────────────────────────

    private function buildGenero($sheet, $byGender, int $total): void
    {
       $sheet->setShowGridlines(false);
        $this->titleRow($sheet, 'A1:E1', 'REPORTE POR GÉNERO', self::AMBER, 13);
        $sheet->getRowDimension(1)->setRowHeight(32);

        $headers = ['Género','Total','% Total','Frecuentes','Ocasionales'];
        $widths   = [18, 12, 12, 14, 14];
        foreach ($headers as $i => $h) {
            $col = chr(65 + $i);
            $sheet->setCellValue("{$col}3", $h);
            $sheet->getStyle("{$col}3")->applyFromArray($this->hStyle(self::AMBER));
            $sheet->getColumnDimension($col)->setWidth($widths[$i]);
        }
        $sheet->getRowDimension(3)->setRowHeight(28);

        $generoLabels = ['femenino' => 'Femenino','masculino' => 'Masculino','otro' => 'Otro','no_especifica' => 'No especifica'];
        $genBgs       = [self::VIOLET_LIGHT, self::BLUE_LIGHT, self::GREEN_LIGHT, self::GRAY_LIGHT];

        $row = 4;
        foreach ($generoLabels as $i => [$key, $label]) {}

        $row = 4;
        $keys = array_keys($generoLabels);
        foreach ($generoLabels as $key => $label) {
            $data = $byGender->get($key, ['total' => 0, 'frecuente' => 0, 'ocasional' => 0]);
            $pct  = $total > 0 ? round($data['total'] / $total * 100, 1) : 0;
            $bg   = $genBgs[array_search($key, $keys) % count($genBgs)];

            $sheet->setCellValue("A{$row}", $label);
            $sheet->getStyle("A{$row}")->applyFromArray($this->cellStyle(self::GRAY_DARK, $bg, true));
            $sheet->setCellValue("B{$row}", $data['total']);
            $sheet->getStyle("B{$row}")->applyFromArray($this->cellStyle(self::AMBER, $bg, true, 'center'));
            $sheet->setCellValue("C{$row}", "{$pct}%");
            $sheet->getStyle("C{$row}")->applyFromArray($this->cellStyle(self::GRAY_MED, $bg, false, 'center'));
            $sheet->setCellValue("D{$row}", $data['frecuente']);
            $sheet->getStyle("D{$row}")->applyFromArray($this->cellStyle(self::VIOLET, $bg, false, 'center'));
            $sheet->setCellValue("E{$row}", $data['ocasional']);
            $sheet->getStyle("E{$row}")->applyFromArray($this->cellStyle(self::BLUE, $bg, false, 'center'));

            $sheet->getRowDimension($row)->setRowHeight(24);
            $row++;
        }
    }

    // ── Hoja 6: Frecuentes vs Ocasionales ─────────────────────────────────────

    private function buildModoDetalle($sheet, $clients): void
    {
     $sheet->setShowGridlines(false);
        $this->titleRow($sheet, 'A1:G1', 'CLIENTES FRECUENTES vs OCASIONALES', self::VIOLET, 13);
        $sheet->getRowDimension(1)->setRowHeight(32);

        // Tabla frecuentes
        $this->titleRow($sheet, 'A3:G3', 'CLIENTES FRECUENTES', self::VIOLET, 11);
        $sheet->getRowDimension(3)->setRowHeight(28);

        $headers = ['Nombre Completo','Teléfono','Email','Tipo','Departamento','Fuente','Registrado'];
        $widths   = [24, 14, 26, 12, 16, 14, 14];
        $cols     = ['A','B','C','D','E','F','G'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValue("{$cols[$i]}4", $h);
            $sheet->getStyle("{$cols[$i]}4")->applyFromArray($this->hStyle(self::VIOLET, 9));
            $sheet->getColumnDimension($cols[$i])->setWidth($widths[$i]);
        }
        $sheet->getRowDimension(4)->setRowHeight(24);

        $row = 5;
        $frecuentes = $clients->where('client_mode', 'frecuente');
        foreach ($frecuentes as $idx => $client) {
            $bg = $idx % 2 === 0 ? self::VIOLET_LIGHT : self::WHITE;
            $typeColors = ['vip' => self::AMBER,'recurrente' => self::VIOLET,'nuevo' => self::BLUE,'inactivo' => self::GRAY_MED,'unico' => self::RED];
            $tc = $typeColors[$client->client_type] ?? self::GRAY_MED;

            $data = [$client->full_name, $client->phone ?? '', $client->email ?? '',
                     $client->client_type_label, $client->department ?? '',
                     $client->acquisition_label, $client->created_at?->format('d/m/Y') ?? ''];

            foreach ($data as $i => $v) {
                $sheet->setCellValue("{$cols[$i]}{$row}", $v);
                $color = $i === 3 ? $tc : self::GRAY_DARK;
                $bold  = $i === 3;
                $align = $i === 3 ? 'center' : 'left';
                $sheet->getStyle("{$cols[$i]}{$row}")->applyFromArray($this->cellStyle($color, $bg, $bold, $align));
            }
            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;
        }

        if ($frecuentes->isEmpty()) {
            $sheet->mergeCells("A5:G5");
            $sheet->setCellValue('A5', 'Sin clientes frecuentes registrados');
            $sheet->getStyle('A5')->applyFromArray($this->cellStyle(self::GRAY_MED, self::GRAY_LIGHT, false, 'center'));
            $row = 6;
        }

        // Tabla ocasionales
        $row += 2;
        $this->titleRow($sheet, "A{$row}:G{$row}", 'CLIENTES OCASIONALES', self::BLUE, 11);
        $sheet->getRowDimension($row)->setRowHeight(28);
        $row++;

        foreach ($headers as $i => $h) {
            $sheet->setCellValue("{$cols[$i]}{$row}", $h);
            $sheet->getStyle("{$cols[$i]}{$row}")->applyFromArray($this->hStyle(self::BLUE, 9));
        }
        $sheet->getRowDimension($row)->setRowHeight(24);
        $row++;

        $ocasionales = $clients->where('client_mode', 'ocasional');
        foreach ($ocasionales as $idx => $client) {
            $bg = $idx % 2 === 0 ? self::BLUE_LIGHT : self::WHITE;
            $data = [$client->full_name, $client->phone ?? '', $client->email ?? '',
                     $client->client_type_label, $client->department ?? '',
                     $client->acquisition_label, $client->created_at?->format('d/m/Y') ?? ''];

            foreach ($data as $i => $v) {
                $sheet->setCellValue("{$cols[$i]}{$row}", $v);
                $sheet->getStyle("{$cols[$i]}{$row}")->applyFromArray($this->cellStyle(self::GRAY_DARK, $bg));
            }
            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;
        }

        if ($ocasionales->isEmpty()) {
            $sheet->mergeCells("A{$row}:G{$row}");
            $sheet->setCellValue("A{$row}", 'Sin clientes ocasionales registrados');
            $sheet->getStyle("A{$row}")->applyFromArray($this->cellStyle(self::GRAY_MED, self::GRAY_LIGHT, false, 'center'));
        }
    }
}