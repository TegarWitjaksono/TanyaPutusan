<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TanyaPutusan - Portal Riset & Direktori Putusan Mahkamah Agung RI</title>
    
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
            <!-- Brand & Official Logo -->
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
                <a href="{{ route('home') }}" class="text-navy-900 font-semibold border-b-2 border-gold-500 pb-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-book-bookmark text-gold-600 text-xs"></i> Direktori Putusan
                </a>
                <a href="{{ route('yurisprudensi') }}" class="hover:text-navy-900 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-landmark text-slate-400 text-xs"></i> Yurisprudensi
                </a>
                <a href="{{ route('tren') }}" class="hover:text-navy-900 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-chart-line text-slate-400 text-xs"></i> Tren Putusan 2026
                </a>
            </nav>

            <!-- Action Buttons: Tanya AI & Status Login -->
            <div class="flex items-center gap-3">
                <button 
                    onclick="handleTanyaAiClick()" 
                    id="btn-tanya-ai"
                    class="px-4 py-2 rounded-lg bg-navy-900 text-white hover:bg-navy-800 text-xs font-semibold tracking-wide transition shadow-sm flex items-center gap-2 cursor-pointer"
                >
                    <i class="fa-solid fa-brain text-gold-400 text-xs"></i>
                    <span>Tanya AI Hukum</span>
                </button>

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
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            
            <div class="relative z-10 max-w-3xl space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-navy-50 border border-navy-100 text-navy-700 text-xs font-semibold">
                    <i class="fa-solid fa-shield-halved text-gold-600"></i>
                    <span>Basis Data Terbuka Mahkamah Agung Republik Indonesia</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-serif font-bold text-navy-900 tracking-tight leading-snug">
                    Telusuri & Bedah <span class="legal-gold-text">Putusan Pengadilan</span> dengan Cepat dan Akurat
                </h1>
                <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                    Akses komprehensif ribuan amar putusan, pertimbangan hakim, dan berkas salinan resmi dari seluruh lingkungan peradilan (Pidana Umum, Pidana Khusus, Perdata, TUN, Agama, Pajak, Militer).
                </p>

                <!-- Search / Filter Form -->
                <form action="{{ route('home') }}" method="GET" class="pt-2 flex flex-col sm:flex-row gap-2 max-w-2xl">
                    <input type="hidden" name="tahun" value="{{ $tahunAktif ?? 2026 }}">
                    <input type="hidden" name="kategori" value="{{ $kategoriAktif ?? 'semua' }}">
                    <div class="relative flex-grow">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fa-solid fa-magnifying-glass text-sm"></i>
                        </span>
                        <input 
                            type="text" 
                            name="q"
                            value="{{ $search ?? '' }}"
                            placeholder="Cari berdasarkan nomor putusan, kata kunci, pihak, atau pengadilan..." 
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl py-3 pl-10 pr-4 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-navy-600 focus:bg-white focus:ring-2 focus:ring-navy-100 transition"
                        >
                    </div>
                    <button type="submit" class="px-6 py-3 bg-navy-900 text-white hover:bg-navy-800 rounded-xl text-sm font-semibold tracking-wide transition shadow-sm flex items-center justify-center gap-2">
                        <span>Cari Putusan</span>
                        <i class="fa-solid fa-arrow-right text-xs text-gold-400"></i>
                    </button>
                    @if(!empty($search))
                        <a href="{{ route('home', ['tahun' => $tahunAktif ?? 2026, 'kategori' => $kategoriAktif ?? 'semua']) }}" class="px-4 py-3 bg-slate-200 text-slate-700 hover:bg-slate-300 rounded-xl text-sm font-semibold transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Metric Badges: Real-time Stats Skala Direktori MA (2 Kolom Bersih & Simetris) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="card-clean p-5 rounded-xl border-l-4 border-l-emerald-500 flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-2 text-slate-500 mb-1">
                        <span class="text-xs font-semibold uppercase tracking-wider">Total Direktori MA</span>
                    </div>
                    <div id="stat-total" class="text-2xl font-bold font-serif text-navy-950">{{ $totalData ?? '11.450.852' }}</div>
                    <p class="text-xs text-emerald-600 font-medium mt-1 flex items-center gap-1.5">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span>Live Stream Portal MA &bull; Real-time</span>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-server"></i>
                </div>
            </div>

            <div class="card-clean p-5 rounded-xl border-l-4 border-l-gold-500 flex items-center justify-between">
                <div class="min-w-0 pr-3">
                    <div class="flex items-center gap-2 text-slate-500 mb-1">
                        <span class="text-xs font-semibold uppercase tracking-wider">Klasifikasi / Lembaga Aktif</span>
                    </div>
                    <div class="text-xl font-bold font-serif text-navy-950 truncate" title="{{ !empty($peradilanAktif) && $peradilanAktif !== 'semua' ? ($peradilanList[$peradilanAktif] ?? $peradilanAktif) : ($categories[$kategoriAktif ?? 'semua'] ?? 'Semua Klasifikasi') }}">
                        @if(!empty($peradilanAktif) && $peradilanAktif !== 'semua')
                            {{ $peradilanList[$peradilanAktif] ?? $peradilanAktif }}
                        @else
                            {{ $categories[$kategoriAktif ?? 'semua'] ?? 'Semua Klasifikasi (11,4 Juta)' }}
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Halaman {{ $currentPage ?? 1 }} dari {{ number_format($pagination['max_page'] ?? 1) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gold-50 border border-gold-200 text-gold-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-scale-balanced"></i>
                </div>
            </div>
        </div>

        <!-- Layout 2 Kolom: Sidebar Direktori MA (Kiri) & Konten Putusan (Kanan) -->
        <div id="direktori" class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">

            <!-- SIDEBAR FILTER MA (Dinamis: Direktori, Klasifikasi, Pengadilan, Tahun) -->
            <aside class="lg:col-span-1 space-y-4">
                @foreach($sidebarFilters ?? [] as $cardIndex => $card)
                        <div class="card-clean rounded-xl overflow-hidden border border-slate-200/90 shadow-sm bg-white">
                            <!-- Card Header (Collapsible) -->
                            <button 
                                type="button"
                                onclick="toggleSidebarCard(this)"
                                class="w-full px-4 py-3 bg-gradient-to-r from-slate-50 to-slate-100 hover:bg-slate-100/80 border-b border-slate-200 flex items-center justify-between text-left transition font-semibold text-xs text-navy-950 uppercase tracking-wider"
                            >
                                <span class="flex items-center gap-2">
                                    @if(stripos($card['title'] ?? '', 'Direktori') !== false)
                                        <i class="fa-solid fa-folder-tree text-navy-700"></i>
                                    @elseif(stripos($card['title'] ?? '', 'Klasifikasi') !== false)
                                        <i class="fa-solid fa-tags text-emerald-600"></i>
                                    @elseif(stripos($card['title'] ?? '', 'Pengadilan') !== false || stripos($card['title'] ?? '', 'Peradilan') !== false)
                                        <i class="fa-solid fa-landmark text-gold-600"></i>
                                    @elseif(stripos($card['title'] ?? '', 'Tahun') !== false)
                                        <i class="fa-regular fa-calendar-days text-blue-600"></i>
                                    @else
                                        <i class="fa-solid fa-filter text-slate-500"></i>
                                    @endif
                                    <span>{{ $card['title'] ?? 'Filter' }}</span>
                                </span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200"></i>
                            </button>

                            @if(!empty($card['is_table']) && !empty($card['columns']))
                                <!-- Tampilan Khusus Card Tahun: Tabel 3 Kolom Responsif (Putus, Register, Upload) -->
                                <div class="p-2 overflow-x-auto text-xs">
                                    <div class="grid grid-cols-3 gap-1.5 text-center font-bold text-[11px] text-navy-900 border-b border-slate-200 pb-2 mb-2 bg-slate-50/80 py-1 rounded">
                                        @foreach($card['columns'] as $col)
                                            <div class="uppercase tracking-wider {{ ($periodeAktif ?? 'putus') === strtolower($col['name']) ? 'text-emerald-700 font-extrabold' : 'text-slate-600' }}">
                                                {{ $col['name'] }}
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="grid grid-cols-3 gap-1.5 items-start">
                                        @foreach($card['columns'] as $col)
                                            <div class="space-y-1">
                                                @foreach($col['items'] ?? [] as $tItem)
                                                    @php
                                                        $isYearActive = ($tahunAktif ?? 0) == $tItem['tahun'] && ($periodeAktif ?? 'putus') === strtolower($col['periode'] ?? '');
                                                        $targetKategori = $tItem['kategori'] ?? ($kategoriAktif ?? 'semua');
                                                        $targetPeradilan = $tItem['peradilan'] ?? ($peradilanAktif ?? '');
                                                    @endphp
                                                    <a 
                                                        href="{{ route('home', ['tahun' => $tItem['tahun'], 'kategori' => $targetKategori, 'peradilan' => $targetPeradilan, 'periode' => $tItem['periode'] ?? 'putus', 'q' => $search ?? '', 'page' => 1]) }}"
                                                        class="block p-1.5 rounded-lg border text-center transition group {{ $isYearActive ? 'bg-emerald-600 text-white font-bold border-emerald-600 shadow-xs' : 'bg-slate-50 hover:bg-slate-100 border-slate-200/90 text-slate-800' }}"
                                                        title="Tahun {{ $tItem['label'] }} ({{ $tItem['count'] }} putusan)"
                                                    >
                                                        <div class="font-bold text-[11px] {{ $isYearActive ? 'text-white' : 'text-navy-950' }}">{{ $tItem['label'] }}</div>
                                                        @if(!empty($tItem['count']))
                                                            <div class="text-[9px] truncate font-mono {{ $isYearActive ? 'text-emerald-100' : 'text-slate-500 group-hover:text-slate-700' }}">{{ $tItem['count'] }}</div>
                                                        @endif
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <!-- Card Body Standar List Items -->
                                <div class="p-2 space-y-1 max-h-80 overflow-y-auto text-xs">
                                    @foreach($card['items'] ?? [] as $item)
                                        @php
                                            // Tentukan apakah item ini aktif
                                            $isItemActive = !empty($item['is_active']);
                                            if (!$isItemActive) {
                                                if (!empty($item['kategori']) && ($kategoriAktif ?? '') === $item['kategori']) {
                                                    if (empty($item['peradilan']) || ($peradilanAktif ?? '') === $item['peradilan']) {
                                                        $isItemActive = true;
                                                    }
                                                }
                                                if (!empty($item['peradilan']) && ($peradilanAktif ?? '') === $item['peradilan']) {
                                                    $isItemActive = true;
                                                }
                                            }

                                            // URL tujuan
                                            $targetKategori = $item['kategori'] ?? ($kategoriAktif ?? 'semua');
                                            $targetPeradilan = $item['peradilan'] ?? ($peradilanAktif ?? '');
                                            $targetTahun = !empty($item['tahun']) ? $item['tahun'] : ($tahunAktif ?? 0);
                                            $targetPeriode = !empty($item['periode']) ? $item['periode'] : ($periodeAktif ?? 'putus');
                                        @endphp
                                        <a 
                                            href="{{ route('home', ['tahun' => $targetTahun, 'kategori' => $targetKategori, 'peradilan' => $targetPeradilan, 'periode' => $targetPeriode, 'q' => $search ?? '', 'page' => 1]) }}"
                                            class="flex items-center justify-between px-3 py-2 rounded-lg transition group {{ $isItemActive ? 'bg-emerald-50 text-emerald-900 font-bold border border-emerald-200 shadow-xs' : 'hover:bg-slate-100 text-slate-700' }}"
                                            title="{{ $item['label'] ?? '' }}"
                                        >
                                            <span class="truncate pr-2 {{ $isItemActive ? 'text-emerald-950 font-bold' : 'group-hover:text-navy-950' }}">
                                                {{ $item['label'] ?? '' }}
                                            </span>
                                            @if(!empty($item['count']))
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold shrink-0 {{ $isItemActive ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-200/80 text-slate-600 group-hover:bg-slate-300' }}">
                                                    {{ $item['count'] }}
                                                </span>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
            </aside>


            <!-- AREA UTAMA: DAFTAR DOKUMEN PUTUSAN & PAGINASI (Kanan) -->
            <main class="lg:col-span-3 space-y-5">
                <!-- Info Status Bar Direktori Aktif -->
                <div class="card-clean px-5 py-3.5 rounded-xl flex flex-wrap items-center justify-between gap-3 bg-white border border-slate-200 shadow-xs">
                    <div class="flex items-center gap-2 flex-wrap text-xs">
                        <span class="font-bold text-navy-900 flex items-center gap-1.5 uppercase tracking-wide">
                            <i class="fa-solid fa-scale-balanced text-gold-600"></i>
                            {{ !empty($peradilanAktif) && $peradilanAktif !== 'semua' ? ($peradilanList[$peradilanAktif] ?? $peradilanAktif) : ($categories[$kategoriAktif ?? 'semua'] ?? 'Semua Klasifikasi') }}
                        </span>
                        @if(($tahunAktif ?? 0) > 0)
                            <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-semibold border border-blue-200">
                                Tahun {{ $tahunAktif }}
                            </span>
                        @endif
                        @if(!empty($search))
                            <span class="px-2 py-0.5 rounded bg-gold-50 text-gold-800 font-semibold border border-gold-200">
                                Kata Kunci: "{{ $search }}"
                            </span>
                        @endif
                    </div>
                    <div class="text-xs text-slate-500 font-medium">
                        Menampilkan <strong>{{ count($putusanList ?? []) }}</strong> putusan di halaman ini
                    </div>
                </div>

                <!-- Daftar Dokumen Putusan (Looping Data Asli Scraper) -->
                <div class="space-y-4" id="putusan-container">
                    @forelse($putusanList ?? [] as $item)
                        <div class="card-clean rounded-xl p-6 transition-all space-y-4 border-l-4 {{ str_contains($item['klasifikasi'] ?? '', 'Pidana') ? 'border-l-blue-600' : (str_contains($item['klasifikasi'] ?? '', 'Perdata') ? 'border-l-gold-500' : 'border-l-navy-600') }}">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    @if(!empty($item['pengadilan']))
                                        <span class="px-2.5 py-1 rounded text-xs font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                                            {{ $item['pengadilan'] }}
                                        </span>
                                    @endif
                                    @if(!empty($item['klasifikasi']))
                                        <span class="px-2.5 py-1 rounded text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            {{ $item['klasifikasi'] }}
                                        </span>
                                    @endif
                                </div>
                                <span class="text-xs text-slate-500 flex items-center gap-1.5 font-mono">
                                    <i class="fa-regular fa-calendar-check text-slate-400"></i> Tanggal Putus: <strong>{{ !empty($item['tgl_putus']) ? $item['tgl_putus'] : '2026' }}</strong>
                                </span>
                            </div>

                            <div>
                                <h3 class="text-lg font-serif font-bold text-navy-950 hover:text-navy-700 cursor-pointer transition">
                                    <a href="javascript:void(0)" onclick="openPratinjau('{{ $item['url'] ?? '' }}', '{{ addslashes($item['judul'] ?? 'Putusan Mahkamah Agung') }}')">
                                        {{ $item['judul'] ?? 'Putusan Perkara Mahkamah Agung' }}
                                    </a>
                                </h3>
                                <p class="text-xs font-medium text-slate-500 mt-1 flex items-center gap-1.5">
                                    <i class="fa-solid fa-landmark text-gold-600 text-xs"></i>
                                    <span>{{ $item['pengadilan'] ?? 'Mahkamah Agung RI' }} &bull; Direktori Putusan Resmi</span>
                                </p>
                            </div>

                            @if(!empty($item['ringkasan']))
                                <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200 text-slate-700 text-xs leading-relaxed font-serif italic">
                                    "{{ $item['ringkasan'] }}"
                                </div>
                            @endif

                            <div class="pt-2 flex flex-wrap items-center justify-between gap-4 border-t border-slate-100 text-xs">
                                <div class="flex flex-wrap items-center gap-6 text-slate-600">
                                    @if(!empty($item['tgl_register']))
                                        <div>
                                            <span class="text-slate-400">Register:</span> 
                                            <span class="font-medium text-slate-800 ml-1">{{ $item['tgl_register'] }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($item['tgl_upload']))
                                        <div>
                                            <span class="text-slate-400">Upload:</span> 
                                            <span class="font-medium text-slate-800 ml-1">{{ $item['tgl_upload'] }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 flex-wrap">
                                    <button 
                                        type="button"
                                        onclick="downloadPDF('{{ $item['url'] ?? '' }}', '{{ addslashes($item['judul'] ?? 'Putusan Mahkamah Agung') }}', this)"
                                        class="px-3.5 py-2 rounded-lg bg-gradient-to-r from-amber-500 to-gold-500 hover:from-amber-600 hover:to-gold-600 text-navy-950 font-bold transition flex items-center gap-1.5 shadow-xs text-xs cursor-pointer group"
                                        title="Download PDF Salinan Resmi Perkara (Otomatis Dicatat ke Firebase)" >
                                        <i class="fa-solid fa-download"></i> <span>Download PDF</span>
                                    </button>
                                    <button 
                                        type="button" 
                                        onclick="openAiBedahPutusan('{{ $item['url'] ?? '' }}', '{{ addslashes($item['judul'] ?? 'Putusan Mahkamah Agung') }}')" 
                                        class="px-3.5 py-2 rounded-lg bg-gradient-to-r from-amber-500 to-gold-500 hover:from-amber-600 hover:to-gold-600 text-navy-950 font-bold transition flex items-center gap-1.5 shadow-xs text-xs cursor-pointer group"
                                        title="Bedah pertimbangan hukum, amar, dan yurisprudensi perkara ini bersama AI"
                                    >
                                        <i class="fa-solid fa-wand-magic-sparkles text-navy-950 text-xs transition-transform group-hover:scale-110"></i>
                                        <span>Bedah AI</span>
                                    </button>
                                    <button 
                                        type="button" 
                                        onclick="openPratinjau('{{ $item['url'] ?? '' }}', '{{ addslashes($item['judul'] ?? 'Putusan Mahkamah Agung') }}')" 
                                        class="px-3.5 py-2 rounded-lg bg-navy-900 hover:bg-navy-800 text-white font-semibold transition flex items-center gap-1.5 shadow-sm text-xs"
                                        title="Buka pratinjau telaah perkara dan salinan berkas"
                                    >
                                        <i class="fa-solid fa-eye text-gold-400 text-xs"></i>
                                        <span>Pratinjau</span>
                                    </button>
                                    <a href="{{ $item['url'] ?? '#' }}" target="_blank" class="px-3 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition flex items-center gap-1 text-xs">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-500"></i> Portal MA
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="card-clean rounded-xl p-12 text-center space-y-3">
                            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-xl">
                                <i class="fa-solid fa-folder-open"></i>
                            </div>
                            <h3 class="text-base font-bold text-navy-900">Tidak Ada Perkara Ditemukan</h3>
                            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                                Coba gunakan kata kunci pencarian yang lebih umum atau pilih klasifikasi perkara lainnya dari sidebar.
                            </p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination Live Navigator (Zero Disk, Real-time Mahkamah Agung) -->
                @php
                    $curr = $pagination['current_page'] ?? 1;
                    $maxP = $pagination['max_page'] ?? 1;
                    $hasPrev = $pagination['has_prev'] ?? false;
                    $hasNext = $pagination['has_next'] ?? false;
                @endphp
                <div class="card-clean p-4 rounded-xl flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-xs text-slate-600 font-medium">
                        <span>Halaman <strong class="text-navy-900">{{ $curr }}</strong> dari <strong class="text-navy-900">{{ number_format($maxP) }}</strong> Halaman Direktori MA</span>
                    </div>

                    <div class="flex items-center gap-1.5 flex-wrap">
                        <!-- Tombol Halaman Pertama -->
                        @if($curr > 1)
                            <a 
                                href="{{ route('home', ['tahun' => $tahunAktif ?? 0, 'kategori' => $kategoriAktif ?? 'semua', 'peradilan' => $peradilanAktif ?? '', 'periode' => $periodeAktif ?? 'putus', 'q' => $search ?? '', 'page' => 1]) }}"
                                class="px-3 py-2 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition flex items-center gap-1 shadow-sm"
                                title="Halaman Pertama (1)"
                            >
                                <i class="fa-solid fa-angles-left"></i>
                                <span class="hidden sm:inline">Pertama</span>
                            </a>
                        @endif

                        <!-- Tombol Sebelumnya -->
                        @if($hasPrev)
                            <a 
                                href="{{ route('home', ['tahun' => $tahunAktif ?? 0, 'kategori' => $kategoriAktif ?? 'semua', 'peradilan' => $peradilanAktif ?? '', 'periode' => $periodeAktif ?? 'putus', 'q' => $search ?? '', 'page' => $curr - 1]) }}"
                                class="px-3.5 py-2 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition flex items-center gap-1.5 shadow-sm"
                            >
                                <i class="fa-solid fa-angle-left"></i>
                                <span>Sebelumnya</span>
                            </a>
                        @else
                            <button disabled class="px-3.5 py-2 rounded-lg bg-slate-100 text-slate-400 text-xs font-semibold cursor-not-allowed border border-slate-200">
                                <i class="fa-solid fa-angle-left"></i> Sebelumnya
                            </button>
                        @endif

                        <span class="px-3.5 py-2 rounded-lg bg-navy-900 text-white font-bold text-xs shadow-sm">
                            {{ $curr }}
                        </span>

                        <!-- Tombol Selanjutnya -->
                        @if($hasNext)
                            <a 
                                href="{{ route('home', ['tahun' => $tahunAktif ?? 0, 'kategori' => $kategoriAktif ?? 'semua', 'peradilan' => $peradilanAktif ?? '', 'periode' => $periodeAktif ?? 'putus', 'q' => $search ?? '', 'page' => $curr + 1]) }}"
                                class="px-3.5 py-2 rounded-lg bg-navy-900 hover:bg-navy-800 text-white text-xs font-semibold transition flex items-center gap-1.5 shadow-sm"
                            >
                                <span>Selanjutnya</span>
                                <i class="fa-solid fa-angle-right"></i>
                            </a>
                        @else
                            <button disabled class="px-3.5 py-2 rounded-lg bg-slate-100 text-slate-400 text-xs font-semibold cursor-not-allowed border border-slate-200">
                                Selanjutnya <i class="fa-solid fa-angle-right"></i>
                            </button>
                        @endif

                        <!-- Tombol Halaman Terakhir -->
                        @if($curr < $maxP)
                            <a 
                                href="{{ route('home', ['tahun' => $tahunAktif ?? 0, 'kategori' => $kategoriAktif ?? 'semua', 'peradilan' => $peradilanAktif ?? '', 'periode' => $periodeAktif ?? 'putus', 'q' => $search ?? '', 'page' => $maxP]) }}"
                                class="px-3 py-2 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition flex items-center gap-1 shadow-sm"
                                title="Halaman Terakhir ({{ $maxP }})"
                            >
                                <span class="hidden sm:inline">Terakhir</span>
                                <i class="fa-solid fa-angles-right"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </main>
        </div>



        <!-- Modal Pop-Up: Wajib Login Terlebih Dahulu (Login Required) -->
        <div id="modal-login-required" class="fixed inset-0 z-40 bg-slate-900/20 hidden flex items-center justify-center p-4 pt-20 transition-all">
            <div class="card-clean rounded-2xl max-w-md w-full p-6 sm:p-8 shadow-2xl space-y-6 animate-in fade-in zoom-in-95 duration-150 border border-slate-200">
                <!-- Header Icon -->
                <div class="text-center space-y-2">
                    <div class="w-16 h-16 rounded-2xl bg-gold-50 border border-gold-200 text-gold-700 flex items-center justify-center text-3xl shadow-sm mx-auto">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <span class="inline-block text-[10px] font-bold tracking-widest uppercase bg-gold-100 text-gold-900 px-2 py-0.5 rounded border border-gold-300">
                        Autentikasi Diperlukan
                    </span>
                    <h3 class="text-xl font-serif font-bold text-navy-950">
                        Masuk untuk Mengakses <span class="legal-gold-text">Tanya AI Hukum</span>
                    </h3>
                    <p class="text-xs text-slate-500 leading-relaxed max-w-xs mx-auto">
                        Fitur konsultasi cerdas dan pembedahan amar putusan berbasis AI hanya dapat diakses oleh pengguna terdaftar.
                    </p>
                </div>

                <!-- Feature Benefits List -->
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-2 text-xs text-slate-700">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i>
                        <span>Rangkum pertimbangan hakim secara instan</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i>
                        <span>Cari pasal dan preseden perkara serupa</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i>
                        <span>Analisis potensi dikabulkannya kasasi / PK</span>
                    </div>
                </div>

                <!-- Action Buttons: Login & Register -->
                <div class="space-y-2.5 pt-1">
                    <a 
                        href="{{ route('login') }}" 
                        class="w-full py-3 rounded-xl bg-navy-900 hover:bg-navy-800 text-white font-semibold text-xs tracking-wider transition shadow-md shadow-navy-950/10 flex items-center justify-center gap-2"
                    >
                        <i class="fa-solid fa-right-to-bracket text-gold-400"></i>
                        <span>Masuk ke Akun Saya</span>
                    </a>

                    <a 
                        href="{{ route('register') }}" 
                        class="w-full py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 hover:text-navy-900 font-semibold text-xs tracking-wider transition border border-slate-300 flex items-center justify-center gap-2"
                    >
                        <i class="fa-regular fa-user-plus text-slate-500"></i>
                        <span>Daftar Akun Baru (Gratis)</span>
                    </a>
                </div>

                <div class="text-center pt-1 border-t border-slate-100">
                    <button 
                        type="button" 
                        onclick="closeLoginRequiredModal()" 
                        class="text-xs text-slate-400 hover:text-slate-600 font-medium transition"
                    >
                        Nanti Saja, Lanjutkan Membaca
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Dialog: Tanya AI Hukum (Untuk User yang Sudah Login) -->
        <div id="modal-ai-chat" class="fixed inset-0 z-40 bg-slate-900/20 hidden flex items-center justify-center p-4 pt-20 transition-all">
            <div class="bg-white rounded-2xl max-w-2xl w-full h-[72vh] max-h-[640px] shadow-xl flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-150 border border-slate-200/80">
                <!-- Header Chat AI -->
                <div class="px-6 py-4 bg-navy-900 text-white flex items-center justify-between border-b border-navy-800 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gold-500/20 text-gold-400 flex items-center justify-center text-lg font-bold border border-gold-500/30">
                            <i class="fa-solid fa-brain"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-serif font-bold text-white">Asisten AI Riset Hukum</h3>
                                <span class="text-[10px] bg-emerald-500/20 text-emerald-400 px-2 py-0.5 rounded border border-emerald-500/30 font-medium">Online</span>
                            </div>
                            <p class="text-xs text-slate-400">Didukung basis data direktori Mahkamah Agung RI</p>
                        </div>
                    </div>
                    <button onclick="closeAiChatModal()" class="w-8 h-8 rounded-lg bg-navy-800 hover:bg-navy-700 text-slate-300 text-sm flex items-center justify-center transition">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Chat Messages Body -->
                <div id="ai-chat-messages" class="flex-grow p-6 overflow-y-auto space-y-4 bg-slate-50 text-xs">
                    <!-- Bot Greeting -->
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-navy-900 text-gold-400 flex items-center justify-center shrink-0 text-xs">
                            <i class="fa-solid fa-scale-balanced"></i>
                        </div>
                        <div class="bg-white p-4 rounded-2xl rounded-tl-none border border-slate-200 shadow-sm text-slate-800 space-y-2 max-w-lg leading-relaxed">
                            <p class="font-semibold text-navy-900">
                                Halo, @auth {{ Auth::user()->name }} @else Rekan Penegak Hukum @endauth! 👋
                            </p>
                            <p>
                                Saya adalah asisten intelijen hukum TanyaPutusan. Anda dapat menanyakan ringkasan yurisprudensi, analisis amar putusan, sengketa tanah, tindak pidana korupsi, atau membedah perkara tertentu dari Mahkamah Agung.
                            </p>
                            <div class="pt-1 flex flex-wrap gap-1.5">
                                <button onclick="setAiQuery('Apa syarat pembeli beriktikad baik menurut yurisprudensi MA?')" class="px-2.5 py-1 rounded-md bg-slate-100 hover:bg-gold-50 hover:text-gold-900 text-slate-700 text-[11px] border border-slate-200 transition">
                                    💡 Kaidah Pembeli Beriktikad Baik
                                </button>
                                <button onclick="setAiQuery('Bagaimana penerapan perbuatan melawan hukum (PMH) dalam sengketa tanah?')" class="px-2.5 py-1 rounded-md bg-slate-100 hover:bg-gold-50 hover:text-gold-900 text-slate-700 text-[11px] border border-slate-200 transition">
                                    💡 Sengketa PMH Tanah
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat Input Box -->
                <div class="p-4 bg-white border-t border-slate-200 shrink-0">
                    <form onsubmit="handleSendAiMessage(event)" class="space-y-2">
                        <div class="flex items-center gap-2">
                            <!-- Input Pertanyaan -->
                            <input 
                                type="text" 
                                id="ai-user-input" 
                                placeholder="Ketik pertanyaan hukum atau nomor putusan yang ingin dikaji..." 
                                class="flex-grow bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-navy-600 focus:bg-white focus:ring-1 focus:ring-navy-600 transition"
                            >
                            
                            <!-- AI Engine Dropdown Selector -->
                            <div class="relative shrink-0">
                                <select 
                                    id="global-ai-engine-select" 
                                    onchange="setGlobalAiEngine(this.value)"
                                    class="appearance-none bg-slate-100 hover:bg-slate-200/80 border border-slate-300 rounded-xl pl-8 pr-7 py-3 text-xs font-semibold text-slate-800 cursor-pointer focus:outline-none focus:ring-1 focus:ring-navy-600 transition shadow-2xs"
                                    title="Pilih Engine AI (Lokal atau Cloud)"
                                >
                                    <option value="gemini">☁️ Gemini 2.5 Flash</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-slate-500">
                                    <i id="global-engine-select-icon" class="fa-solid fa-microchip text-indigo-600 text-xs"></i>
                                </div>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2 text-slate-400">
                                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                </div>
                            </div>

                            <button 
                                type="submit" 
                                class="px-5 py-3 rounded-xl bg-navy-900 hover:bg-navy-800 text-white font-semibold text-xs tracking-wider transition shadow-sm flex items-center gap-2 cursor-pointer shrink-0"
                            >
                                <span>Kirim</span>
                                <i class="fa-solid fa-paper-plane text-gold-400 text-xs"></i>
                            </button>
                        </div>
                    </form>
                    <p class="text-[10px] text-slate-400 text-center mt-2">
                        Analisis hukum AI bersifat informatif edukatif dan tidak menggantikan nasihat hukum advokat resmi.
                    </p>
                </div>
            </div>
        </div>

        <!-- Modal Pratinjau Putusan (In-App Document Previewer) -->
        <div id="modal-preview" class="fixed inset-0 z-40 bg-slate-900/20 hidden flex items-center justify-center p-4 pt-20 transition-all">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200/90 w-full max-w-4xl h-[72vh] max-h-[640px] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                <!-- Header Modal -->
                <div class="px-6 py-4 bg-navy-900 text-white flex items-center justify-between border-b border-navy-800 shrink-0">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div class="w-9 h-9 rounded-xl bg-gold-500/20 text-gold-400 flex items-center justify-center text-base font-bold border border-gold-500/30 shrink-0">
                            <i class="fa-solid fa-book-open"></i>
                        </div>
                        <div class="truncate">
                            <h3 id="preview-modal-title" class="text-sm font-serif font-bold text-white truncate">
                                Memuat Pratinjau Perkara...
                            </h3>
                            <p class="text-[11px] text-slate-400 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span>
                                <span>Direktori Putusan Mahkamah Agung RI &bull; Live Preview</span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <!-- Tab Switcher: Ringkasan vs PDF vs Bedah AI -->
                        <div class="inline-flex rounded-lg bg-navy-800 p-1 border border-navy-700 text-xs font-semibold mr-2">
                            <button 
                                id="tab-btn-summary" 
                                onclick="switchPreviewTab('summary')" 
                                class="px-3 py-1.5 rounded-md bg-navy-900 text-gold-400 shadow-sm transition flex items-center gap-1.5"
                            >
                                <i class="fa-solid fa-file-lines text-xs"></i>
                                <span>Ringkasan</span>
                            </button>
                            <button 
                                id="tab-btn-pdf" 
                                onclick="switchPreviewTab('pdf')" 
                                class="px-3 py-1.5 rounded-md text-slate-300 hover:text-white transition flex items-center gap-1.5"
                            >
                                <i class="fa-solid fa-file-pdf text-red-400 text-xs"></i>
                                <span>Dokumen PDF</span>
                            </button>
                            <button 
                                id="tab-btn-ai" 
                                onclick="switchPreviewTab('ai')" 
                                class="px-3 py-1.5 rounded-md text-amber-300 hover:text-amber-200 transition flex items-center gap-1.5"
                            >
                                <i class="fa-solid fa-wand-magic-sparkles text-amber-400 text-xs"></i>
                                <span>Bedah AI</span>
                            </button>
                        </div>

                        <a 
                            id="preview-btn-newtab" 
                            href="#" 
                            target="_blank" 
                            class="px-3 py-1.5 rounded-lg bg-navy-800 hover:bg-navy-700 text-slate-200 text-xs font-medium transition flex items-center gap-1.5 border border-navy-700"
                            title="Buka Berkas di Tab Baru"
                        >
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                            <span class="hidden sm:inline">Tab Baru</span>
                        </a>
                        <button 
                            onclick="closePratinjau()" 
                            class="w-8 h-8 rounded-lg bg-navy-800 hover:bg-red-900/50 hover:text-red-400 text-slate-300 text-sm flex items-center justify-center transition border border-navy-700"
                            title="Tutup Pratinjau"
                        >
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body: 3 Tampilan (Ringkasan vs PDF vs Bedah AI) -->
                <div class="relative flex-grow overflow-hidden bg-slate-50 flex flex-col">
                    
                    <!-- Loading State -->
                    <div id="preview-loading-state" class="absolute inset-0 z-20 bg-white/90 backdrop-blur-sm flex flex-col items-center justify-center gap-3 text-navy-900">
                        <i class="fa-solid fa-spinner fa-spin text-3xl text-gold-600"></i>
                        <div class="text-center">
                            <p class="text-sm font-bold text-navy-900">Mengambil Pratinjau Langsung dari Mahkamah Agung...</p>
                            <p class="text-xs text-slate-500 mt-1">Membaca amar, kaidah hukum, pihak perkara & status berkas</p>
                        </div>
                    </div>

                    <!-- Panel 1: Ringkasan & Kaidah Putusan (Default) -->
                    <div id="panel-summary" class="flex-grow overflow-y-auto p-6 sm:p-8 space-y-6">
                        <!-- Judul Lengkap -->
                        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-2">
                            <span class="px-2.5 py-1 rounded text-[11px] font-bold uppercase tracking-wider bg-gold-50 text-gold-800 border border-gold-200">
                                Identitas Perkara
                            </span>
                            <h2 id="summary-full-title" class="text-lg sm:text-xl font-serif font-bold text-navy-950 leading-snug">
                                -
                            </h2>
                        </div>

                        <!-- Kaidah Hukum Block (Jika ada) -->
                        <div id="summary-kaidah-section" class="bg-amber-50/70 border-l-4 border-l-gold-500 p-5 rounded-xl border border-gold-200/80 shadow-sm space-y-2 hidden">
                            <h4 class="text-xs font-bold text-gold-900 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fa-solid fa-quote-left text-gold-600"></i> Kaidah Hukum & Pertimbangan Tetap
                            </h4>
                            <div id="summary-kaidah-content" class="text-sm text-slate-800 font-serif italic leading-relaxed space-y-2">
                            </div>
                        </div>

                        <!-- Grid Metadata Perkara -->
                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
                            <h4 class="text-xs font-bold text-navy-900 uppercase tracking-wider flex items-center gap-2">
                                <i class="fa-solid fa-circle-info text-navy-600"></i> Informasi Detail Perkara
                            </h4>
                            <div id="summary-meta-grid" class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                                <!-- Terisi via JS -->
                            </div>
                        </div>

                        <!-- Tombol Aksi Lanjut ke PDF & Bedah AI -->
                        <div class="p-4 rounded-xl bg-slate-100 border border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="text-xs text-slate-600">
                                <span class="font-semibold text-slate-800">Pelajari lebih dalam perkara ini:</span>
                                <p class="text-slate-500">Buka salinan PDF lengkap atau diskusikan pertimbangan hukum dengan AI.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button 
                                    type="button" 
                                    onclick="switchPreviewTab('ai')" 
                                    class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-gold-500 hover:from-amber-600 hover:to-gold-600 text-navy-950 font-bold transition flex items-center gap-2 shadow-sm text-xs shrink-0"
                                >
                                    <i class="fa-solid fa-wand-magic-sparkles text-navy-950"></i>
                                    <span>Bedah AI</span>
                                </button>
                                <button 
                                    type="button" 
                                    onclick="switchPreviewTab('pdf')" 
                                    class="px-4 py-2.5 rounded-xl bg-navy-900 hover:bg-navy-800 text-white font-semibold transition flex items-center gap-2 shadow-sm text-xs shrink-0"
                                >
                                    <i class="fa-solid fa-file-pdf text-red-400"></i>
                                    <span>Buka PDF</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Panel 2: PDF Stream Viewer -->
                    <div id="panel-pdf" class="flex-grow hidden relative bg-slate-900 flex items-center justify-center">
                        <div id="pdf-stream-loading" class="absolute inset-0 z-10 bg-slate-900/90 flex flex-col items-center justify-center gap-3 text-white hidden">
                            <i class="fa-solid fa-spinner fa-spin text-3xl text-gold-400"></i>
                            <div class="text-center">
                                <p class="text-sm font-semibold">Mengambil Stream Dokumen PDF dari Mahkamah Agung...</p>
                                <p class="text-xs text-slate-400 mt-1">Bypass Cloudflare & Stream langsung ke layar Anda</p>
                            </div>
                        </div>
                        <iframe 
                            id="preview-pdf-frame" 
                            src="" 
                            class="w-full h-full border-0"
                            onload="document.getElementById('pdf-stream-loading').classList.add('hidden')"
                        ></iframe>
                    </div>

                    <!-- Panel 3: Bedah Putusan bersama AI -->
                    <div id="panel-ai" class="flex-grow hidden relative flex flex-col bg-slate-50 overflow-hidden">
                        <!-- Header Banner Konteks Perkara -->
                        <div class="px-6 py-3.5 bg-white border-b border-slate-200 flex items-center justify-between gap-3 shrink-0">
                            <div class="flex items-center gap-2.5 overflow-hidden">
                                <div class="w-8 h-8 rounded-lg bg-gold-50 border border-gold-200 text-gold-700 flex items-center justify-center shrink-0 text-xs font-bold">
                                    <i class="fa-solid fa-gavel"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 id="modal-ai-case-title" class="text-xs font-bold text-navy-950 truncate">
                                        Telaah Perkara Aktif
                                    </h4>
                                    <p class="text-[11px] text-slate-500">Analisis ratio decidendi, amar pertimbangan, dan kaidah hukum</p>
                                </div>
                            </div>
                                            
                        </div>

                        <!-- Chat Messages Container -->
                        <div id="modal-ai-messages" class="flex-grow p-4 sm:p-6 overflow-y-auto space-y-4 text-xs">
                            <!-- Bot Welcome Bubble -->
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-navy-900 text-gold-400 flex items-center justify-center shrink-0 text-xs shadow-sm">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                                </div>
                                <div class="bg-white p-4 rounded-2xl rounded-tl-none border border-slate-200 shadow-xs text-slate-800 space-y-3 max-w-xl leading-relaxed">
                                    <p class="font-bold text-navy-950 flex items-center gap-1.5">
                                        Halo! Saya siap membedah dokumen putusan ini untuk Anda.
                                    </p>
                                    <p class="text-slate-600">
                                        Pilih telaah cepat berikut atau ketik pertanyaan hukum spesifik mengenai berkas perkara ini:
                                    </p>
                                    <div class="pt-1 flex flex-wrap gap-2">
                                        <button 
                                            type="button" 
                                            onclick="triggerModalAiPrompt('Bedah dan jelaskan secara ringkas amar putusan perkara ini beserta implikasi hukumnya.')" 
                                            class="px-3 py-1.5 rounded-lg bg-slate-50 hover:bg-gold-50 hover:text-gold-950 text-slate-700 text-[11px] font-medium border border-slate-200 transition flex items-center gap-1.5 cursor-pointer"
                                        >
                                            <i class="fa-solid fa-scale-balanced text-gold-600 text-xs"></i>
                                            <span>Bedah Amar Putusan</span>
                                        </button>
                                        <button 
                                            type="button" 
                                            onclick="triggerModalAiPrompt('Jelaskan konstruksi pertimbangan hukum hakim (ratio decidendi) dalam menjatuhkan putusan ini.')" 
                                            class="px-3 py-1.5 rounded-lg bg-slate-50 hover:bg-gold-50 hover:text-gold-950 text-slate-700 text-[11px] font-medium border border-slate-200 transition flex items-center gap-1.5 cursor-pointer"
                                        >
                                            <i class="fa-solid fa-brain text-purple-600 text-xs"></i>
                                            <span>Pertimbangan Hukum Hakim</span>
                                        </button>
                                        <button 
                                            type="button" 
                                            onclick="triggerModalAiPrompt('Apakah perkara ini memuat kaidah hukum penting atau bertentangan dengan yurisprudensi tetap MA terdahulu?')" 
                                            class="px-3 py-1.5 rounded-lg bg-slate-50 hover:bg-gold-50 hover:text-gold-950 text-slate-700 text-[11px] font-medium border border-slate-200 transition flex items-center gap-1.5 cursor-pointer"
                                        >
                                            <i class="fa-solid fa-scroll text-blue-600 text-xs"></i>
                                            <span>Kaidah & Yurisprudensi</span>
                                        </button>
                                        <button 
                                            type="button" 
                                            onclick="triggerModalAiPrompt('Bagaimana peluang dan analisis dasar hukum jika pihak yang kalah mengajukan upaya hukum lanjutan (Banding / Kasasi / PK)?')" 
                                            class="px-3 py-1.5 rounded-lg bg-slate-50 hover:bg-gold-50 hover:text-gold-950 text-slate-700 text-[11px] font-medium border border-slate-200 transition flex items-center gap-1.5 cursor-pointer"
                                        >
                                            <i class="fa-solid fa-shield-halved text-emerald-600 text-xs"></i>
                                            <span>Potensi Upaya Hukum</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Chat Input Box & Engine Selector -->
                        <div class="p-3 sm:p-4 bg-white border-t border-slate-200 shrink-0">
                            @auth
                                <form onsubmit="handleSendModalAiMessage(event)" class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        
                                        <!-- Input Pertanyaan -->
                                        <input 
                                            type="text" 
                                            id="modal-ai-input" 
                                            placeholder="Tanyakan hal spesifik tentang pertimbangan atau amar perkara ini..." 
                                            class="flex-grow bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-navy-600 focus:bg-white focus:ring-1 focus:ring-navy-600 transition"
                                        >
                                        
                                        <div class="relative shrink-0">
                                            <select 
                                                id="modal-ai-engine-select" 
                                                onchange="setAiEngine(this.value)"
                                                class="appearance-none bg-slate-100 hover:bg-slate-200/80 border border-slate-300 rounded-xl pl-8 pr-7 py-2.5 text-xs font-semibold text-slate-800 cursor-pointer focus:outline-none focus:ring-1 focus:ring-navy-600 transition shadow-2xs"
                                                title="Pilih Engine AI (Lokal atau Cloud)"
                                            >
                                                <option value="gemini">☁️ Gemini 2.5 Flash</option>
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-slate-500">
                                                <i id="engine-select-icon" class="fa-solid fa-microchip text-indigo-600 text-xs"></i>
                                            </div>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2 text-slate-400">
                                                <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                            </div>
                                        </div>
                                        <button 
                                            type="submit" 
                                            class="px-4 py-2.5 rounded-xl bg-navy-900 hover:bg-navy-800 text-white font-semibold text-xs transition shadow-sm flex items-center gap-1.5 cursor-pointer shrink-0"
                                        >
                                            <span>Kirim</span>
                                            <i class="fa-solid fa-paper-plane text-gold-400 text-[10px]"></i>
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3 text-xs">
                                    <div class="flex items-center gap-2 text-slate-600">
                                        <i class="fa-solid fa-lock text-amber-600"></i>
                                        <span>Masuk akun untuk bertanya bebas dan menyimpan riset bedah putusan ini.</span>
                                    </div>
                                    <button 
                                        type="button" 
                                        onclick="handleTanyaAiClick()" 
                                        class="px-3.5 py-1.5 rounded-lg bg-navy-900 text-white font-bold text-xs hover:bg-navy-800 transition shadow-sm shrink-0 cursor-pointer"
                                    >
                                        Masuk Akun
                                    </button>
                                </div>
                            @endauth
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- MODAL LOGIN REQUIRED (Muncul saat user belum login mengklik 'Tanya AI Hukum') -->
        <div id="modal-login-required" class="fixed inset-0 z-50 bg-navy-950/70 backdrop-blur-sm flex items-center justify-center p-4 hidden">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-md w-full overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                <!-- Header Modal -->
                <div class="bg-gradient-to-r from-navy-950 via-navy-900 to-navy-950 p-6 text-white text-center relative">
                    <button 
                        type="button" 
                        onclick="closeLoginRequiredModal()" 
                        class="absolute top-4 right-4 text-slate-400 hover:text-white transition w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-xs cursor-pointer"
                    >
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    
                    <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-gold-500/20 border border-gold-400/40 flex items-center justify-center text-gold-400 text-2xl shadow-inner">
                        <i class="fa-solid fa-brain"></i>
                    </div>
                    
                    <h3 class="text-lg font-serif font-bold text-white tracking-wide">
                        Fitur Tanya AI Hukum
                    </h3>
                    <p class="text-xs text-slate-300 mt-1 max-w-xs mx-auto">
                        Akses analisis putusan berbasis kecerdasan artifisial dan konsultasi yuridis komprehensif.
                    </p>
                </div>

                <!-- Body Modal -->
                <div class="p-6 space-y-4">
                    <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 flex items-start gap-3 text-xs text-amber-900">
                        <i class="fa-solid fa-lock text-amber-700 text-sm mt-0.5"></i>
                        <p class="leading-relaxed">
                            Untuk menjaga privasi riset dan menyimpan riwayat telaah yuridis Anda, silakan <strong>masuk ke akun</strong> terlebih dahulu.
                        </p>
                    </div>

                    <div class="space-y-2.5 pt-2">
                        <!-- Login dengan Google Cepat -->
                        <a 
                            href="{{ route('auth.google', ['mode' => 'login']) }}" 
                            class="w-full py-2.5 px-4 rounded-xl border border-slate-300 hover:border-slate-400 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs transition shadow-sm flex items-center justify-center gap-2.5 cursor-pointer"
                        >
                            <svg class="w-4 h-4" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                            </svg>
                            <span>Lanjut dengan Akun Google</span>
                        </a>

                        <!-- Masuk Biasa -->
                        <a 
                            href="{{ route('login') }}" 
                            class="w-full py-2.5 px-4 rounded-xl bg-navy-900 hover:bg-navy-800 text-white font-semibold text-xs tracking-wider transition shadow-sm flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <i class="fa-solid fa-right-to-bracket text-gold-400"></i>
                            <span>Masuk ke Akun Saya</span>
                        </a>

                        <!-- Daftar Baru -->
                        <a 
                            href="{{ route('register') }}" 
                            class="w-full py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition border border-slate-200 flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <i class="fa-solid fa-user-plus text-slate-500"></i>
                            <span>Daftar Akun Baru (Gratis)</span>
                        </a>
                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="bg-slate-50 px-6 py-3 border-t border-slate-200 text-center text-[11px] text-slate-400">
                    Bebas biaya &bull; Akses penuh ke direktori yurisprudensi dan asisten hukum
                </div>
            </div>
        </div>

        <!-- MODAL AI CHAT (Jika User Sudah Login) -->
        <div id="modal-ai-chat" class="fixed inset-0 z-50 bg-navy-950/70 backdrop-blur-sm flex items-center justify-center p-4 hidden">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-2xl w-full h-[85vh] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                <!-- Header -->
                <div class="bg-gradient-to-r from-navy-950 via-navy-900 to-navy-950 px-6 py-4 text-white flex items-center justify-between border-b border-navy-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gold-500/20 border border-gold-400/40 flex items-center justify-center text-gold-400 text-lg">
                            <i class="fa-solid fa-brain"></i>
                        </div>
                        <div>
                            <h3 class="font-serif font-bold text-sm tracking-wide text-white flex items-center gap-2">
                                <span>Tanya AI Hukum Indonesia</span>
                                <span class="px-2 py-0.5 rounded text-[10px] bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-sans font-normal">Aktif</span>
                            </h3>
                            <p class="text-[11px] text-slate-300">Asisten yuridis cerdas berbasis data putusan Mahkamah Agung RI</p>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        onclick="closeAiChatModal()" 
                        class="text-slate-400 hover:text-white transition w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-xs cursor-pointer"
                    >
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Chat Body -->
                <div id="ai-chat-messages" class="flex-grow overflow-y-auto p-6 space-y-4 bg-slate-50/60 text-xs">
                    <!-- Sambutan AI -->
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-navy-900 text-gold-400 flex items-center justify-center text-xs shrink-0 shadow-sm border border-navy-700">
                            <i class="fa-solid fa-scale-balanced"></i>
                        </div>
                        <div class="max-w-xl bg-white p-4 rounded-2xl rounded-tl-none border border-slate-200 shadow-sm space-y-2 text-slate-800 leading-relaxed">
                            <p class="font-semibold text-navy-900">
                                Halo, @auth {{ Auth::user()->name }} @else Rekan Penegak Hukum @endauth! 👋
                            </p>
                            <p>
                                Saya adalah asisten kecerdasan artifisial TanyaPutusan. Anda dapat menanyakan doktrin hukum, perbandingan yurisprudensi, analisis amar putusan perdata/pidana/TUN/agama, hingga konstruksi pertimbangan hukum Mahkamah Agung.
                            </p>
                            <div class="pt-2">
                                <span class="text-[11px] font-semibold text-slate-500 block mb-1.5">Contoh pertanyaan populer:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <button onclick="setAiQuery('Apa kaidah hukum terbaru terkait sengketa tanah waris?')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-gold-50 hover:text-gold-900 hover:border-gold-300 border border-slate-200 text-[11px] transition text-left">
                                        ⚖️ Kaidah sengketa tanah waris
                                    </button>
                                    <button onclick="setAiQuery('Bagaimana pertimbangan MA terkait wanprestasi vs penipuan?')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-gold-50 hover:text-gold-900 hover:border-gold-300 border border-slate-200 text-[11px] transition text-left">
                                        📜 Wanprestasi vs Tindak Pidana Penipuan
                                    </button>
                                    <button onclick="setAiQuery('Apa syarat pembuktian perbuatan melawan hukum oleh penguasa (OOD)?')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-gold-50 hover:text-gold-900 hover:border-gold-300 border border-slate-200 text-[11px] transition text-left">
                                        🏛️ Gugatan OOD di PTUN
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Input Footer -->
                <div class="p-4 bg-white border-t border-slate-200">
                    <form onsubmit="handleSendAiMessage(event)" class="flex items-center gap-2">
                        <input 
                            type="text" 
                            id="ai-user-input" 
                            placeholder="Ketik pertanyaan hukum atau analisis perkara di sini..." 
                            class="flex-grow bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-navy-600 focus:bg-white focus:ring-2 focus:ring-navy-100 transition"
                            autocomplete="off"
                        >
                        <button 
                            type="submit" 
                            class="px-4 py-2.5 rounded-xl bg-navy-900 hover:bg-navy-800 text-white font-semibold text-xs transition shadow-sm flex items-center gap-1.5 cursor-pointer shrink-0"
                        >
                            <span>Kirim</span>
                            <i class="fa-solid fa-paper-plane text-gold-400 text-xs"></i>
                        </button>
                    </form>
                    <p class="text-[10px] text-slate-400 mt-2 text-center">
                        AI dapat melakukan kekeliruan kutipan. Selalu verifikasi amar dengan salinan resmi putusan Mahkamah Agung.
                    </p>
                </div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="mt-auto bg-white border-t border-slate-200 text-slate-600 text-xs py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2 font-serif font-bold text-navy-900">
                <i class="fa-solid fa-scale-balanced text-gold-600"></i>
                <span>TanyaPutusan Legal Intelligence &copy; 2026</span>
            </div>
            <p class="text-slate-500">
                Data bersumber resmi dari Direktori Putusan Mahkamah Agung Republik Indonesia (putusan3.mahkamahagung.go.id)
            </p>
        </div>
    </footer>

    <!-- Interactive Client JS Logic -->
    <script>
        let currentTargetUrl = '';
        let currentDecisionTitle = '';

        function changeTahun(val) {
            const url = new URL(window.location.href);
            url.searchParams.set('tahun', val);
            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        }

        function changePeradilan(val) {
            const url = new URL(window.location.href);
            url.searchParams.set('peradilan', val);
            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        }

        function toggleSidebarCard(headerEl) {
            const body = headerEl.nextElementSibling;
            const chevron = headerEl.querySelector('.fa-chevron-down');
            if (body) {
                body.classList.toggle('hidden');
                if (chevron) {
                    chevron.classList.toggle('rotate-180');
                }
            }
        }

        // Cek status autentikasi pengguna dari server
        const isAuthenticated = {{ Auth::check() ? 'true' : 'false' }};

        function handleTanyaAiClick() {
            if (!isAuthenticated) {
                // Tampilkan Modal Login Required jika user belum login
                const modalLogin = document.getElementById('modal-login-required');
                if (modalLogin) {
                    modalLogin.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
            } else {
                // Jika sudah login, langsung buka Modal AI Legal Chat
                const modalChat = document.getElementById('modal-ai-chat');
                if (modalChat) {
                    modalChat.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                    setTimeout(() => {
                        const input = document.getElementById('ai-user-input');
                        if (input) input.focus();
                    }, 100);
                }
            }
        }

        function closeLoginRequiredModal() {
            const modal = document.getElementById('modal-login-required');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        function closeAiChatModal() {
            const modal = document.getElementById('modal-ai-chat');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        function parseLegalMarkdown(text) {
            if (!text) return '';

            // Pisahkan per baris untuk memproses heading dan list
            let lines = text.split('\n');
            let html = '';
            let inList = false;

            for (let i = 0; i < lines.length; i++) {
                let line = lines[i].trim();

                if (!line) {
                    if (inList) {
                        html += '</ul>';
                        inList = false;
                    }
                    continue;
                }

                // 1. Bersihkan Headings (####, ###, ##, #) dan ganti menjadi subjudul bergaya editorial
                if (/^#{1,6}\s+(.*)/.test(line)) {
                    if (inList) {
                        html += '</ul>';
                        inList = false;
                    }
                    let headingText = line.replace(/^#{1,6}\s+/, '');
                    html += `<h4 class="font-bold text-navy-950 text-xs tracking-wide uppercase mt-3 mb-1 border-b border-slate-200/80 pb-1 flex items-center gap-1.5"><i class="fa-solid fa-scale-balanced text-gold-600 text-[10px]"></i> ${headingText}</h4>`;
                    continue;
                }

                // 2. Bersihkan bullet point (* item, - item) dan ganti menjadi <ul><li> rapi
                if (/^[\*\-]\s+(.*)/.test(line)) {
                    if (!inList) {
                        html += '<ul class="list-disc list-inside space-y-1.5 my-1.5 text-slate-700 pl-1">';
                        inList = true;
                    }
                    let itemText = line.replace(/^[\*\-]\s+/, '');
                    html += `<li>${itemText}</li>`;
                    continue;
                }

                // 3. Bersihkan numbered list (1. item, 2. item)
                if (/^\d+\.\s+(.*)/.test(line)) {
                    if (inList) {
                        html += '</ul>';
                        inList = false;
                    }
                    let numText = line.replace(/^\d+\.\s+/, '');
                    html += `<div class="flex items-start gap-2 my-1 text-slate-700"><span class="font-bold text-gold-700 shrink-0">&bull;</span><span>${numText}</span></div>`;
                    continue;
                }

                // Jika bukan list, tutup ul jika ada
                if (inList) {
                    html += '</ul>';
                    inList = false;
                }

                // 4. Paragraf biasa
                html += `<p class="leading-relaxed mb-2 text-slate-700">${line}</p>`;
            }

            if (inList) {
                html += '</ul>';
            }

            // 5. Ganti **text** menjadi <strong>text</strong> (Bold langsung)
            html = html.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-navy-950">$1</strong>');

            // 6. Ganti *text* atau _text_ menjadi <em>text</em> (Italic langsung)
            html = html.replace(/\*([^\*]+)\*/g, '<em class="italic text-slate-800">$1</em>');
            html = html.replace(/\b_([^_]+)_\b/g, '<em class="italic text-slate-800">$1</em>');

            // 7. Bersihkan sisa backtick `kode/pasal`
            html = html.replace(/`([^`]+)`/g, '<span class="px-1 py-0.5 rounded bg-slate-100 text-navy-900 font-mono text-[11px] border border-slate-200">$1</span>');

            return html;
        }

        function setAiQuery(text) {
            const input = document.getElementById('ai-user-input');
            if (input) {
                input.value = text;
                input.focus();
            }
        }

        let globalActiveAiEngine = 'ollama'; // 'ollama' | 'gemini'

        function setGlobalAiEngine(engine) {
            globalActiveAiEngine = engine;
            const selectEl = document.getElementById('global-ai-engine-select');
            const iconEl = document.getElementById('global-engine-select-icon');

            if (selectEl && selectEl.value !== engine) {
                selectEl.value = engine;
            }

            if (engine === 'gemini') {
                if (iconEl) iconEl.className = 'fa-solid fa-cloud text-amber-500 text-xs';
            } else {
                if (iconEl) iconEl.className = 'fa-solid fa-microchip text-indigo-600 text-xs';
            }
        }

        let aiChatHistory = [];

        async function handleSendAiMessage(e) {
            e.preventDefault();
            const input = document.getElementById('ai-user-input');
            const messagesContainer = document.getElementById('ai-chat-messages');
            if (!input || !input.value.trim()) return;

            const userQuestion = input.value.trim();
            input.value = '';

            // 1. Tampilkan Chat Pengguna
            const userHtml = `
                <div class="flex items-start justify-end gap-2.5">
                    <div class="bg-navy-900 text-white p-3.5 rounded-2xl rounded-tr-none shadow-sm max-w-lg leading-relaxed text-xs">
                        ${userQuestion.replace(/</g, '&lt;').replace(/>/g, '&gt;')}
                    </div>
                    <div class="w-7 h-7 rounded-lg bg-gold-100 text-gold-900 font-bold flex items-center justify-center shrink-0 text-xs border border-gold-300">
                        Anda
                    </div>
                </div>`;
            messagesContainer.insertAdjacentHTML('beforeend', userHtml);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;

            // 2. Tampilkan Animasi Memproses AI
            const loadingId = 'ai-loading-' + Date.now();
            const engineLabel = globalActiveAiEngine === 'ollama' ? 'Ollama Llama 3.2 (Lokal)' : 'Gemini Legal AI (Cloud)';
            const loadingHtml = `
                <div id="${loadingId}" class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-navy-900 text-gold-400 flex items-center justify-center shrink-0 text-xs shadow-sm">
                        <i class="fa-solid fa-brain fa-spin text-gold-400"></i>
                    </div>
                    <div class="bg-white p-4 rounded-2xl rounded-tl-none border border-slate-200 shadow-sm text-slate-500 italic max-w-lg leading-relaxed text-xs flex items-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin text-gold-600"></i>
                        <span>Menelaah doktrin hukum & yurisprudensi Mahkamah Agung via ${engineLabel}...</span>
                    </div>
                </div>`;
            messagesContainer.insertAdjacentHTML('beforeend', loadingHtml);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;

            try {
                const response = await fetch("{{ route('api.ai.chat') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        message: userQuestion,
                        history: aiChatHistory,
                        provider: globalActiveAiEngine
                    })
                });

                const data = await response.json();
                const loadingElem = document.getElementById(loadingId);
                if (loadingElem) loadingElem.remove();

                if (data.success && data.reply) {
                    // Bersihkan karakter markdown mentah (####, *, **, _) dan konversi langsung ke elemen HTML (bold, italic, list, headings)
                    let formattedReply = parseLegalMarkdown(data.reply);

                    // Buat tampilan daftar berkas putusan terkait yang diverifikasi resmi dari portal MA
                    let sourcesHtml = '';
                    if (data.sources && data.sources.length > 0) {
                        sourcesHtml = `
                            <div class="mt-3 pt-3 border-t border-slate-100">
                                <span class="text-[11px] font-bold text-navy-950 flex items-center gap-1.5 uppercase tracking-wider mb-2">
                                    <i class="fa-solid fa-file-shield text-gold-600"></i> Sumber Dokumen Resmi Terkait (putusan3.mahkamahagung.go.id):
                                </span>
                                <div class="space-y-2">
                                    ${data.sources.map(src => `
                                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 hover:border-gold-300 transition flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                            <div class="min-w-0">
                                                <div class="font-bold text-navy-900 text-[11px] truncate flex items-center gap-1.5">
                                                    <i class="fa-regular fa-file-lines text-gold-600"></i>
                                                    <span>${src.judul}</span>
                                                </div>
                                                <div class="text-[10px] text-slate-500 mt-0.5 flex flex-wrap gap-2">
                                                    <span class="bg-slate-200/60 px-1.5 py-0.5 rounded text-slate-700">${src.klasifikasi}</span>
                                                    ${src.tgl_putus ? `<span>Putus: ${src.tgl_putus}</span>` : ''}
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1.5 shrink-0 self-end sm:self-center">
                                                <button 
                                                    type="button" 
                                                    onclick="openPratinjau('${src.url || ''}', '${src.judul ? src.judul.replace(/'/g, "\\'") : ''}')" 
                                                    class="px-2.5 py-1 rounded-lg bg-navy-900 hover:bg-navy-800 text-white font-medium text-[10px] transition flex items-center gap-1"
                                                >
                                                    <i class="fa-solid fa-eye text-gold-400"></i> Buka Berkas
                                                </button>
                                                <a 
                                                    href="${src.url || '#'}" 
                                                    target="_blank" 
                                                    class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-[10px] font-medium transition flex items-center gap-1"
                                                >
                                                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px] text-slate-500"></i> Portal MA
                                                </a>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        `;
                    }

                    const botReplyHtml = `
                        <div class="flex items-start gap-3 animate-in fade-in duration-200">
                            <div class="w-8 h-8 rounded-lg bg-navy-900 text-gold-400 flex items-center justify-center shrink-0 text-xs shadow-sm">
                                <i class="fa-solid fa-scale-balanced"></i>
                            </div>
                            <div class="bg-white p-4 rounded-2xl rounded-tl-none border border-slate-200 shadow-sm text-slate-800 space-y-2 max-w-xl leading-relaxed text-xs">
                                <div class="font-bold text-navy-950 flex items-center justify-between gap-1.5 border-b border-slate-100 pb-1.5 mb-2">
                                    <span class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-gavel text-gold-600"></i> Telaah Yuridis TanyaPutusan AI
                                    </span>
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[9px] text-slate-500 font-medium bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                            ${data.model_used || 'AI Legal'}
                                        </span>
                                        <span class="text-[10px] text-emerald-600 font-normal bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 flex items-center gap-1">
                                            <i class="fa-solid fa-check text-[9px]"></i> Grounded to MA-RI
                                        </span>
                                    </div>
                                </div>
                                <div class="text-slate-800 space-y-2">
                                    ${formattedReply}
                                </div>
                                ${sourcesHtml}
                            </div>
                        </div>`;
                    messagesContainer.insertAdjacentHTML('beforeend', botReplyHtml);

                    // Catat ke riwayat percakapan lokal
                    aiChatHistory.push({ role: 'user', text: userQuestion });
                    aiChatHistory.push({ role: 'model', text: data.reply });

                } else {
                    const errorMsg = data.message || 'Terjadi kendala saat memproses jawaban AI.';
                    const errorHtml = `
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center shrink-0 text-xs">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <div class="bg-red-50 p-3.5 rounded-2xl rounded-tl-none border border-red-200 text-red-800 max-w-lg leading-relaxed text-xs">
                                <strong>Gagal:</strong> ${errorMsg}
                            </div>
                        </div>`;
                    messagesContainer.insertAdjacentHTML('beforeend', errorHtml);
                }

            } catch (err) {
                const loadingElem = document.getElementById(loadingId);
                if (loadingElem) loadingElem.remove();

                const errorHtml = `
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center shrink-0 text-xs">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div class="bg-red-50 p-3.5 rounded-2xl rounded-tl-none border border-red-200 text-red-800 max-w-lg leading-relaxed text-xs">
                            Gagal menghubungi server. Pastikan koneksi internet Anda aktif.
                        </div>
                    </div>`;
                messagesContainer.insertAdjacentHTML('beforeend', errorHtml);
            }

            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        function changeTahun(selectedYear) {
            const peradilan = "{{ $peradilanAktif ?? '' }}";
            const kategori = "{{ $kategoriAktif ?? 'semua' }}";
            let url = "{{ route('home') }}?tahun=" + selectedYear + "&kategori=" + encodeURIComponent(kategori);
            if (peradilan && peradilan !== 'semua') {
                url += "&peradilan=" + encodeURIComponent(peradilan);
            }
            window.location.href = url;
        }

        function changePeradilan(selectedPeradilan) {
            const tahun = "{{ $tahunAktif ?? 0 }}";
            let url = "{{ route('home') }}?tahun=" + tahun;
            if (selectedPeradilan && selectedPeradilan !== 'semua') {
                url += "&peradilan=" + encodeURIComponent(selectedPeradilan);
            } else {
                url += "&kategori=semua";
            }
            window.location.href = url;
        }

        // Fungsi Download Berkas PDF Putusan MA & Simpan Log ke Database Firebase
        async function downloadPDF(url, title, btnElement) {
            if (!url) {
                alert('Tautan berkas perkara tidak ditemukan.');
                return;
            }

            const originalHtml = btnElement ? btnElement.innerHTML : '';
            if (btnElement) {
                btnElement.disabled = true;
                btnElement.classList.add('opacity-75', 'cursor-not-allowed');
                btnElement.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> <span>Mengunduh...</span>';
            }

            try {
                const downloadEndpoint = "{{ route('putusan.pdf') }}?url=" + encodeURIComponent(url) + "&title=" + encodeURIComponent(title || '') + "&download=1";

                // Buat elemen link tersembunyi untuk memicu browser native download
                const link = document.createElement('a');
                link.href = downloadEndpoint;
                link.setAttribute('download', (title ? title.replace(/[^a-zA-Z0-9_\-]/g, '_') : 'salinan_putusan_ma') + '.pdf');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                // Tampilkan notifikasi toast kecil bahwa unduhan dimulai dan disimpan ke Firebase
                showToastNotification('Mengunduh salinan resmi PDF dan mencatat ke database Firebase.');
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan saat memproses unduhan PDF.');
            } finally {
                if (btnElement) {
                    setTimeout(() => {
                        btnElement.disabled = false;
                        btnElement.classList.remove('opacity-75', 'cursor-not-allowed');
                        btnElement.innerHTML = originalHtml;
                    }, 2500);
                }
            }
        }

        // Notifikasi toast interaktif
        function showToastNotification(message) {
            const existingToast = document.getElementById('app-download-toast');
            if (existingToast) existingToast.remove();

            const toast = document.createElement('div');
            toast.id = 'app-download-toast';
            toast.className = 'fixed bottom-5 right-5 z-50 bg-navy-950 text-white px-4 py-3 rounded-xl shadow-2xl border border-gold-500/30 flex items-center gap-3 text-xs animate-in slide-in-from-bottom duration-300';
            toast.innerHTML = `
                <div class="w-6 h-6 rounded-full bg-gold-500/20 text-gold-400 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-cloud-arrow-down text-xs"></i>
                </div>
                <div>
                    <p class="font-bold text-white text-[11px]">Download Sedang Berjalan</p>
                    <p class="text-slate-300 text-[10px] mt-0.5">${message}</p>
                </div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => {
                if (toast) {
                    toast.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                    setTimeout(() => toast.remove(), 500);
                }
            }, 4000);
        }

        // Buka Modal Pratinjau Perkara Langsung (Bisa baca ringkasan dulu baru buka PDF)
        async function openPratinjau(url, title) {
            if (!url) {
                alert('Tautan berkas perkara tidak valid.');
                return;
            }

            currentTargetUrl = url;
            currentDecisionTitle = title || "Pratinjau Perkara";

            const modal = document.getElementById('modal-preview');
            const titleEl = document.getElementById('preview-modal-title');
            const fullTitleEl = document.getElementById('summary-full-title');
            const loading = document.getElementById('preview-loading-state');
            const newtabBtn = document.getElementById('preview-btn-newtab');

            if (titleEl) titleEl.innerText = currentDecisionTitle;
            if (fullTitleEl) fullTitleEl.innerText = currentDecisionTitle;
            if (newtabBtn) newtabBtn.href = url;

            // Default ke tab summary
            switchPreviewTab('summary');

            // Buka modal
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
            if (loading) loading.classList.remove('hidden');

            try {
                const response = await fetch("{{ route('putusan.preview') }}?url=" + encodeURIComponent(url));
                const res = await response.json();

                if (res.success) {
                    if (res.title) {
                        currentDecisionTitle = res.title;
                        fullTitleEl.innerText = res.title;
                        titleEl.innerText = res.title;
                    }

                    // Render Kaidah jika ada
                    const kaidahSec = document.getElementById('summary-kaidah-section');
                    const kaidahCont = document.getElementById('summary-kaidah-content');
                    if (kaidahSec && kaidahCont) {
                        if (res.kaidah && res.kaidah.length > 0) {
                            kaidahSec.classList.remove('hidden');
                            kaidahCont.innerHTML = res.kaidah.map(k => `<p class="border-b border-amber-200/50 pb-2 mb-2 last:border-b-0">"${k}"</p>`).join('');
                        } else if (res.metadata && res.metadata['Kaidah Hukum']) {
                            kaidahSec.classList.remove('hidden');
                            kaidahCont.innerHTML = `<p>"${res.metadata['Kaidah Hukum']}"</p>`;
                        } else {
                            kaidahSec.classList.add('hidden');
                        }
                    }

                    // Render Metadata
                    const metaGrid = document.getElementById('summary-meta-grid');
                    if (metaGrid && res.metadata) {
                        let metaHtml = '';
                        for (const [key, val] of Object.entries(res.metadata)) {
                            if (key === 'Kaidah Hukum' || key === 'Pengantar' || key === 'Pendapat Mahkamah Agung') continue;
                            metaHtml += `
                                <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200 flex flex-col">
                                    <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">${key}</span>
                                    <span class="text-slate-800 font-medium text-xs mt-0.5">${val || '-'}</span>
                                </div>
                            `;
                        }
                        metaGrid.innerHTML = metaHtml || '<p class="text-slate-400">Tidak ada metadata tambahan.</p>';
                    }

                } else {
                    fullTitleEl.innerText = title;
                }
            } catch (err) {
                console.error(err);
            } finally {
                if (loading) loading.classList.add('hidden');
            }
        }

        // Variabel konteks perkara aktif untuk Bedah AI
        let modalAiChatHistory = [];
        let modalAiActiveUrl = '';

        // Buka langsung ke Tab Bedah AI dari tombol di kartu putusan
        function openAiBedahPutusan(url, title) {
            openPratinjau(url, title);
            setTimeout(() => {
                switchPreviewTab('ai');
            }, 80);
        }

        // Beralih antara tab ringkasan, dokumen PDF, dan Bedah AI
        function switchPreviewTab(tab) {
            const panelSummary = document.getElementById('panel-summary');
            const panelPdf = document.getElementById('panel-pdf');
            const panelAi = document.getElementById('panel-ai');

            const btnSummary = document.getElementById('tab-btn-summary');
            const btnPdf = document.getElementById('tab-btn-pdf');
            const btnAi = document.getElementById('tab-btn-ai');

            const frame = document.getElementById('preview-pdf-frame');
            const pdfLoading = document.getElementById('pdf-stream-loading');
            const newtabBtn = document.getElementById('preview-btn-newtab');

            // Reset semua panel & tombol
            if (panelSummary) panelSummary.classList.add('hidden');
            if (panelPdf) panelPdf.classList.add('hidden');
            if (panelAi) panelAi.classList.add('hidden');

            if (btnSummary) btnSummary.className = 'px-3 py-1.5 rounded-md text-slate-300 hover:text-white transition flex items-center gap-1.5';
            if (btnPdf) btnPdf.className = 'px-3 py-1.5 rounded-md text-slate-300 hover:text-white transition flex items-center gap-1.5';
            if (btnAi) btnAi.className = 'px-3 py-1.5 rounded-md text-slate-300 hover:text-white transition flex items-center gap-1.5';

            if (tab === 'summary') {
                if (panelSummary) panelSummary.classList.remove('hidden');
                if (btnSummary) btnSummary.className = 'px-3 py-1.5 rounded-md bg-navy-900 text-gold-400 shadow-sm transition flex items-center gap-1.5';
                if (newtabBtn) newtabBtn.href = currentTargetUrl;
            } else if (tab === 'pdf') {
                if (panelPdf) panelPdf.classList.remove('hidden');
                if (btnPdf) btnPdf.className = 'px-3 py-1.5 rounded-md bg-navy-900 text-gold-400 shadow-sm transition flex items-center gap-1.5';

                const streamUrl = "{{ route('putusan.pdf') }}?url=" + encodeURIComponent(currentTargetUrl);
                if (newtabBtn) newtabBtn.href = streamUrl;

                if (frame && frame.src !== streamUrl) {
                    if (pdfLoading) pdfLoading.classList.remove('hidden');
                    frame.src = streamUrl;
                }
            } else if (tab === 'ai') {
                if (panelAi) panelAi.classList.remove('hidden');
                if (btnAi) btnAi.className = 'px-3 py-1.5 rounded-md bg-gradient-to-r from-amber-500 to-gold-500 text-navy-950 font-bold shadow-sm transition flex items-center gap-1.5';
                if (newtabBtn) newtabBtn.href = currentTargetUrl;

                const caseTitleEl = document.getElementById('modal-ai-case-title');
                if (caseTitleEl) caseTitleEl.innerText = currentDecisionTitle || 'Telaah Perkara Aktif';

                // Fokuskan ke input jika ada
                setTimeout(() => {
                    const aiInput = document.getElementById('modal-ai-input');
                    if (aiInput) aiInput.focus();
                }, 150);
            }
        }

        let activeAiEngine = 'ollama'; // Default ke Ollama Llama 3.2 Lokal On-Demand

        function setAiEngine(engine) {
            activeAiEngine = engine;
            const selectEl = document.getElementById('modal-ai-engine-select');
            const iconEl = document.getElementById('engine-select-icon');
            const headerBadge = document.getElementById('modal-ai-header-badge');

            if (selectEl && selectEl.value !== engine) {
                selectEl.value = engine;
            }

            if (engine === 'gemini') {
                if (iconEl) {
                    iconEl.className = 'fa-solid fa-cloud text-amber-500 text-xs';
                }
                if (headerBadge) {
                    headerBadge.className = 'px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center gap-1 shrink-0';
                    headerBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Gemini Cloud Aktif';
                }
            } else {
                if (iconEl) {
                    iconEl.className = 'fa-solid fa-microchip text-indigo-600 text-xs';
                }
                if (headerBadge) {
                    headerBadge.className = 'px-2.5 py-1 rounded-full text-[10px] font-semibold bg-indigo-50 text-indigo-900 border border-indigo-200 flex items-center gap-1 shrink-0';
                    headerBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-indigo-600 animate-pulse"></span> Ollama Llama 3.2 Aktif';
                }
            }
        }

        // Trigger prompt cepat khusus perkara yang sedang dibedah
        function triggerModalAiPrompt(promptText) {
            if (!isAuthenticated) {
                handleTanyaAiClick();
                return;
            }
            const input = document.getElementById('modal-ai-input');
            if (input) {
                input.value = promptText;
                handleSendModalAiMessage(new Event('submit'));
            }
        }

        // Kirim pertanyaan AI khusus perkara di dalam Modal
        async function handleSendModalAiMessage(e) {
            if (e && e.preventDefault) e.preventDefault();
            if (!isAuthenticated) {
                handleTanyaAiClick();
                return;
            }

            const input = document.getElementById('modal-ai-input');
            const messagesContainer = document.getElementById('modal-ai-messages');
            if (!input || !input.value.trim()) return;

            const userQuestion = input.value.trim();
            input.value = '';

            // 1. Tampilkan Chat Pengguna
            const userHtml = `
                <div class="flex items-start justify-end gap-2.5 animate-in fade-in duration-150">
                    <div class="bg-navy-900 text-white p-3.5 rounded-2xl rounded-tr-none shadow-sm max-w-lg leading-relaxed text-xs">
                        ${userQuestion.replace(/</g, '&lt;').replace(/>/g, '&gt;')}
                    </div>
                    <div class="w-7 h-7 rounded-lg bg-gold-100 text-gold-900 font-bold flex items-center justify-center shrink-0 text-xs border border-gold-300">
                        Anda
                    </div>
                </div>`;
            messagesContainer.insertAdjacentHTML('beforeend', userHtml);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;

            // 2. Loading state AI
            const loadingId = 'modal-ai-loading-' + Date.now();
            const engineLabel = activeAiEngine === 'ollama' ? 'Ollama Llama 3.2 (Lokal)' : 'Gemini Legal AI (Cloud)';
            const loadingHtml = `
                <div id="${loadingId}" class="flex items-start gap-3 animate-in fade-in duration-150">
                    <div class="w-8 h-8 rounded-lg bg-navy-900 text-gold-400 flex items-center justify-center shrink-0 text-xs shadow-sm">
                        <i class="fa-solid fa-wand-magic-sparkles fa-spin text-gold-400"></i>
                    </div>
                    <div class="bg-white p-4 rounded-2xl rounded-tl-none border border-slate-200 shadow-xs text-slate-500 italic max-w-lg leading-relaxed text-xs flex items-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin text-gold-600"></i>
                        <span>Membedah pertimbangan hukum perkara ini bersama ${engineLabel}...</span>
                    </div>
                </div>`;
            messagesContainer.insertAdjacentHTML('beforeend', loadingHtml);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;

            try {
                const response = await fetch("{{ route('api.ai.chat') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        message: userQuestion,
                        history: modalAiChatHistory,
                        provider: activeAiEngine,
                        context_putusan: {
                            judul: currentDecisionTitle,
                            url: currentTargetUrl
                        }
                    })
                });

                const data = await response.json();
                const loadingElem = document.getElementById(loadingId);
                if (loadingElem) loadingElem.remove();

                if (data.success && data.reply) {
                    let formattedReply = parseLegalMarkdown(data.reply);

                    const botReplyHtml = `
                        <div class="flex items-start gap-3 animate-in fade-in duration-200">
                            <div class="w-8 h-8 rounded-lg bg-navy-900 text-gold-400 flex items-center justify-center shrink-0 text-xs shadow-sm">
                                <i class="fa-solid fa-scale-balanced"></i>
                            </div>
                            <div class="bg-white p-4 rounded-2xl rounded-tl-none border border-slate-200 shadow-xs text-slate-800 space-y-2 max-w-xl leading-relaxed text-xs">
                                <div class="font-bold text-navy-950 flex items-center justify-between gap-1.5 border-b border-slate-100 pb-1.5 mb-2">
                                    <span class="flex items-center gap-1.5 text-navy-900">
                                        <i class="fa-solid fa-gavel text-gold-600"></i> Telaah Yuridis Putusan
                                    </span>
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[9px] text-slate-500 font-medium bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                            ${data.model_used || 'AI Legal'}
                                        </span>
                                        <span class="text-[10px] text-emerald-600 font-normal bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 flex items-center gap-1">
                                            <i class="fa-solid fa-check text-[9px]"></i> Grounded
                                        </span>
                                    </div>
                                </div>
                                <div class="text-slate-800 space-y-2">
                                    ${formattedReply}
                                </div>
                            </div>
                        </div>`;
                    messagesContainer.insertAdjacentHTML('beforeend', botReplyHtml);

                    modalAiChatHistory.push({ role: 'user', text: userQuestion });
                    modalAiChatHistory.push({ role: 'model', text: data.reply });

                } else {
                    const errorMsg = data.message || 'Terjadi kendala saat membedah putusan.';
                    const errorHtml = `
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center shrink-0 text-xs">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <div class="bg-red-50 p-3.5 rounded-2xl rounded-tl-none border border-red-200 text-red-800 max-w-lg leading-relaxed text-xs">
                                <strong>Gagal:</strong> ${errorMsg}
                            </div>
                        </div>`;
                    messagesContainer.insertAdjacentHTML('beforeend', errorHtml);
                }

            } catch (err) {
                const loadingElem = document.getElementById(loadingId);
                if (loadingElem) loadingElem.remove();

                const errorHtml = `
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center shrink-0 text-xs">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div class="bg-red-50 p-3.5 rounded-2xl rounded-tl-none border border-red-200 text-red-800 max-w-lg leading-relaxed text-xs">
                            Gagal menghubungi server AI. Pastikan koneksi internet Anda aktif.
                        </div>
                    </div>`;
                messagesContainer.insertAdjacentHTML('beforeend', errorHtml);
            }

            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        function closePratinjau() {
            const modal = document.getElementById('modal-preview');
            const frame = document.getElementById('preview-pdf-frame');
            if (frame) frame.src = "";
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePratinjau();
            }
        });

        // Fungsi me-render kartu putusan secara instan ke DOM tanpa reload
        function renderPutusanList(items) {
            const container = document.getElementById('putusan-container');
            if (!container) return;

            if (!items || items.length === 0) {
                container.innerHTML = `
                    <div class="card-clean rounded-xl p-12 text-center space-y-3">
                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-xl">
                            <i class="fa-solid fa-folder-open"></i>
                        </div>
                        <h3 class="text-base font-bold text-navy-900">Belum Ada Data Putusan</h3>
                        <p class="text-xs text-slate-500">Klik 'Perbarui Data Putusan' untuk menarik data tahun ini.</p>
                    </div>`;
                return;
            }

            let html = '';
            items.forEach(item => {
                const isPidana = (item.klasifikasi || '').includes('Pidana');
                const isPerdata = (item.klasifikasi || '').includes('Perdata');
                const borderClass = isPidana ? 'border-l-blue-600' : (isPerdata ? 'border-l-gold-500' : 'border-l-navy-600');
                const safeTitle = (item.judul || 'Putusan Mahkamah Agung').replace(/'/g, "\\'");

                html += `
                <div class="card-clean rounded-xl p-6 transition-all space-y-4 border-l-4 ${borderClass}">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            ${item.pengadilan ? `<span class="px-2.5 py-1 rounded text-xs font-semibold bg-slate-100 text-slate-800 border border-slate-200">${item.pengadilan}</span>` : ''}
                            ${item.klasifikasi ? `<span class="px-2.5 py-1 rounded text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">${item.klasifikasi}</span>` : ''}
                        </div>
                        <span class="text-xs text-slate-500 flex items-center gap-1.5 font-mono">
                            <i class="fa-regular fa-calendar-check text-slate-400"></i> Tanggal Putus: <strong>${item.tgl_putus || '-'}</strong>
                        </span>
                    </div>

                    <div>
                        <h3 class="text-lg font-serif font-bold text-navy-950 hover:text-navy-700 cursor-pointer transition">
                            <a href="javascript:void(0)" onclick="openPratinjau('${item.url || ''}', '${safeTitle}')">
                                ${item.judul || 'Putusan Perkara Mahkamah Agung'}
                            </a>
                        </h3>
                        <p class="text-xs font-medium text-slate-500 mt-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-landmark text-gold-600 text-xs"></i>
                            <span>${item.pengadilan || 'Mahkamah Agung RI'} &bull; Direktori Putusan Resmi</span>
                        </p>
                    </div>

                    ${item.ringkasan ? `
                        <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200 text-slate-700 text-xs leading-relaxed font-serif italic">
                            "${item.ringkasan}"
                        </div>
                    ` : ''}

                    <div class="pt-2 flex flex-wrap items-center justify-between gap-4 border-t border-slate-100 text-xs">
                        <div class="flex flex-wrap items-center gap-6 text-slate-600">
                            ${item.tgl_register ? `<div><span class="text-slate-400">Register:</span> <span class="font-medium text-slate-800 ml-1">${item.tgl_register}</span></div>` : ''}
                            ${item.tgl_upload ? `<div><span class="text-slate-400">Upload:</span> <span class="font-medium text-slate-800 ml-1">${item.tgl_upload}</span></div>` : ''}
                        </div>

                        <div class="flex items-center gap-2 flex-wrap">
                            <button 
                                type="button"
                                onclick="downloadPDF('${item.url || ''}', '${safeTitle}', this)"
                                class="px-3.5 py-2 rounded-lg bg-gradient-to-r from-amber-500 to-gold-500 hover:from-amber-600 hover:to-gold-600 text-navy-950 font-bold transition flex items-center gap-1.5 shadow-xs text-xs cursor-pointer group"
                                title="Download PDF Salinan Resmi Perkara (Otomatis Dicatat ke Firebase)" >
                                <i class="fa-solid fa-download"></i> <span>Download PDF</span>
                            </button>
                            <button 
                                type="button" 
                                onclick="openAiBedahPutusan('${item.url || ''}', '${safeTitle}')" 
                                class="px-3.5 py-2 rounded-lg bg-gradient-to-r from-amber-500 to-gold-500 hover:from-amber-600 hover:to-gold-600 text-navy-950 font-bold transition flex items-center gap-1.5 shadow-xs text-xs cursor-pointer group"
                                title="Bedah pertimbangan hukum, amar, dan yurisprudensi perkara ini bersama AI"
                            >
                                <i class="fa-solid fa-wand-magic-sparkles text-navy-950 text-xs transition-transform group-hover:scale-110"></i>
                                <span>Bedah AI</span>
                            </button>
                            <button 
                                type="button" 
                                onclick="openPratinjau('${item.url || ''}', '${safeTitle}')" 
                                class="px-3.5 py-2 rounded-lg bg-navy-900 hover:bg-navy-800 text-white font-semibold transition flex items-center gap-1.5 shadow-sm text-xs"
                                title="Buka pratinjau telaah perkara dan salinan berkas"
                            >
                                <i class="fa-solid fa-eye text-gold-400 text-xs"></i>
                                <span>Pratinjau</span>
                            </button>
                            <a href="${item.url || '#'}" target="_blank" class="px-3 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition flex items-center gap-1 text-xs">
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-500"></i> Portal MA
                            </a>
                        </div>
                    </div>
                </div>`;
            });

            container.innerHTML = html;
        }

        // Buka otomatis jika diarahkan dengan parameter open_ai
        if (new URLSearchParams(window.location.search).get('open_ai') === '1') {
            handleTanyaAiClick();
        }
    </script>

</body>
</html>
