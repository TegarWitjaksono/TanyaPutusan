<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis & Tren Putusan Mahkamah Agung 2026 - TanyaPutusan</title>
    
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
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
        }
        .card-clean:hover {
            border-color: #cbd5e1;
            box-shadow: 0 10px 25px -3px rgba(15, 23, 42, 0.08);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-slate-50 selection:bg-gold-100 selection:text-gold-700">

    <!-- Top Legal Navigation Bar -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-navy-800 to-navy-950 flex items-center justify-center shadow-md shadow-navy-900/10 text-gold-400 text-lg border border-gold-500/30">
                    <i class="fa-solid fa-scale-balanced"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-serif tracking-wider text-xl font-bold text-navy-900">TANYA<span class="text-gold-600">PUTUSAN</span></span>
                        <span class="text-[10px] font-semibold tracking-wider uppercase bg-slate-100 text-navy-700 px-2 py-0.5 rounded border border-slate-200">Legal Intelligence</span>
                    </div>
                    <p class="text-xs text-slate-500">Pusat Telaah & Riset Putusan Peradilan Indonesia</p>
                </div>
            </a>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center gap-7 text-sm font-medium text-slate-600">
                <a href="{{ route('home') }}" class="hover:text-navy-900 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-book-bookmark text-slate-400 text-xs"></i> Direktori Putusan
                </a>
                <a href="{{ route('yurisprudensi') }}" class="hover:text-navy-900 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-landmark text-slate-400 text-xs"></i> Yurisprudensi
                </a>
                <a href="{{ route('tren') }}" class="text-navy-900 font-semibold border-b-2 border-gold-500 pb-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-chart-line text-gold-600 text-xs"></i> Tren Putusan 2026
                </a>
            </nav>

            <!-- Action Buttons: Tanya AI & Status Login -->
            <div class="flex items-center gap-3">
                <a 
                    href="{{ route('home') }}?open_ai=1" 
                    id="btn-tanya-ai"
                    class="px-4 py-2 rounded-lg bg-navy-900 text-white hover:bg-navy-800 text-xs font-semibold tracking-wide transition shadow-sm flex items-center gap-2 cursor-pointer"
                >
                    <i class="fa-solid fa-brain text-gold-400 text-xs"></i>
                    <span>Tanya AI Hukum</span>
                </a>

                @auth
                    <!-- User Profile Dropdown / Logged in indicator -->
                    <div class="relative flex items-center gap-2 pl-2 border-l border-slate-200">
                        <div class="w-8 h-8 rounded-full bg-gold-100 text-gold-900 flex items-center justify-center text-xs font-bold font-serif border border-gold-300">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden sm:block text-left">
                            <span class="block text-xs font-bold text-navy-900 leading-tight">{{ Str::limit(Auth::user()->name, 14) }}</span>
                            <span class="block text-[10px] text-emerald-600 font-medium">Terverifikasi</span>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="w-7 h-7 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 flex items-center justify-center transition" title="Keluar">
                                <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="px-3.5 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition border border-slate-200 flex items-center gap-1.5">
                        <i class="fa-regular fa-user text-xs text-slate-500"></i>
                        <span>Masuk</span>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        <!-- Hero Section -->
        <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-8 sm:p-10 shadow-sm">
            <div class="absolute -right-8 -bottom-8 opacity-[0.03] pointer-events-none text-navy-950 text-[260px]">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            
            <div class="relative z-10 max-w-3xl space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold">
                        <i class="fa-solid fa-chart-pie text-blue-600"></i>
                        <span>Statistik & Tren Peradilan Mahkamah Agung RI</span>
                    </div>

                    <!-- Dropdown Pilihan Tahun Ringkas di Hero -->
                    <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 shadow-sm">
                        <span class="text-xs font-semibold text-slate-500">Pilih Tahun:</span>
                        <select 
                            onchange="window.location.href='{{ route('tren') }}?tahun=' + this.value"
                            class="text-xs font-bold text-navy-900 bg-transparent focus:outline-none cursor-pointer"
                        >
                            @foreach($availableYears ?? [2026, 2025, 2024, 2023, 2022, 2021, 2020] as $yr)
                                <option value="{{ $yr }}" {{ ($tahunAktif ?? 2026) == $yr ? 'selected' : '' }}>
                                    Tahun {{ $yr }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <h1 class="text-3xl sm:text-4xl font-serif font-bold text-navy-900 tracking-tight leading-snug">
                    Pemetaan Pola & <span class="legal-gold-text">Tren Perkara Hukum {{ $tahunAktif ?? 2026 }}</span>
                </h1>
                <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                    Analisis distribusi perkara berdasarkan kamar pengadilan, jenis tindak pidana terbanyak, sengketa perdata dominan, hingga rasio putusan yang dikabulkan maupun ditolak.
                </p>
            </div>
        </div>

        <!-- 3 Metrics Top -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="card-clean p-5 rounded-xl border-t-2 border-t-navy-900">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider">Total Sampel Perkara</span>
                    <i class="fa-solid fa-folder-closed text-navy-700"></i>
                </div>
                <div class="text-2xl font-bold font-serif text-navy-950">{{ number_format($totalPerkara) }} Perkara</div>
                <p class="text-xs text-slate-500 mt-1">Data direktori MA skala nasional</p>
            </div>

            <div class="card-clean p-5 rounded-xl border-t-2 border-t-blue-600">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider">Kamar Paling Aktif</span>
                    <i class="fa-solid fa-fire text-red-500"></i>
                </div>
                <div class="text-2xl font-bold font-serif text-navy-950">Perdata Agama</div>
                <p class="text-xs text-slate-500 mt-1">7,6 Juta perkara terindeks</p>
            </div>

            <div class="card-clean p-5 rounded-xl border-t-2 border-t-gold-500">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider">Tahun Pantauan</span>
                    <i class="fa-regular fa-calendar-check text-gold-600"></i>
                </div>
                <div class="flex items-center gap-2">
                    <select 
                        onchange="window.location.href='{{ route('tren') }}?tahun=' + this.value"
                        class="text-sm font-semibold text-navy-950 bg-slate-50 hover:bg-slate-100 border border-slate-300 rounded-lg px-2.5 py-1 focus:outline-none focus:border-navy-600 cursor-pointer shadow-sm transition"
                    >
                        @foreach($availableYears ?? [2026, 2025, 2024, 2023, 2022, 2021, 2020] as $yr)
                            <option value="{{ $yr }}" {{ ($tahunAktif ?? 2026) == $yr ? 'selected' : '' }}>
                                Tahun {{ $yr }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <p class="text-xs text-slate-500 mt-2">Filter pantauan tahun aktif</p>
            </div>
        </div>

        <!-- Visual Distribution of Chambers (Progress / Bar Charts) -->
        <div class="card-clean rounded-2xl p-7 space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-slate-200">
                <div>
                    <h2 class="text-lg font-serif font-bold text-navy-950">Distribusi Perkara Berdasarkan Kamar Pengadilan</h2>
                    <p class="text-xs text-slate-500">Proporsi perkara tahun {{ $tahunAktif ?? 2026 }} yang tercatat pada direktori Mahkamah Agung</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-gold-700 bg-gold-50 px-3 py-1.5 rounded-lg border border-gold-200">
                        Pantauan: Tahun {{ $tahunAktif ?? 2026 }}
                    </span>
                </div>
            </div>

            <div class="space-y-5">
                @php
                    $total = max(1, $totalPerkara);
                    $colors = [
                        'Perdata Agama' => 'bg-purple-600',
                        'Perdata Umum' => 'bg-amber-500',
                        'Pidana Umum' => 'bg-indigo-600',
                        'Pidana Khusus' => 'bg-blue-600',
                        'Tata Usaha Negara' => 'bg-emerald-600',
                        'Perdata Khusus' => 'bg-rose-500',
                        'Pidana Militer' => 'bg-stone-700',
                        'Pajak' => 'bg-cyan-600',
                    ];
                @endphp

                @foreach($kamarCounts as $kamarName => $count)
                    @php
                        $percentage = round(($count / $total) * 100, 1);
                        $barColor = $colors[$kamarName] ?? 'bg-navy-700';
                    @endphp
                    <div class="space-y-1.5">
                        <div class="flex flex-wrap justify-between text-xs font-medium gap-2">
                            <span class="text-slate-800 flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full {{ $barColor }} inline-block"></span>
                                <span>Kamar {{ $kamarName }}</span>
                            </span>
                            <span class="font-bold text-navy-900">{{ number_format($count) }} Perkara ({{ $percentage }}%)</span>
                        </div>
                        <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full {{ $barColor }} rounded-full transition-all duration-500" style="width: {{ max(1, $percentage) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 3 Insight Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="card-clean rounded-xl p-6 space-y-3 border-l-4 border-l-navy-900">
                <h3 class="text-base font-bold font-serif text-navy-950 flex items-center gap-2">
                    <i class="fa-solid fa-lightbulb text-gold-600"></i>
                    Kesimpulan Analisis Perkara 2026
                </h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Perkara tindak pidana khusus (terutama peninjauan kembali kasus narkotika dan tipikor) menempati porsi terbesar dari putusan yang diunggah sepanjang 2026. Di ranah perdata, sengketa perbuatan melawan hukum (PMH) atas kepemilikan tanah tetap menjadi porsi terbesar sengketa antar-pihak.
                </p>
            </div>

            <div class="card-clean rounded-xl p-6 space-y-3 border-l-4 border-l-gold-500">
                <h3 class="text-base font-bold font-serif text-navy-950 flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-gold-600"></i>
                    Akses Berkas Salinan PDF
                </h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Seluruh dokumen perkara yang diindeks dalam sistem ini telah dilengkapi tautan salinan berkas resmi Mahkamah Agung RI sehingga memudahkan praktisi hukum dan akademisi untuk melakukan verifikasi pertimbangan majelis hakim.
                </p>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="mt-16 border-t border-slate-200 bg-white text-slate-500 text-xs py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-scale-balanced text-gold-600 text-base"></i>
                <span class="font-serif text-navy-900 font-bold tracking-wider">TANYAPUTUSAN</span>
                <span>&bull; Analisis & Intelijen Hukum Terbuka Mahkamah Agung RI</span>
            </div>
        </div>
    </footer>

</body>
</html>
