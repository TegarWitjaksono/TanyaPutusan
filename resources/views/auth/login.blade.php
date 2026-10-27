<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - TanyaPutusan AI Legal Intelligence</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS & FontAwesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            50: '#f0f5fa',
                            100: '#e1ecf5',
                            600: '#1d3e69',
                            700: '#142c4b',
                            800: '#0e1f35',
                            900: '#0a1524',
                            950: '#050a12',
                        },
                        gold: {
                            50: '#fbf8ed',
                            100: '#f6eed4',
                            200: '#ecdba9',
                            400: '#d8ab42',
                            500: '#b8860b',
                            600: '#996f08',
                            700: '#7a5705',
                        }
                    },
                    fontFamily: {
                        serif: ['Cinzel', 'Georgia', 'serif'],
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }
        .legal-gold-text {
            background: linear-gradient(135deg, #996f08 0%, #b8860b 50%, #7a5705 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .card-clean {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.08);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between bg-slate-50 selection:bg-gold-100 selection:text-gold-700">

    <!-- Top Navigation Simple -->
    <header class="w-full bg-white border-b border-slate-200 py-4 px-6 sm:px-10 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-navy-800 to-navy-950 flex items-center justify-center text-gold-400 text-lg border border-gold-500/30 shadow-sm">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div>
                <span class="font-serif tracking-wider text-lg font-bold text-navy-900">TANYA<span class="text-gold-600">PUTUSAN</span></span>
                <span class="text-[9px] font-semibold tracking-wider uppercase bg-slate-100 text-navy-700 px-1.5 py-0.5 rounded border border-slate-200 ml-1.5">Legal AI</span>
            </div>
        </a>

        <a href="{{ route('home') }}" class="text-xs font-semibold text-slate-600 hover:text-navy-900 flex items-center gap-1.5 transition">
            <i class="fa-solid fa-arrow-left text-[11px]"></i>
            <span>Kembali ke Beranda</span>
        </a>
    </header>

    <!-- Main Container -->
    <main class="flex-grow flex items-center justify-center p-4 sm:p-6 my-8">
        <div class="w-full max-w-md">
            
            <!-- Auth Card -->
            <div class="card-clean rounded-2xl p-7 sm:p-9 space-y-6">
                <!-- Card Header -->
                <div class="text-center space-y-2">
                    <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-br from-navy-900 to-navy-950 text-gold-400 items-center justify-center text-2xl shadow-md border border-gold-500/30 mx-auto">
                        <i class="fa-solid fa-brain"></i>
                    </div>
                    <h1 class="text-2xl font-serif font-bold text-navy-950 tracking-tight">
                        Masuk ke <span class="legal-gold-text">TanyaPutusan</span>
                    </h1>
                    <p class="text-xs text-slate-500 max-w-xs mx-auto">
                        Akses asisten AI riset hukum, rangkuman amar putusan, dan telaah yurisprudensi Mahkamah Agung.
                    </p>
                </div>

                <!-- Session Alert Messages -->
                @if(session('success'))
                    <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('info'))
                    <div class="p-3.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-xs flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-blue-600"></i>
                        <span>{{ session('info') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs space-y-1">
                        <div class="font-bold flex items-center gap-1.5">
                            <i class="fa-solid fa-triangle-exclamation text-red-600"></i>
                            <span>Gagal Masuk:</span>
                        </div>
                        <ul class="list-disc list-inside text-[11px] text-red-700">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Tombol Login dengan Google -->
                <a 
                    href="{{ route('auth.google', ['mode' => 'login']) }}" 
                    class="w-full py-2.5 px-4 rounded-xl border border-slate-300 hover:border-slate-400 bg-white hover:bg-slate-50/80 text-slate-700 font-semibold text-xs transition shadow-sm flex items-center justify-center gap-3 cursor-pointer group"
                >
                    <svg class="w-4 h-4" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    <span class="group-hover:text-navy-900">Masuk dengan Akun Google</span>
                </a>

                <!-- Divider pemisah -->
                <div class="relative flex items-center justify-center my-1">
                    <div class="border-t border-slate-200 w-full"></div>
                    <span class="bg-white px-3 text-[11px] font-medium text-slate-400 uppercase tracking-wider shrink-0">
                        atau email
                    </span>
                    <div class="border-t border-slate-200 w-full"></div>
                </div>

                <!-- Form Login -->
                <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Alamat Email
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                                <i class="fa-regular fa-envelope text-xs"></i>
                            </span>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                required 
                                autofocus
                                placeholder="nama@instansi.com" 
                                class="w-full bg-slate-50 border border-slate-300 rounded-xl py-2.5 pl-9 pr-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-navy-600 focus:bg-white focus:ring-2 focus:ring-navy-100 transition"
                            >
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-semibold text-slate-700">
                                Kata Sandi
                            </label>
                            <a href="#" class="text-[11px] font-medium text-gold-700 hover:text-gold-800 transition">
                                Lupa sandi?
                            </a>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                                <i class="fa-solid fa-lock text-xs"></i>
                            </span>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required 
                                placeholder="••••••••" 
                                class="w-full bg-slate-50 border border-slate-300 rounded-xl py-2.5 pl-9 pr-10 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-navy-600 focus:bg-white focus:ring-2 focus:ring-navy-100 transition"
                            >
                            <button 
                                type="button" 
                                onclick="togglePasswordVisibility('password', this)" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600"
                            >
                                <i class="fa-regular fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-navy-900 focus:ring-navy-900">
                            <span class="text-slate-600">Ingat saya di perangkat ini</span>
                        </label>
                    </div>

                    <button 
                        type="submit" 
                        class="w-full py-3 rounded-xl bg-navy-900 hover:bg-navy-800 text-white font-semibold text-xs tracking-wider transition shadow-md shadow-navy-950/10 flex items-center justify-center gap-2 cursor-pointer mt-2"
                    >
                        <i class="fa-solid fa-right-to-bracket text-gold-400"></i>
                        <span>Masuk Sekarang</span>
                    </button>
                </form>

                <!-- Divider & Register Link -->
                <div class="border-t border-slate-100 pt-5 text-center text-xs text-slate-500">
                    <span>Belum memiliki akun?</span>
                    <a href="{{ route('register') }}" class="font-bold text-gold-700 hover:text-gold-800 ml-1 transition">
                        Daftar Gratis di sini
                    </a>
                </div>
            </div>

            <!-- Trust Badge -->
            <div class="text-center text-[11px] text-slate-400 mt-6 flex items-center justify-center gap-2">
                <i class="fa-solid fa-shield-halved text-gold-600"></i>
                <span>Terkoneksi aman dengan direktori putusan resmi Mahkamah Agung RI</span>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full bg-white border-t border-slate-200 py-4 px-6 text-center text-xs text-slate-400">
        &copy; 2026 TanyaPutusan Legal Intelligence &bull; Platform Riset Hukum Indonesia
    </footer>

    <script>
        function togglePasswordVisibility(fieldId, btn) {
            const input = document.getElementById(fieldId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fa-regular fa-eye-slash text-xs';
            } else {
                input.type = 'password';
                icon.className = 'fa-regular fa-eye text-xs';
            }
        }
    </script>
</body>
</html>
