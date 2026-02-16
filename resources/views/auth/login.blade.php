<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynasty Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        @keyframes pulse-slow {
            0%, 100% {
                opacity: 0.3;
            }
            50% {
                opacity: 0.1;
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        .animate-fadeIn {
            animation: fadeIn 1s ease-out forwards;
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animate-pulse-slow {
            animation: pulse-slow 4s ease-in-out infinite;
        }

        .animate-slideInLeft {
            animation: slideInLeft 0.8s ease-out forwards;
        }

        .delay-100 {
            animation-delay: 0.1s;
            opacity: 0;
        }

        .delay-200 {
            animation-delay: 0.2s;
            opacity: 0;
        }

        .delay-300 {
            animation-delay: 0.3s;
            opacity: 0;
        }

        .delay-400 {
            animation-delay: 0.4s;
            opacity: 0;
        }

        .delay-500 {
            animation-delay: 0.5s;
            opacity: 0;
        }

        .input-focus-effect {
            transition: all 0.3s ease;
        }

        .input-focus-effect:focus {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(192, 38, 211, 0.2);
        }

        .btn-hover {
            transition: all 0.3s ease;
        }

        .btn-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(192, 38, 211, 0.4);
        }

        .logo-glow {
            filter: drop-shadow(0 0 20px rgba(255, 255, 255, 0.3));
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-fuchsia-600 via-fuchsia-500 to-pink-400 p-4">
    
<div class="w-full max-w-4xl min-h-[520px] flex rounded-3xl overflow-hidden shadow-2xl bg-white animate-fadeIn">

        
        <!-- Left Side - Dynasty Gradient Panel -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-fuchsia-600 via-fuchsia-500 to-fuchsia-600 items-center justify-center p-12 relative overflow-hidden">
            <!-- Decorative circles with animation -->
            <div class="absolute top-0 left-0 w-96 h-96 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2 animate-pulse-slow"></div>
            <div class="absolute bottom-0 right-0 w-80 h-80 bg-white/5 rounded-full translate-x-1/3 translate-y-1/3 animate-pulse-slow" style="animation-delay: 2s;"></div>
            <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-white/3 rounded-full -translate-x-1/2 -translate-y-1/2 animate-pulse-slow" style="animation-delay: 1s;"></div>
            
            <div class="relative z-10 text-center">
                <!-- Dynasty Logo Grande con animación -->
                <div class="mb-8 flex justify-center animate-float">
                  <div class="bg-white/10 backdrop-blur-sm rounded-full p-8 logo-glow">
                    <img src="{{ asset('images/logo-dynasty.png') }}" alt="Dynasty Logo" class="w-48 h-48 object-contain">
                  </div>
                </div>
                
                <h2 class="text-3xl font-bold text-white mb-3 animate-fadeInUp delay-100">Dynasty</h2>
                <p class="text-xl text-white/90 font-light tracking-wider animate-fadeInUp delay-200">Studio & Spa</p>
                <p class="text-lg text-white/70 mt-2 animate-fadeInUp delay-300">Lima, Perú</p>
                
                <!-- Decorative line -->
                <div class="mt-8 flex items-center justify-center gap-3 animate-fadeInUp delay-400">
                    <div class="w-12 h-0.5 bg-white/30"></div>
                    <div class="w-2 h-2 rounded-full bg-white/50"></div>
                    <div class="w-12 h-0.5 bg-white/30"></div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
     <div class="w-full lg:w-1/2 p-6 lg:p-8 flex flex-col justify-center">

            
            <!-- Logo Icon for form (Más grande y animado) -->
         <div class="mb-6 flex justify-center animate-slideInLeft">
    <img 
        src="{{ asset('images/logo-dynasty-1.png') }}" 
        alt="Dynasty Logo"
        class="w-28 h-28 object-contain"
    >
</div>


            <h1 class="text-4xl font-bold bg-gradient-to-r from-fuchsia-600 to-pink-500 bg-clip-text text-transparent mb-3 text-center lg:text-left animate-fadeInUp delay-100">
                Bienvenido
            </h1>
            <p class="text-gray-500 mb-8 text-center lg:text-left animate-fadeInUp delay-200">
                Ingresa tus credenciales para continuar
            </p>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg animate-fadeInUp">
                    <p class="text-red-700 text-sm">{{ $errors->first() }}</p>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf

                <!-- Email Input -->
                <div class="relative animate-fadeInUp delay-300">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <input 
                            type="email" 
                            name="email" 
                            placeholder="tu@email.com" 
                            required
                            class="w-full pl-12 pr-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-fuchsia-500 focus:ring-2 focus:ring-fuchsia-200 outline-none input-focus-effect text-gray-700 placeholder-gray-400 bg-gray-50 focus:bg-white"
                        >
                    </div>
                </div>

                <!-- Password Input -->
                <div class="relative animate-fadeInUp delay-400">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input 
                            type="password" 
                            name="password" 
                            placeholder="••••••••" 
                            required
                            class="w-full pl-12 pr-4 py-3.5 border-2 border-gray-200 rounded-xl focus:border-fuchsia-500 focus:ring-2 focus:ring-fuchsia-200 outline-none input-focus-effect text-gray-700 placeholder-gray-400 bg-gray-50 focus:bg-white"
                        >
                    </div>
                </div>

                <!-- Remember & Forgot Password -->
                <div class="flex items-center justify-between animate-fadeInUp delay-500">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" class="w-4 h-4 text-fuchsia-600 border-gray-300 rounded focus:ring-fuchsia-500">
                        <span class="ml-2 text-sm text-gray-600">Recordarme</span>
                    </label>
                    <a href="#" class="text-sm text-fuchsia-600 hover:text-fuchsia-700 font-medium transition-colors duration-300">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>

                <!-- Submit Button -->
                <div class="pt-2 animate-fadeInUp delay-500">
                    <button 
                        type="submit"
                        class="w-full py-3 bg-gradient-to-r from-fuchsia-600 via-fuchsia-500 to-pink-500 text-white font-semibold rounded-xl btn-hover shadow-lg"
                    >
                        <span class="flex items-center justify-center gap-2">
                            INGRESAR
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </span>
                    </button>
                </div>
            </form>

     
        </div>
    </div>

</body>
</html>