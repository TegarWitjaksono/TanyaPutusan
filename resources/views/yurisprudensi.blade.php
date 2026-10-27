<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yurisprudensi Mahkamah Agung RI - TanyaPutusan</title>
    
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
                <a href="{{ route('yurisprudensi') }}" class="text-navy-900 font-semibold border-b-2 border-gold-500 pb-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-landmark text-gold-600 text-xs"></i> Yurisprudensi
                </a>
                <a href="{{ route('tren') }}" class="hover:text-navy-900 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-chart-line text-slate-400 text-xs"></i> Tren Putusan 2026
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
                <i class="fa-solid fa-landmark"></i>
            </div>
            
            <div class="relative z-10 max-w-3xl space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gold-50 border border-gold-200 text-gold-700 text-xs font-semibold">
                    <i class="fa-solid fa-award text-gold-600"></i>
                    <span>Kompilasi Kaidah Hukum Yurisprudensi Mahkamah Agung</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-serif font-bold text-navy-900 tracking-tight leading-snug">
                    Yurisprudensi Tetap & <span class="legal-gold-text">Landmark Decisions</span>
                </h1>
                <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                    Kumpulan putusan pengadilan yang telah berkekuatan hukum tetap dan memuat pertimbangan hukum penting yang diikuti secara berulang oleh para hakim untuk memutus perkara serupa.
                </p>

                <!-- Search box -->
                <div class="pt-2 max-w-xl">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fa-solid fa-magnifying-glass text-sm"></i>
                        </span>
                        <input 
                            type="text" 
                            id="search-kaidah"
                            onkeyup="filterKaidah(this.value)"
                            placeholder="Cari kaidah hukum, asas, kata kunci pembeli beriktikad baik, korupsi..." 
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl py-3 pl-10 pr-4 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-navy-600 focus:bg-white focus:ring-2 focus:ring-navy-100 transition"
                        >
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric Badges -->
        @php
            // Hitung kategori kamar unik dari data live
            $kamarSet = [];
            foreach ($yurisprudensiList as $item) {
                $rawKamar = $item['kamar'] ?? '';
                $mainKamar = trim(explode('>', $rawKamar)[0]);
                if (!empty($mainKamar)) {
                    $kamarSet[$mainKamar] = ($kamarSet[$mainKamar] ?? 0) + 1;
                }
            }
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="card-clean p-5 rounded-xl border-l-4 border-l-gold-500">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider">Kaidah Hukum Live</span>
                    <i class="fa-solid fa-scale-balanced text-gold-600"></i>
                </div>
                <div class="text-2xl font-bold font-serif text-navy-950">{{ count($yurisprudensiList) }} Preseden</div>
                <p class="text-xs text-emerald-600 font-medium mt-1 flex items-center gap-1">
                    <i class="fa-solid fa-check text-[10px]"></i> Live Portal Mahkamah Agung
                </p>
            </div>

            <div class="card-clean p-5 rounded-xl border-l-4 border-l-navy-600">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider">Bidang Kamar Hukum</span>
                    <i class="fa-solid fa-layer-group text-blue-600"></i>
                </div>
                <div class="text-2xl font-bold font-serif text-navy-950">{{ count($kamarSet) > 0 ? count($kamarSet) : 'Seluruh' }} Bidang Kamar</div>
                <p class="text-xs text-slate-500 mt-1">Pidana, Perdata, TUN, Agama, Khusus, Militer</p>
            </div>

            <div class="card-clean p-5 rounded-xl border-l-4 border-l-emerald-600">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider">Kekuatan Hukum</span>
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                </div>
                <div class="text-2xl font-bold font-serif text-emerald-700">Inkracht Tetap</div>
                <p class="text-xs text-slate-500 mt-1">Mengikat sebagai pedoman hakim</p>
            </div>
        </div>

        <!-- Filter Pill Chips 10 Klasifikasi Resmi Direktori Mahkamah Agung -->
        <div class="card-clean p-4 rounded-xl space-y-2.5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-navy-900 uppercase tracking-wider block">
                    <i class="fa-solid fa-filter text-gold-600 mr-1"></i> Klasifikasi Kamar Direktori Mahkamah Agung:
                </span>
                <span id="active-filter-badge" class="text-xs text-slate-500 font-medium">Menampilkan Semua Klasifikasi</span>
            </div>
            <div class="flex flex-wrap items-center gap-2" id="filter-pill-container">
                <button type="button" onclick="filterKaidah('', this)" class="filter-pill px-3 py-1.5 rounded-lg text-xs font-semibold bg-navy-900 text-white shadow-sm transition">
                    Semua Klasifikasi
                </button>
                <button type="button" onclick="filterKaidah('pidana-umum', this)" class="filter-pill px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 transition">
                    <i class="fa-solid fa-handcuffs text-slate-400 mr-1"></i> Pidana Umum
                </button>
                <button type="button" onclick="filterKaidah('pidana-khusus', this)" class="filter-pill px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 transition">
                    <i class="fa-solid fa-triangle-exclamation text-slate-400 mr-1"></i> Pidana Khusus / Tipikor
                </button>
                <button type="button" onclick="filterKaidah('pidana-militer', this)" class="filter-pill px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 transition">
                    <i class="fa-solid fa-shield-halved text-slate-400 mr-1"></i> Pidana Militer
                </button>
                <button type="button" onclick="filterKaidah('perdata-umum', this)" class="filter-pill px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 transition">
                    <i class="fa-solid fa-scale-balanced text-slate-400 mr-1"></i> Perdata Umum & PMH
                </button>
                <button type="button" onclick="filterKaidah('perdata-khusus', this)" class="filter-pill px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 transition">
                    <i class="fa-solid fa-briefcase text-slate-400 mr-1"></i> Perdata Khusus & PHI
                </button>
                <button type="button" onclick="filterKaidah('perdata-agama', this)" class="filter-pill px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 transition">
                    <i class="fa-solid fa-mosque text-slate-400 mr-1"></i> Perdata Agama & Syariah
                </button>
                <button type="button" onclick="filterKaidah('tun', this)" class="filter-pill px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 transition">
                    <i class="fa-solid fa-building-columns text-slate-400 mr-1"></i> Tata Usaha Negara (TUN)
                </button>
                <button type="button" onclick="filterKaidah('pajak', this)" class="filter-pill px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 transition">
                    <i class="fa-solid fa-receipt text-slate-400 mr-1"></i> Pajak
                </button>
                <button type="button" onclick="filterKaidah('waris', this)" class="filter-pill px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 transition">
                    <i class="fa-solid fa-users text-slate-400 mr-1"></i> Waris & Tanah
                </button>
            </div>
        </div>

        <!-- List Yurisprudensi Live dari Mahkamah Agung -->
        <div class="space-y-4" id="yurisprudensi-cards">
            @forelse($yurisprudensiList as $y)
                @php
                    $kamarStr = strtolower($y['kamar'] ?? '');
                    $nomorStr = strtolower($y['nomor'] ?? '');
                    $keywordsStr = strtolower($y['keywords'] ?? '');

                    // Klasifikasi kategori terstandarisasi untuk filter cepat
                    $categoryTags = [];
                    if (str_contains($kamarStr, 'militer') || str_contains($nomorStr, 'mil')) {
                        $categoryTags[] = 'pidana-militer';
                    }
                    if (str_contains($kamarStr, 'korupsi') || str_contains($kamarStr, 'khusus') && str_contains($kamarStr, 'pidana') || str_contains($keywordsStr, 'tipikor')) {
                        $categoryTags[] = 'pidana-khusus';
                    }
                    if (str_contains($kamarStr, 'hukum pidana') && !str_contains($kamarStr, 'korupsi') && !str_contains($kamarStr, 'militer')) {
                        $categoryTags[] = 'pidana-umum';
                    }
                    if (str_contains($kamarStr, 'perdata khusus') || str_contains($kamarStr, 'phi') || str_contains($kamarStr, 'konsumen') || str_contains($kamarStr, 'arbitrase')) {
                        $categoryTags[] = 'perdata-khusus';
                    }
                    if (str_contains($kamarStr, 'acara perdata') || str_contains($kamarStr, 'melawan hukum') || (str_contains($kamarStr, 'perdata') && !str_contains($kamarStr, 'agama') && !str_contains($kamarStr, 'khusus'))) {
                        $categoryTags[] = 'perdata-umum';
                    }
                    if (str_contains($kamarStr, 'agama') || str_contains($kamarStr, 'syariah')) {
                        $categoryTags[] = 'perdata-agama';
                    }
                    if (str_contains($kamarStr, 'tata usaha negara') || str_contains($nomorStr, 'tun') || str_contains($kamarStr, 'kepegawian')) {
                        $categoryTags[] = 'tun';
                    }
                    if (str_contains($kamarStr, 'pajak') || str_contains($keywordsStr, 'pajak')) {
                        $categoryTags[] = 'pajak';
                    }
                    if (str_contains($kamarStr, 'waris') || str_contains($kamarStr, 'tanah') || str_contains($keywordsStr, 'waris') || str_contains($keywordsStr, 'tanah')) {
                        $categoryTags[] = 'waris';
                    }
                    $tagsAttribute = implode(' ', $categoryTags);
                @endphp
                <div class="card-clean rounded-xl p-6 transition-all space-y-4 border-l-4 border-l-gold-500 yuris-item" data-tags="{{ $tagsAttribute }}" data-search="{{ strtolower(($y['nomor'] ?? '') . ' ' . ($y['kamar'] ?? '') . ' ' . ($y['keywords'] ?? '') . ' ' . ($y['kaidah'] ?? '')) }}">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2.5 py-1 rounded text-xs font-semibold bg-gold-50 text-gold-800 border border-gold-200">
                                {{ $y['status'] ?? 'Yurisprudensi Resmi MA' }}
                            </span>
                            <span class="px-2.5 py-1 rounded text-xs font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                                {{ $y['kamar'] ?? 'Kaidah Hukum' }}
                            </span>
                        </div>
                        <span class="text-xs text-emerald-600 font-semibold flex items-center gap-1">
                            <i class="fa-solid fa-circle-check text-[10px]"></i> Preseden Mengikat (Inkracht)
                        </span>
                    </div>

                    <div>
                        <h3 class="text-lg font-serif font-bold text-navy-950">
                            <a href="javascript:void(0)" onclick="openPratinjau('{{ $y['url'] ?? '' }}', '{{ addslashes($y['nomor'] ?? 'Yurisprudensi MA') }}')" class="hover:text-gold-700 transition cursor-pointer">
                                {{ $y['nomor'] ?? 'Yurisprudensi MA' }}
                            </a>
                        </h3>
                        @if(!empty($y['keywords']))
                            <p class="text-xs text-slate-500 mt-1">
                                <span class="font-semibold text-slate-700">Kata Kunci:</span> {{ $y['keywords'] }}
                            </p>
                        @endif
                    </div>

                    <!-- Kaidah Hukum Quote -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-sm leading-relaxed font-serif italic border-l-4 border-l-gold-500">
                        <span class="font-bold font-sans not-italic text-xs text-gold-700 uppercase tracking-wider block mb-1.5">
                            <i class="fa-solid fa-quote-left mr-1"></i> Intisari Kaidah Hukum:
                        </span>
                        "{{ $y['kaidah'] ?? '-' }}"
                    </div>

                    <div class="pt-2 flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 text-xs">
                        <span class="text-slate-500">Direktori Resmi Mahkamah Agung Republik Indonesia</span>
                        <div class="flex items-center gap-2">
                            <button 
                                type="button" 
                                onclick="openAiBedahPutusan('{{ $y['url'] ?? '' }}', '{{ addslashes($y['nomor'] ?? 'Yurisprudensi Mahkamah Agung') }}')" 
                                class="px-3.5 py-1.5 rounded-lg bg-gradient-to-r from-amber-500 to-gold-500 hover:from-amber-600 hover:to-gold-600 text-navy-950 font-bold transition flex items-center gap-1.5 shadow-xs text-xs cursor-pointer group"
                                title="Bedah kaidah hukum, ratio decidendi, dan amar perkara ini bersama AI"
                            >
                                <i class="fa-solid fa-wand-magic-sparkles text-navy-950 text-xs transition-transform group-hover:scale-110"></i>
                                <span>Bedah AI</span>
                            </button>
                            <button 
                                type="button" 
                                onclick="openPratinjau('{{ $y['url'] ?? '' }}', '{{ addslashes($y['nomor'] ?? 'Yurisprudensi Mahkamah Agung') }}')" 
                                class="px-3.5 py-1.5 rounded-lg bg-navy-900 hover:bg-navy-800 text-white font-semibold transition flex items-center gap-1.5 shadow-sm text-xs"
                                title="Buka pratinjau kaidah dan telaah berkas perkara"
                            >
                                <i class="fa-solid fa-eye text-gold-400 text-xs"></i>
                                <span>Pratinjau Putusan</span>
                            </button>
                            <a href="{{ $y['url'] ?? '#' }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition flex items-center gap-1 text-xs">
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-500"></i> Portal MA
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card-clean rounded-xl p-10 text-center text-slate-500">
                    Belum ada data yurisprudensi termuat.
                </div>
            @endforelse

            <!-- State Kosong Hasil Filter -->
            <div id="no-filter-results" class="hidden card-clean rounded-2xl p-10 text-center space-y-4 border-2 border-dashed border-slate-200">
                <div class="w-14 h-14 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-xl">
                    <i class="fa-solid fa-filter-circle-xmark"></i>
                </div>
                <div>
                    <h4 class="text-base font-bold text-navy-900 font-serif">Tidak Ada Kaidah Hukum yang Cocok</h4>
                    <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                        Tidak ditemukan preseden untuk klasifikasi atau kata kunci ini. Silakan coba klasifikasi lain atau kembalikan ke semua daftar yurisprudensi.
                    </p>
                </div>
                <div>
                    <button type="button" onclick="filterKaidah('', null)" class="px-4 py-2 rounded-lg bg-navy-900 text-white hover:bg-navy-800 text-xs font-semibold shadow-sm transition inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-rotate-left text-gold-400"></i> Tampilkan Semua Yurisprudensi
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Pratinjau Putusan & Yurisprudensi (In-App Document Previewer) -->
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
                        <!-- Tab Switcher: Ringkasan vs PDF -->
                        <div class="inline-flex rounded-lg bg-navy-800 p-1 border border-navy-700 text-xs font-semibold mr-2">
                            <button 
                                id="tab-btn-summary" 
                                onclick="switchPreviewTab('summary')" 
                                class="px-3 py-1.5 rounded-md bg-navy-900 text-gold-400 shadow-sm transition flex items-center gap-1.5"
                            >
                                <i class="fa-solid fa-file-lines text-xs"></i>
                                <span>Ringkasan Perkara</span>
                            </button>
                            <button 
                                id="tab-btn-pdf" 
                                onclick="switchPreviewTab('pdf')" 
                                class="px-3 py-1.5 rounded-md text-slate-300 hover:text-white transition flex items-center gap-1.5"
                            >
                                <i class="fa-solid fa-file-pdf text-red-400 text-xs"></i>
                                <span>Buka Dokumen PDF</span>
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

                <!-- Modal Body: 2 Tampilan (Ringkasan vs PDF) -->
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

                        <!-- Sumber Putusan Utama & Penjelasan PDF -->
                        <div id="summary-sumber-section" class="bg-blue-50/80 border-l-4 border-l-navy-800 p-5 rounded-xl border border-blue-200/80 shadow-sm space-y-3 hidden">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h4 class="text-xs font-bold text-navy-950 uppercase tracking-wider flex items-center gap-1.5">
                                        <i class="fa-solid fa-landmark text-navy-800"></i> Sumber Landasan Perkara Putusan (Naskah PDF)
                                    </h4>
                                    <p class="text-xs text-slate-600 mt-1">
                                        Yurisprudensi adalah kompilasi kaidah hukum tetap. Berkas dokumen PDF yang terlampir adalah salinan putusan sumber yang menjadi landasan kaidah ini.
                                    </p>
                                </div>
                            </div>
                            <div id="summary-sumber-badge" class="flex flex-wrap items-center gap-2 pt-1">
                                <!-- Terisi via JS -->
                            </div>
                        </div>

                        <!-- Kaidah Hukum Block (Jika ada) -->
                        <div id="summary-kaidah-section" class="bg-amber-50/70 border-l-4 border-l-gold-500 p-5 rounded-xl border border-gold-200/80 shadow-sm space-y-2 hidden">
                            <h4 class="text-xs font-bold text-gold-900 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fa-solid fa-quote-left text-gold-600"></i> Kaidah Hukum & Pertimbangan Tetap
                            </h4>
                            <div id="summary-kaidah-content" class="text-sm text-slate-800 font-serif italic leading-relaxed space-y-2">
                            </div>
                        </div>

                        <!-- Pendapat Mahkamah Agung / Catatan Doktrin -->
                        <div id="summary-pendapat-section" class="bg-slate-50 border border-slate-200 p-5 rounded-xl shadow-sm space-y-2 hidden">
                            <h4 class="text-xs font-bold text-navy-900 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fa-solid fa-gavel text-gold-600"></i> Pertimbangan / Pendapat Mahkamah Agung
                            </h4>
                            <div id="summary-pendapat-content" class="text-xs text-slate-700 leading-relaxed font-serif max-h-48 overflow-y-auto pr-2">
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

                        <!-- Putusan Yang Mengikuti (Preseden Serupa) -->
                        <div id="summary-terkait-section" class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-3 hidden">
                            <h4 class="text-xs font-bold text-navy-900 uppercase tracking-wider flex items-center gap-2">
                                <i class="fa-solid fa-list-check text-emerald-600"></i> Putusan Pengadilan Yang Mengikuti Kaidah Ini
                            </h4>
                            <p class="text-xs text-slate-500">
                                Putusan-putusan kasasi/PK lain yang menerapkan kaidah hukum yurisprudensi ini:
                            </p>
                            <div id="summary-terkait-list" class="flex flex-wrap gap-2 pt-1">
                                <!-- Terisi via JS -->
                            </div>
                        </div>

                        <!-- Tombol Aksi Lanjut ke PDF & Bedah AI -->
                        <div class="p-4 rounded-xl bg-slate-100 border border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="text-xs text-slate-600">
                                <span class="font-semibold text-slate-800">Pelajari lebih dalam perkara ini:</span>
                                <p class="text-slate-500" id="summary-pdf-note">Buka salinan PDF lengkap atau diskusikan pertimbangan hukum dengan AI.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button 
                                    type="button" 
                                    onclick="switchPreviewTab('ai')" 
                                    class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-gold-500 hover:from-amber-600 hover:to-gold-600 text-navy-950 font-bold transition flex items-center gap-2 shadow-sm text-xs shrink-0 cursor-pointer"
                                >
                                    <i class="fa-solid fa-wand-magic-sparkles text-navy-950"></i>
                                    <span>Bedah AI</span>
                                </button>
                                <button 
                                    type="button" 
                                    onclick="switchPreviewTab('pdf')" 
                                    class="px-4 py-2.5 rounded-xl bg-navy-900 hover:bg-navy-800 text-white font-semibold transition flex items-center gap-2 shadow-sm text-xs shrink-0 cursor-pointer"
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
                            <span id="modal-ai-header-badge" class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-indigo-50 text-indigo-900 border border-indigo-200 flex items-center gap-1 shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 animate-pulse"></span>
                                Ollama Llama 3.2 Aktif
                            </span>
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
                                        Halo! Saya siap membedah dokumen yurisprudensi putusan ini untuk Anda.
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
                                            onclick="triggerModalAiPrompt('Jelaskan konstruksi pertimbangan hukum hakim (ratio decidendi) dalam menetapkan kaidah hukum ini.')" 
                                            class="px-3 py-1.5 rounded-lg bg-slate-50 hover:bg-gold-50 hover:text-gold-950 text-slate-700 text-[11px] font-medium border border-slate-200 transition flex items-center gap-1.5 cursor-pointer"
                                        >
                                            <i class="fa-solid fa-brain text-purple-600 text-xs"></i>
                                            <span>Pertimbangan Hakim</span>
                                        </button>
                                        <button 
                                            type="button" 
                                            onclick="triggerModalAiPrompt('Apakah perkara ini menjadi yurisprudensi tetap Mahkamah Agung dan bagaimana penerapannya dalam praktik peradilan?')" 
                                            class="px-3 py-1.5 rounded-lg bg-slate-50 hover:bg-gold-50 hover:text-gold-950 text-slate-700 text-[11px] font-medium border border-slate-200 transition flex items-center gap-1.5 cursor-pointer"
                                        >
                                            <i class="fa-solid fa-scroll text-blue-600 text-xs"></i>
                                            <span>Penerapan Yurisprudensi</span>
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
                                                <option value="ollama" selected>⚡ Ollama (Llama 3.2)</option>
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
                                        <span>Masuk akun untuk bertanya bebas dan membedah yurisprudensi ini bersama AI.</span>
                                    </div>
                                    <a 
                                        href="{{ route('login') }}" 
                                        class="px-3.5 py-1.5 rounded-lg bg-navy-900 text-white font-bold text-xs hover:bg-navy-800 transition shadow-sm shrink-0 cursor-pointer"
                                    >
                                        Masuk Akun
                                    </a>
                                </div>
                            @endauth
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="mt-16 border-t border-slate-200 bg-white text-slate-500 text-xs py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-scale-balanced text-gold-600 text-base"></i>
                <span class="font-serif text-navy-900 font-bold tracking-wider">TANYAPUTUSAN</span>
                <span>&bull; Portal Riset Hukum & Yurisprudensi Mahkamah Agung RI</span>
            </div>
        </div>
    </footer>

    <script>
        let currentTargetUrl = '';
        let currentDecisionTitle = '';

        let activeTag = '';

        function filterKaidah(keyword, btnElement) {
            const searchInput = document.getElementById('search-kaidah');
            const items = document.querySelectorAll('.yuris-item');
            const noResults = document.getElementById('no-filter-results');
            const activeFilterBadge = document.getElementById('active-filter-badge');

            // Jika dipanggil dari tombol kategori (btnElement ada atau keyword adalah tag tertentu)
            if (btnElement !== undefined) {
                // Update active pill button styling
                const pills = document.querySelectorAll('.filter-pill');
                pills.forEach(p => {
                    p.className = 'filter-pill px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 transition';
                });

                if (btnElement) {
                    btnElement.className = 'filter-pill px-3 py-1.5 rounded-lg text-xs font-semibold bg-navy-900 text-white shadow-sm transition';
                    activeTag = keyword;
                } else {
                    // Reset ke pill pertama (Semua Klasifikasi)
                    const firstPill = document.querySelector('.filter-pill');
                    if (firstPill) firstPill.className = 'filter-pill px-3 py-1.5 rounded-lg text-xs font-semibold bg-navy-900 text-white shadow-sm transition';
                    activeTag = '';
                    if (searchInput) searchInput.value = '';
                }
            }

            // Ambil query dari teks pencarian
            const textQuery = searchInput ? searchInput.value.toLowerCase().trim() : '';

            let matchCount = 0;
            items.forEach(el => {
                const tags = (el.getAttribute('data-tags') || '').split(' ');
                const searchableText = (el.getAttribute('data-search') || el.innerText).toLowerCase();

                let tagMatches = true;
                if (activeTag) {
                    tagMatches = tags.includes(activeTag);
                }

                let textMatches = true;
                if (textQuery) {
                    textMatches = searchableText.includes(textQuery);
                }

                if (tagMatches && textMatches) {
                    el.style.display = '';
                    matchCount++;
                } else {
                    el.style.display = 'none';
                }
            });

            // Update badge dan state kosong
            if (activeFilterBadge) {
                if (!activeTag && !textQuery) {
                    activeFilterBadge.innerText = `Menampilkan Semua (${matchCount} Preseden)`;
                } else {
                    activeFilterBadge.innerText = `Ditemukan ${matchCount} Preseden Cocok`;
                }
            }

            if (noResults) {
                if (matchCount === 0) {
                    noResults.classList.remove('hidden');
                } else {
                    noResults.classList.add('hidden');
                }
            }
        }

        // Buka Modal Pratinjau Perkara
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
                        fullTitleEl.innerText = res.title;
                        titleEl.innerText = res.title;
                    }

                    // Render Sumber Putusan Landasan (Penting: Menjelaskan nomor putusan asli di file PDF)
                    const sumberSec = document.getElementById('summary-sumber-section');
                    const sumberBadge = document.getElementById('summary-sumber-badge');
                    const pdfNote = document.getElementById('summary-pdf-note');
                    if (sumberSec && sumberBadge) {
                        if (res.sumber_putusan && res.sumber_putusan.length > 0) {
                            sumberSec.classList.remove('hidden');
                            sumberBadge.innerHTML = res.sumber_putusan.map(s => `
                                <span class="px-3 py-1.5 rounded-lg bg-navy-900 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm">
                                    <i class="fa-solid fa-file-pdf text-red-400"></i>
                                    <span>Putusan Landasan Asli: ${s.nomor}</span>
                                </span>
                            `).join('');
                            if (pdfNote) {
                                const nomorAsli = res.sumber_putusan.map(s => s.nomor).join(', ');
                                pdfNote.innerHTML = `Dokumen PDF yang dibuka adalah salinan resmi putusan landasan: <strong class="text-navy-900">${nomorAsli}</strong>.`;
                            }
                        } else {
                            sumberSec.classList.add('hidden');
                            if (pdfNote) pdfNote.innerText = "Membuka salinan resmi putusan landasan dari server Mahkamah Agung.";
                        }
                    }

                    // Render Kaidah
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

                    // Render Pendapat Mahkamah Agung jika ada
                    const pendapatSec = document.getElementById('summary-pendapat-section');
                    const pendapatCont = document.getElementById('summary-pendapat-content');
                    if (pendapatSec && pendapatCont) {
                        const pendapatText = res.metadata && (res.metadata['Pendapat Mahkamah Agung'] || res.metadata['Pengantar'] || res.metadata['Yurisprudensi']);
                        if (pendapatText) {
                            pendapatSec.classList.remove('hidden');
                            pendapatCont.innerHTML = `<p class="leading-relaxed whitespace-pre-line">${pendapatText}</p>`;
                        } else {
                            pendapatSec.classList.add('hidden');
                        }
                    }

                    // Render Metadata
                    const metaGrid = document.getElementById('summary-meta-grid');
                    if (metaGrid && res.metadata) {
                        let metaHtml = '';
                        for (const [key, val] of Object.entries(res.metadata)) {
                            if (['Kaidah Hukum', 'Pengantar', 'Pendapat Mahkamah Agung', 'Yurisprudensi'].includes(key)) continue;
                            metaHtml += `
                                <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200 flex flex-col">
                                    <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">${key}</span>
                                    <span class="text-slate-800 font-medium text-xs mt-0.5">${val || '-'}</span>
                                </div>
                            `;
                        }
                        metaGrid.innerHTML = metaHtml || '<p class="text-slate-400">Tidak ada metadata tambahan.</p>';
                    }

                    // Render Putusan Yang Mengikuti
                    const terkaitSec = document.getElementById('summary-terkait-section');
                    const terkaitList = document.getElementById('summary-terkait-list');
                    if (terkaitSec && terkaitList) {
                        const listToRender = (res.putusan_mengikuti && res.putusan_mengikuti.length > 0) ? res.putusan_mengikuti : (res.putusan_terkait || []);
                        if (listToRender.length > 0) {
                            terkaitSec.classList.remove('hidden');
                            terkaitList.innerHTML = listToRender.map(pt => `
                                <button 
                                    type="button" 
                                    onclick="openPratinjau('${pt.href}', '${pt.nomor}')" 
                                    class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs border border-slate-200 transition flex items-center gap-1.5"
                                >
                                    <i class="fa-solid fa-file-lines text-gold-600 text-[10px]"></i>
                                    <span>${pt.nomor}</span>
                                </button>
                            `).join('');
                        } else {
                            terkaitSec.classList.add('hidden');
                        }
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
        let activeAiEngine = 'ollama'; // Default ke Ollama Llama 3.2 Lokal On-Demand
        const isAuthenticated = @json(Auth::check());

        function parseLegalMarkdown(text) {
            if (!text) return '';
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

                if (/^#{1,6}\s+(.*)/.test(line)) {
                    if (inList) {
                        html += '</ul>';
                        inList = false;
                    }
                    let headingText = line.replace(/^#{1,6}\s+/, '');
                    html += `<h4 class="font-bold text-navy-950 text-xs tracking-wide uppercase mt-3 mb-1 border-b border-slate-200/80 pb-1 flex items-center gap-1.5"><i class="fa-solid fa-scale-balanced text-gold-600 text-[10px]"></i> ${headingText}</h4>`;
                    continue;
                }

                if (/^[\*\-]\s+(.*)/.test(line)) {
                    if (!inList) {
                        html += '<ul class="list-disc list-inside space-y-1.5 my-1.5 text-slate-700 pl-1">';
                        inList = true;
                    }
                    let itemText = line.replace(/^[\*\-]\s+/, '');
                    html += `<li>${itemText}</li>`;
                    continue;
                }

                if (/^\d+\.\s+(.*)/.test(line)) {
                    if (inList) {
                        html += '</ul>';
                        inList = false;
                    }
                    let numText = line.replace(/^\d+\.\s+/, '');
                    html += `<div class="flex items-start gap-2 my-1 text-slate-700"><span class="font-bold text-gold-700 shrink-0">&bull;</span><span>${numText}</span></div>`;
                    continue;
                }

                if (inList) {
                    html += '</ul>';
                    inList = false;
                }

                html += `<p class="leading-relaxed mb-2 text-slate-700">${line}</p>`;
            }

            if (inList) html += '</ul>';

            html = html.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-navy-950">$1</strong>');
            html = html.replace(/\*([^\*]+)\*/g, '<em class="italic text-slate-800">$1</em>');
            html = html.replace(/\b_([^_]+)_\b/g, '<em class="italic text-slate-800">$1</em>');
            html = html.replace(/`([^`]+)`/g, '<span class="px-1 py-0.5 rounded bg-slate-100 text-navy-900 font-mono text-[11px] border border-slate-200">$1</span>');

            return html;
        }

        // Buka langsung ke Tab Bedah AI dari tombol di kartu putusan yurisprudensi
        function openAiBedahPutusan(url, title) {
            openPratinjau(url, title);
            setTimeout(() => {
                switchPreviewTab('ai');
            }, 80);
        }

        function setAiEngine(engine) {
            activeAiEngine = engine;
            const selectEl = document.getElementById('modal-ai-engine-select');
            const iconEl = document.getElementById('engine-select-icon');
            const headerBadge = document.getElementById('modal-ai-header-badge');

            if (selectEl && selectEl.value !== engine) {
                selectEl.value = engine;
            }

            if (engine === 'gemini') {
                if (iconEl) iconEl.className = 'fa-solid fa-cloud text-amber-500 text-xs';
                if (headerBadge) {
                    headerBadge.className = 'px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center gap-1 shrink-0';
                    headerBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Gemini Cloud Aktif';
                }
            } else {
                if (iconEl) iconEl.className = 'fa-solid fa-microchip text-indigo-600 text-xs';
                if (headerBadge) {
                    headerBadge.className = 'px-2.5 py-1 rounded-full text-[10px] font-semibold bg-indigo-50 text-indigo-900 border border-indigo-200 flex items-center gap-1 shrink-0';
                    headerBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-indigo-600 animate-pulse"></span> Ollama Llama 3.2 Aktif';
                }
            }
        }

        function triggerModalAiPrompt(promptText) {
            if (!isAuthenticated) {
                window.location.href = "{{ route('login') }}";
                return;
            }
            const input = document.getElementById('modal-ai-input');
            if (input) {
                input.value = promptText;
                handleSendModalAiMessage(new Event('submit'));
            }
        }

        async function handleSendModalAiMessage(e) {
            if (e && e.preventDefault) e.preventDefault();
            if (!isAuthenticated) {
                window.location.href = "{{ route('login') }}";
                return;
            }

            const input = document.getElementById('modal-ai-input');
            const messagesContainer = document.getElementById('modal-ai-messages');
            if (!input || !input.value.trim()) return;

            const userQuestion = input.value.trim();
            input.value = '';

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

            const loadingId = 'modal-ai-loading-' + Date.now();
            const engineLabel = activeAiEngine === 'ollama' ? 'Ollama Llama 3.2 (Lokal)' : 'Gemini Legal AI (Cloud)';
            const loadingHtml = `
                <div id="${loadingId}" class="flex items-start gap-3 animate-in fade-in duration-150">
                    <div class="w-8 h-8 rounded-lg bg-navy-900 text-gold-400 flex items-center justify-center shrink-0 text-xs shadow-sm">
                        <i class="fa-solid fa-wand-magic-sparkles fa-spin text-gold-400"></i>
                    </div>
                    <div class="bg-white p-4 rounded-2xl rounded-tl-none border border-slate-200 shadow-xs text-slate-500 italic max-w-lg leading-relaxed text-xs flex items-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin text-gold-600"></i>
                        <span>Membedah pertimbangan hukum yurisprudensi ini bersama ${engineLabel}...</span>
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
                                        <i class="fa-solid fa-gavel text-gold-600"></i> Telaah Yuridis Yurisprudensi
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
                            Gagal menghubungi server. Pastikan koneksi internet Anda aktif.
                        </div>
                    </div>`;
                messagesContainer.insertAdjacentHTML('beforeend', errorHtml);
            }

            messagesContainer.scrollTop = messagesContainer.scrollHeight;
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

                // Load stream PDF jika belum dimuat atau berbeda URL
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

                setTimeout(() => {
                    const aiInput = document.getElementById('modal-ai-input');
                    if (aiInput) aiInput.focus();
                }, 150);
            }
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
    </script>
</body>
</html>
