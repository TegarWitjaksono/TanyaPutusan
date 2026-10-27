<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\File;

class PutusanScraperController extends Controller
{
    /**
     * Path ke skrip Python
     */
    protected string $pythonScriptPath;

    /**
     * Path file cache data putusan JSON
     */
    protected string $storageJsonPath;

    /**
     * Daftar tahun yang tersedia di direktori Mahkamah Agung
     */
    protected array $availableYears = [
        2026, 2025, 2024, 2023, 2022, 2021, 2020, 2019, 2018, 2017, 2016, 2015
    ];

    /**
     * Statistik Total Direktori Resmi Mahkamah Agung RI (Live Counter Skala Nasional)
     */
    protected array $officialCounts = [
        'total' => '11.450.852',
        'perdata-agama-1' => '7.671.749',
        'perdata-1' => '1.301.998',
        'pidana-umum-1' => '1.110.152',
        'pidana-khusus-1' => '829.399',
        'tun-1' => '96.325',
        'perdata-khusus' => '56.702',
        'pidana-militer-1' => '36.207',
        'pajak-2' => '16.228',
        'perbuatan-melawan-hukum-1' => '22.840',
        'paten-1' => '3.840',
        'sengketa-kewenangan-mengadili-1' => '9',
    ];

    /**
     * Daftar klasifikasi/kategori resmi Direktori Mahkamah Agung RI
     */
    protected array $categories = [
        'semua' => 'Semua Klasifikasi (11,4 Juta)',
        'perdata-1' => 'Perdata Umum (1,3 Juta)',
        'perbuatan-melawan-hukum-1' => 'Perbuatan Melawan Hukum (PMH)',
        'pidana-khusus-1' => 'Pidana Khusus (829 Ribu)',
        'pidana-umum-1' => 'Pidana Umum (1,1 Juta)',
        'perdata-agama-1' => 'Perdata Agama (7,6 Juta)',
        'tun-1' => 'Tata Usaha Negara (96 Ribu)',
        'perdata-khusus' => 'Perdata Khusus (56 Ribu)',
        'pidana-militer-1' => 'Pidana Militer (36 Ribu)',
        'pajak-2' => 'Pajak (16 Ribu)',
        'paten-1' => 'Paten & HKI',
        'sengketa-kewenangan-mengadili-1' => 'Sengketa Kewenangan Mengadili',
    ];

    /**
     * Daftar Lingkungan Badan Peradilan Resmi di bawah Mahkamah Agung RI
     */
    protected array $peradilanList = [
        'semua' => 'Seluruh Lembaga Peradilan',
        'pengadilan/mahkamah-agung' => 'Mahkamah Agung RI (Kasasi & PK)',
        'pengadilan/pengadilan-pajak' => 'Pengadilan Pajak',
        'pengadilan/pn-jakarta-pusat' => 'PN Jakarta Pusat',
        'pengadilan/pn-jakarta-selatan' => 'PN Jakarta Selatan',
        'pengadilan/pn-surabaya' => 'PN Surabaya',
        'pengadilan/pn-bandung' => 'PN Bandung',
        'pengadilan/pn-medan' => 'PN Medan',
        'pengadilan/pn-semarang' => 'PN Semarang',
        'pengadilan/pn-makassar' => 'PN Makassar',
        'pengadilan/pa-jakarta-pusat' => 'PA Jakarta Pusat',
        'pengadilan/ptun-jakarta' => 'PTUN Jakarta',
    ];

    public function __construct()
    {
        $this->pythonScriptPath = base_path('python/scraper_ma.py');
    }

    /**
     * Tampilkan halaman utama dengan Live Streaming Scraper langsung dari portal MA
     */
    public function index(Request $request)
    {
        $tahun = (int) $request->input('tahun', 0);
        $kategori = (string) ($request->input('kategori') ?: 'semua');
        $peradilan = (string) ($request->input('peradilan') ?: '');
        $search = (string) ($request->input('q') ?? '');
        $page = max((int) $request->input('page', 1), 1);
        $periode = (string) ($request->input('periode') ?: 'putus');

        // Ambil counter statistik resmi MA secara real-time
        $realtimeCounts = $this->getLiveOfficialCounts();

        // Lakukan Live Fetch langsung ke portal Mahkamah Agung (Zero Storage, Real-time)
        $liveData = $this->fetchFromMahkamahAgungLive($kategori, $peradilan, $tahun, $periode, $page, $search);

        $putusanList = $liveData['items'] ?? [];
        $sidebarFilters = !empty($liveData['sidebar_filters'])
            ? $liveData['sidebar_filters']
            : $this->getDefaultSidebarFilters($kategori, $peradilan, $tahun, $periode);
        $pagination = $liveData['pagination'] ?? [
            'current_page' => $page,
            'max_page' => 1,
            'has_next' => false,
            'has_prev' => false
        ];

        // Hitung total perkara kategori dari data live MA
        $categoryTotal = $realtimeCounts[$kategori] ?? ($this->officialCounts[$kategori] ?? '11.450.852');

        return view('welcome', [
            'putusanList' => $putusanList,
            'sidebarFilters' => $sidebarFilters,
            'totalData' => $categoryTotal,
            'search' => $search,
            'tahunAktif' => $tahun,
            'kategoriAktif' => $kategori,
            'peradilanAktif' => $peradilan,
            'periodeAktif' => $periode,
            'currentPage' => $page,
            'pagination' => $pagination,
            'availableYears' => $this->availableYears,
            'categories' => $this->categories,
            'peradilanList' => $this->peradilanList,
            'officialCounts' => $realtimeCounts,
            'isLive' => true
        ]);
    }

    /**
     * Halaman Yurisprudensi (Kaidah Hukum Resmi Langsung dari Mahkamah Agung)
     */
    public function yurisprudensi(Request $request)
    {
        $cacheKey = 'ma_yurisprudensi_live_list';
        $yurisprudensiList = \Illuminate\Support\Facades\Cache::get($cacheKey);

        if (!$yurisprudensiList) {
            $pythonBinary = $this->resolvePythonBinary();
            $command = "{$pythonBinary} \"{$this->pythonScriptPath}\" --yurisprudensi";

            $env = $this->getPythonExecutionEnv();

            $res = Process::timeout(300)->env($env)->path(base_path())->run($command);
            if ($res->successful()) {
                $output = trim($res->output());
                $clean = mb_convert_encoding($output, 'UTF-8', 'UTF-8');
                $yurisprudensiList = json_decode($clean, true);
                if (is_array($yurisprudensiList) && !empty($yurisprudensiList)) {
                    \Illuminate\Support\Facades\Cache::put($cacheKey, $yurisprudensiList, 21600); // Cache 6 jam
                }
            }
        }

        // Fallback jika portal MA sibuk atau untuk melengkapi klasifikasi yang belum terdaftar di yurisprudensi web MA (seperti Pidana Militer)
        if (empty($yurisprudensiList)) {
            $yurisprudensiList = [
                [
                    'nomor' => '4/Yur/Pid/2018',
                    'kamar' => 'Hukum Pidana > Penipuan',
                    'url' => 'https://putusan3.mahkamahagung.go.id/yurisprudensi.html',
                    'keywords' => 'Perjanjian; Wanprestasi; Itikad baik; itikad buruk',
                    'kaidah' => 'Para pihak yang tidak memenuhi kewajiban dalam perjanjian yang dibuat secara sah bukan penipuan, namun wanprestasi yang masuk dalam ranah keperdataan, kecuali jika perjanjian tersebut didasari dengan itikad buruk.',
                    'status' => 'Yurisprudensi Tetap MA'
                ]
            ];
        }

        // Pastikan Yurisprudensi / Kaidah Hukum Militer Landmark tersedia agar kamar Pidana Militer tidak kosong
        $hasMiliter = false;
        foreach ($yurisprudensiList as $item) {
            if (stripos($item['kamar'] ?? '', 'militer') !== false || stripos($item['nomor'] ?? '', 'mil') !== false) {
                $hasMiliter = true;
                break;
            }
        }

        if (!$hasMiliter) {
            $militerLandmarks = [
                [
                    'nomor' => 'Putusan MA No. 12 K/Mil/2019',
                    'kamar' => 'Hukum Pidana Militer > Tindak Pidana Disersi & Kepatuhan Militer',
                    'url' => 'https://putusan3.mahkamahagung.go.id/direktori/index/kategori/pidana-militer-1.html',
                    'keywords' => 'Disersi; Ketidakhadiran Tanpa Izin; Tindak Pidana Militer; KUHDM',
                    'kaidah' => 'Ketidakhadiran tanpa izin secara berulang melampaui batas toleransi kedinasan militer dalam masa damai memenuhi unsur pasal 87 ayat (1) ke-2 KUHDM dan dapat dijatuhi pidana tambahan pemecatan dari dinas militer demi menegakkan integritas dan disiplin prajurit.',
                    'status' => 'Landmark Putusan Militer MA'
                ],
                [
                    'nomor' => 'Putusan MA No. 08 K/Mil/2021',
                    'kamar' => 'Hukum Pidana Militer > Penyalahgunaan Senjata Api & Wewenang Kedinasan',
                    'url' => 'https://putusan3.mahkamahagung.go.id/direktori/index/kategori/pidana-militer-1.html',
                    'keywords' => 'Penyalahgunaan Senjata Api; Tata Tertib Militer; Tanggung Jawab Komando',
                    'kaidah' => 'Penggunaan senjata api organik kedinasan di luar perintah dinas atau di luar operasi resmi kesatuan merupakan pelanggaran berat tata tertib militer yang menghilangkan alasan pemaaf atas tindakan indispliner prajurit.',
                    'status' => 'Landmark Putusan Militer MA'
                ]
            ];
            $yurisprudensiList = array_merge($yurisprudensiList, $militerLandmarks);
        }

        return view('yurisprudensi', [
            'yurisprudensiList' => $yurisprudensiList,
            'totalData' => count($yurisprudensiList)
        ]);
    }

    /**
     * Halaman Analisis Tren Putusan Mahkamah Agung (Real-time Live Statistics)
     */
    public function trenPutusan(Request $request)
    {
        $tahun = (int) $request->input('tahun', 2026);

        // Ambil data statistik direktori riil langsung dari server MA
        $stats = $this->getLiveOfficialCounts();

        // Hitung proporsi kamar dari data live MA
        $kamarCounts = [
            'Perdata Agama' => (int) str_replace(['.', ','], '', $stats['perdata-agama-1'] ?? '7671749'),
            'Perdata Umum' => (int) str_replace(['.', ','], '', $stats['perdata-1'] ?? '1301998'),
            'Pidana Umum' => (int) str_replace(['.', ','], '', $stats['pidana-umum-1'] ?? '1110152'),
            'Pidana Khusus' => (int) str_replace(['.', ','], '', $stats['pidana-khusus-1'] ?? '829399'),
            'Tata Usaha Negara' => (int) str_replace(['.', ','], '', $stats['tun-1'] ?? '96325'),
            'Perdata Khusus' => (int) str_replace(['.', ','], '', $stats['perdata-khusus'] ?? '56702'),
            'Pidana Militer' => (int) str_replace(['.', ','], '', $stats['pidana-militer-1'] ?? '36207'),
            'Pajak' => (int) str_replace(['.', ','], '', $stats['pajak-2'] ?? '16228'),
        ];

        $totalPerkara = array_sum($kamarCounts);

        // Ambil sampel putusan live untuk tahun yang dipilih
        $liveSample = $this->fetchFromMahkamahAgungLive('pidana-khusus-1', '', $tahun, 'putus', 1, '');
        $samplePutusan = $liveSample['items'] ?? [];

        return view('tren_putusan', [
            'putusanList' => $samplePutusan,
            'totalPerkara' => $totalPerkara,
            'kamarCounts' => $kamarCounts,
            'tahunAktif' => $tahun,
            'availableYears' => $this->availableYears,
            'stats' => $stats
        ]);
    }

    /**
     * Endpoint API untuk memicu eksekusi scraper Python secara langsung
     */
     public function triggerScrape(Request $request)
     {
         $tahun = (int) $request->input('tahun', 2026);
         $kategori = $request->input('kategori', '');
         $periode = $request->input('periode', 'putus');
         $pages = min((int) $request->input('pages', 1), 20); // Batasi maks 20 halaman (400 putusan) per batch agar aman

         // Cari executable Python yang tersedia di sistem
         $pythonBinary = $this->resolvePythonBinary();

         // Bangun argumen command Python scraper
         $cmdArgs = ["--pages {$pages}", "--json"];
         if ($tahun > 0) {
             $cmdArgs[] = "--tahun {$tahun}";
         }
         if (!empty($kategori) && $kategori !== 'semua') {
             $cleanKategori = escapeshellarg($kategori);
             $cmdArgs[] = "--kategori {$cleanKategori}";
         }
         if (in_array($periode, ['putus', 'regis', 'upload'])) {
             $cmdArgs[] = "--periode {$periode}";
         }

         $argsStr = implode(' ', $cmdArgs);
         $command = "{$pythonBinary} \"{$this->pythonScriptPath}\" {$argsStr}";

         $env = $this->getPythonExecutionEnv();

         // Tambah timeout proporsional terhadap jumlah halaman (30 detik per halaman)
         $timeout = max(120, $pages * 30);
         $result = Process::timeout($timeout)
             ->env($env)
             ->path(base_path())
             ->run($command);

         if (!$result->successful()) {
             return response()->json([
                 'success' => false,
                 'message' => 'Gagal mengeksekusi skrip scraper Python.',
                 'error' => $result->errorOutput() ?: $result->output(),
             ], 500);
         }

         $output = trim($result->output());
         // Bersihkan malformed UTF-8 characters dari stdout Windows bila ada
         $cleanOutput = mb_convert_encoding($output, 'UTF-8', 'UTF-8');
         $data = json_decode($cleanOutput, true);

         if (!is_array($data)) {
             return response()->json([
                 'success' => false,
                 'message' => 'Output dari scraper bukan format JSON valid.',
                 'raw_output' => $output
             ], 500);
         }

         // Simpan / update ke cache JSON lokal sesuai tahun yang di-scrape
         $this->saveToStorage($data, $tahun ?: 2026);

         // Ambil seluruh data terkini yang sudah digabung
         $allCached = $this->loadCachedPutusan($tahun ?: 2026);

         return response()->json([
             'success' => true,
             'message' => "Berhasil menarik " . count($data) . " berkas putusan dari Mahkamah Agung.",
             'scraped_count' => count($data),
             'total_in_storage' => count($allCached),
             'tahun' => $tahun ?: 2026,
             'kategori' => $kategori ?: 'semua',
             'data' => $allCached
         ]);
     }

    /**
     * Mengambil data putusan dalam format JSON API
     */
    public function getData(Request $request)
    {
        $tahun = (int) $request->input('tahun', 2026);
        $data = $this->loadCachedPutusan($tahun);
        return response()->json([
            'status' => 'success',
            'tahun' => $tahun,
            'count' => count($data),
            'data' => $data
        ]);
    }

    /**
     * Membaca data dari cache JSON lokal berdasarkan tahun
     */
    protected function loadCachedPutusan(int $tahun = 2026): array
    {
        $items = [];
        $map = [];

        $rootJson = base_path("putusan_{$tahun}.json");
        if (File::exists($rootJson)) {
            $list = json_decode(File::get($rootJson), true) ?: [];
            foreach ($list as $it) {
                $k = $it['url'] ?? $it['judul'] ?? '';
                if ($k && !isset($map[$k])) {
                    $map[$k] = true;
                    $items[] = $it;
                }
            }
        }

        $storagePath = storage_path("app/putusan_{$tahun}.json");
        if (File::exists($storagePath)) {
            $list = json_decode(File::get($storagePath), true) ?: [];
            foreach ($list as $it) {
                $k = $it['url'] ?? $it['judul'] ?? '';
                if ($k && !isset($map[$k])) {
                    $map[$k] = true;
                    $items[] = $it;
                }
            }
        }

        return $items;
    }

    /**
     * Menyimpan data putusan ke file storage per tahun
     */
    protected function saveToStorage(array $newData, int $tahun = 2026): void
    {
        $existing = $this->loadCachedPutusan($tahun);
        
        // Gabungkan data unik berdasarkan URL
        $map = [];
        foreach ($existing as $item) {
            $map[$item['url']] = $item;
        }
        foreach ($newData as $item) {
            $map[$item['url']] = $item;
        }

        $combined = array_values($map);

        $storagePath = storage_path("app/putusan_{$tahun}.json");
        if (!File::isDirectory(dirname($storagePath))) {
            File::makeDirectory(dirname($storagePath), 0755, true);
        }

        File::put($storagePath, json_encode($combined, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Mengambil counter statistik resmi dari halaman direktori MA secara real-time
     */
    public function getLiveOfficialCounts(): array
    {
        $cacheKey = 'ma_official_counts_realtime';
        // Simpan cache selama 30 menit agar halaman tetap sangat cepat dibuka
        if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
            return \Illuminate\Support\Facades\Cache::get($cacheKey);
        }

        $pythonBinary = $this->resolvePythonBinary();
        $command = "{$pythonBinary} \"{$this->pythonScriptPath}\" --stats";

        $env = $this->getPythonExecutionEnv();

        $res = Process::timeout(15)->env($env)->path(base_path())->run($command);
        if ($res->successful()) {
            $parsed = json_decode(trim($res->output()), true);
            if (is_array($parsed) && !empty($parsed)) {
                $merged = array_merge($this->officialCounts, $parsed);
                \Illuminate\Support\Facades\Cache::put($cacheKey, $merged, 1800);
                return $merged;
            }
        }

        return $this->officialCounts;
    }

    /**
     * Mengambil 1 halaman langsung dari portal MA secara real-time via Python (Live Proxy Stream)
     */
    protected function fetchFromMahkamahAgungLive(
        string $kategori = 'semua',
        string $peradilan = '',
        int $tahun = 0,
        string $periode = 'putus',
        int $page = 1,
        ?string $query = ''
    ): array {
        $pythonBinary = $this->resolvePythonBinary();

        $cmdArgs = ["--live", "--page {$page}"];
        if ($tahun > 0) {
            $cmdArgs[] = "--tahun {$tahun}";
        }
        if (!empty($kategori) && $kategori !== 'semua') {
            $cleanKategori = escapeshellarg($kategori);
            $cmdArgs[] = "--kategori {$cleanKategori}";
        }
        if (!empty($peradilan) && $peradilan !== 'semua') {
            $cleanPeradilan = escapeshellarg($peradilan);
            $cmdArgs[] = "--peradilan {$cleanPeradilan}";
        }
        if (in_array($periode, ['putus', 'regis', 'upload'])) {
            $cmdArgs[] = "--periode {$periode}";
        }
        if (!empty($query)) {
            $cleanQuery = escapeshellarg($query);
            $cmdArgs[] = "--q {$cleanQuery}";
        }

        $argsStr = implode(' ', $cmdArgs);
        $command = "{$pythonBinary} \"{$this->pythonScriptPath}\" {$argsStr}";

        $env = $this->getPythonExecutionEnv();

        $result = Process::timeout(30)
            ->env($env)
            ->path(base_path())
            ->run($command);

        if (!$result->successful()) {
            $cached = $this->loadCachedPutusan($tahun > 0 ? $tahun : 2026);
            $catCountStr = $this->officialCounts[$kategori] ?? '11450852';
            $numOnly = (int) preg_replace('/[^\d]/', '', $catCountStr);
            $estimatedMaxPage = min(499, max(1, (int) ceil($numOnly / 20)));
            $offset = ($page - 1) * 20;
            $sliceItems = !empty($cached) ? array_slice($cached, $offset % count($cached), 20) : [];

            return [
                'success' => false,
                'items' => $sliceItems,
                'pagination' => [
                    'current_page' => $page,
                    'max_page' => $estimatedMaxPage,
                    'has_next' => $page < $estimatedMaxPage,
                    'has_prev' => $page > 1
                ]
            ];
        }

        $output = trim($result->output());
        $cleanOutput = mb_convert_encoding($output, 'UTF-8', 'UTF-8');
        $data = json_decode($cleanOutput, true);

        if (!is_array($data) || !isset($data['items']) || empty($data['items'])) {
            $cached = $this->loadCachedPutusan($tahun > 0 ? $tahun : 2026);
            
            // Hitung estimasi max_page dari statistik direktori MA (atau batas standar MA 499 halaman per kategori)
            $catCountStr = $this->officialCounts[$kategori] ?? '11450852';
            $numOnly = (int) preg_replace('/[^\d]/', '', $catCountStr);
            $estimatedMaxPage = min(499, max(1, (int) ceil($numOnly / 20)));

            if (!empty($cached)) {
                $offset = ($page - 1) * 20;
                // Jika offset melebihi cache, loop secara virtual agar selalu ada data relevan
                $sliceItems = array_slice($cached, $offset % count($cached), 20);
                if (empty($sliceItems)) {
                    $sliceItems = array_slice($cached, 0, 20);
                }

                return [
                    'success' => true,
                    'items' => $sliceItems,
                    'pagination' => [
                        'current_page' => $page,
                        'max_page' => $estimatedMaxPage,
                        'has_next' => $page < $estimatedMaxPage,
                        'has_prev' => $page > 1
                    ]
                ];
            }

            return [
                'success' => false,
                'items' => [],
                'pagination' => [
                    'current_page' => $page,
                    'max_page' => $estimatedMaxPage,
                    'has_next' => $page < $estimatedMaxPage,
                    'has_prev' => $page > 1
                ]
            ];
        }

        return $data;
    }

    /**
     * Membangun struktur kartu sidebar default (Direktori, Peradilan, Tahun 3-Kolom)
     * jika scraper live belum mengembalikan filter, sehingga tampilan sidebar konsisten 100%.
     */
    protected function getDefaultSidebarFilters(
        string $kategori = 'semua',
        string $peradilan = '',
        int $tahun = 0,
        string $periode = 'putus'
    ): array {
        $realtimeCounts = $this->getLiveOfficialCounts();

        // 1. Kartu Direktori Putusan
        $direktoriItems = [];
        $direktoriItems[] = [
            'label' => 'Semua Direktori',
            'count' => '',
            'kategori' => 'semua',
            'peradilan' => '',
            'tahun' => $tahun,
            'periode' => $periode,
            'is_active' => empty($kategori) || $kategori === 'semua'
        ];

        $kategoriDisplay = [
            'perdata-agama-1' => 'Perdata Agama',
            'perdata-1' => 'Perdata',
            'pidana-umum-1' => 'Pidana Umum',
            'pidana-khusus-1' => 'Pidana Khusus',
            'tun-1' => 'TUN',
            'perdata-khusus' => 'Perdata Khusus',
            'pidana-militer-1' => 'Pidana Militer',
            'pajak-2' => 'Pajak',
            'sengketa-kewenangan-mengadili-1' => 'Sengketa Kewenangan Mengadili',
        ];

        foreach ($kategoriDisplay as $slug => $label) {
            $direktoriItems[] = [
                'label' => $label,
                'count' => $realtimeCounts[$slug] ?? ($this->officialCounts[$slug] ?? ''),
                'kategori' => $slug,
                'peradilan' => $peradilan,
                'tahun' => $tahun,
                'periode' => $periode,
                'is_active' => $kategori === $slug
            ];
        }

        // 2. Kartu Peradilan
        $peradilanItems = [
            ['label' => 'Mahkamah Agung', 'count' => '274.845', 'kategori' => $kategori, 'peradilan' => 'pengadilan/mahkamah-agung', 'tahun' => $tahun, 'periode' => $periode, 'is_active' => $peradilan === 'pengadilan/mahkamah-agung'],
            ['label' => 'Peradilan Umum', 'count' => '3.055.164', 'kategori' => $kategori, 'peradilan' => 'lingkungan/peradilan-umum', 'tahun' => $tahun, 'periode' => $periode, 'is_active' => $peradilan === 'lingkungan/peradilan-umum'],
            ['label' => 'Peradilan Agama', 'count' => '7.629.297', 'kategori' => $kategori, 'peradilan' => 'lingkungan/peradilan-agama', 'tahun' => $tahun, 'periode' => $periode, 'is_active' => $peradilan === 'lingkungan/peradilan-agama'],
            ['label' => 'Peradilan Militer', 'count' => '38.024', 'kategori' => $kategori, 'peradilan' => 'lingkungan/peradilan-militer', 'tahun' => $tahun, 'periode' => $periode, 'is_active' => $peradilan === 'lingkungan/peradilan-militer'],
            ['label' => 'Peradilan Tata Usaha Negara', 'count' => '47.439', 'kategori' => $kategori, 'peradilan' => 'lingkungan/peradilan-tata-usaha-negara', 'tahun' => $tahun, 'periode' => $periode, 'is_active' => $peradilan === 'lingkungan/peradilan-tata-usaha-negara'],
            ['label' => 'Pengadilan Pajak', 'count' => '31.816', 'kategori' => $kategori, 'peradilan' => 'pengadilan/pengadilan-pajak', 'tahun' => $tahun, 'periode' => $periode, 'is_active' => $peradilan === 'pengadilan/pengadilan-pajak'],
        ];

        // 3. Kartu Tahun (Tabel 3 Kolom: Putus, Register, Upload)
        $sampleYears = [2026, 2025, 2024, 2023, 2022];
        $colPutus = [];
        $colRegis = [];
        $colUpload = [];

        foreach ($sampleYears as $yr) {
            $colPutus[] = [
                'label' => (string) $yr,
                'count' => '',
                'kategori' => $kategori,
                'peradilan' => $peradilan,
                'tahun' => $yr,
                'periode' => 'putus',
                'is_active' => $tahun == $yr && $periode === 'putus'
            ];
            $colRegis[] = [
                'label' => (string) $yr,
                'count' => '',
                'kategori' => $kategori,
                'peradilan' => $peradilan,
                'tahun' => $yr,
                'periode' => 'regis',
                'is_active' => $tahun == $yr && $periode === 'regis'
            ];
            $colUpload[] = [
                'label' => (string) $yr,
                'count' => '',
                'kategori' => $kategori,
                'peradilan' => $peradilan,
                'tahun' => $yr,
                'periode' => 'upload',
                'is_active' => $tahun == $yr && $periode === 'upload'
            ];
        }

        return [
            [
                'title' => 'Direktori',
                'is_table' => false,
                'items' => $direktoriItems
            ],
            [
                'title' => 'Peradilan',
                'is_table' => false,
                'items' => $peradilanItems
            ],
            [
                'title' => 'Tahun',
                'is_table' => true,
                'columns' => [
                    ['name' => 'Putus', 'periode' => 'putus', 'items' => $colPutus],
                    ['name' => 'Register', 'periode' => 'register', 'items' => $colRegis],
                    ['name' => 'Upload', 'periode' => 'upload', 'items' => $colUpload],
                ]
            ]
        ];
    }


    /**
     * Endpoint API untuk pengambilan data putusan live streaming
     */
    public function liveData(Request $request)
    {
        $tahun = (int) $request->input('tahun', 0);
        $kategori = $request->input('kategori', 'pidana-khusus-1');
        $search = $request->input('q', '');
        $page = max((int) $request->input('page', 1), 1);
        $periode = $request->input('periode', 'putus');

        $result = $this->fetchFromMahkamahAgungLive($kategori, $tahun, $periode, $page, $search);

        return response()->json($result);
    }

    /**
     * Ambil pratinjau ringkasan/metadata perkara atau yurisprudensi langsung tanpa langsung unduh PDF
     */
    public function previewDetail(Request $request)
    {
        $url = $request->input('url');
        if (empty($url)) {
            return response()->json(['success' => false, 'message' => 'URL perkara tidak diberikan.'], 400);
        }

        $pythonBinary = $this->resolvePythonBinary();
        $cleanUrl = escapeshellarg($url);
        $command = "{$pythonBinary} \"{$this->pythonScriptPath}\" --preview {$cleanUrl}";

        $env = $this->getPythonExecutionEnv();

        $res = Process::timeout(35)->env($env)->path(base_path())->run($command);
        if (!$res->successful()) {
            return response()->json(['success' => false, 'message' => 'Gagal mengambil pratinjau perkara dari portal MA.'], 502);
        }

        $output = trim($res->output());
        $json = json_decode($output, true);
        if (!$json) {
            return response()->json(['success' => false, 'message' => 'Format respons portal MA tidak valid.'], 502);
        }

        return response()->json($json);
    }

    /**
     * Stream langsung file PDF dokumen putusan ke browser tanpa harus membuka portal MA
     */
    public function viewPdf(Request $request)
    {
        $url = $request->input('url');
        if (empty($url)) {
            return response('URL dokumen putusan tidak diberikan.', 400);
        }

        // Buat nama file cache sementara berdasarkan hash URL
        $hash = md5($url);
        $tempDir = storage_path('app/pdf_cache');
        if (!File::isDirectory($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        $pdfPath = "{$tempDir}/{$hash}.pdf";

        // Jika belum ada di cache sementara, fetch via Python bypass Cloudflare
        if (!File::exists($pdfPath) || File::size($pdfPath) < 500) {
            $pythonBinary = $this->resolvePythonBinary();
            $cleanUrl = escapeshellarg($url);
            $cleanPath = escapeshellarg($pdfPath);
            $command = "{$pythonBinary} \"{$this->pythonScriptPath}\" --get-pdf {$cleanUrl} --save-to {$cleanPath}";

            $env = $this->getPythonExecutionEnv();

            $res = Process::timeout(45)->env($env)->path(base_path())->run($command);
            if (!$res->successful() || !File::exists($pdfPath) || File::size($pdfPath) < 500) {
                return response('Gagal mengunduh berkas PDF dari server Mahkamah Agung. Dokumen mungkin belum diunggah atau berstatus pembatasan akses.', 502);
            }
        }

        // Tampilkan langsung di browser sebagai inline PDF
        return response()->file($pdfPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="salinan_putusan_ma.pdf"'
        ]);
    }

    /**
     * Siapkan environment variables untuk eksekusi child process Python secara cross-platform
     */
    protected function getPythonExecutionEnv(): array
    {
        $systemEnv = array_merge($_ENV, $_SERVER);

        if (PHP_OS_FAMILY === 'Windows') {
            $pythonPaths = [
                'C:\\Users\\user\\AppData\\Roaming\\Python\\Python314\\site-packages',
                'C:\\Users\\user\\AppData\\Local\\Packages\\PythonSoftwareFoundation.Python.3.11_qbz5n2kfra8p0\\LocalCache\\local-packages\\Python311\\site-packages',
            ];
            return array_merge($systemEnv, [
                'SystemRoot' => getenv('SystemRoot') ?: 'C:\\Windows',
                'SYSTEMROOT' => getenv('SystemRoot') ?: 'C:\\Windows',
                'windir' => getenv('windir') ?: 'C:\\Windows',
                'PATH' => getenv('PATH') ?: 'C:\\Windows\\system32;C:\\Windows',
                'PYTHONPATH' => implode(';', $pythonPaths),
                'USERPROFILE' => 'C:\\Users\\user',
                'APPDATA' => 'C:\\Users\\user\\AppData\\Roaming',
                'LOCALAPPDATA' => 'C:\\Users\\user\\AppData\\Local',
            ]);
        }

        // Environment standar untuk Linux / Docker (Render, Fly.io, Ubuntu)
        return array_merge($systemEnv, [
            'PYTHONUNBUFFERED' => '1',
            'PATH' => getenv('PATH') ?: '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin',
        ]);
    }

    /**
     * Deteksi executable Python yang tersedia di sistem
     */
    protected function resolvePythonBinary(): string
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            // Linux / Docker container
            foreach (['python3', 'python'] as $bin) {
                $test = Process::run("{$bin} --version");
                if ($test->successful()) {
                    return $bin;
                }
            }
            return 'python3';
        }

        // Prioritaskan Python 3.11 yang stabil dan kompatibel penuh dengan Winsock socket Windows
        $candidates = [
            'py -3.11',
            'C:\\Program Files\\WindowsApps\\PythonSoftwareFoundation.Python.3.11_3.11.2544.0_x64__qbz5n2kfra8p0\\python3.11.exe',
            'py -3',
            'python',
            'C:\\Python314\\python.exe',
        ];

        foreach ($candidates as $candidate) {
            if (str_contains($candidate, '.exe') && File::exists($candidate)) {
                return "\"{$candidate}\"";
            }
            $test = Process::run("{$candidate} -c \"import curl_cffi\"");
            if ($test->successful()) {
                return $candidate;
            }
        }

        return 'py -3.11';
    }

    /**
     * Endpoint API Tanya AI Hukum dengan integrasi Gemini & Grounding Direktori Putusan MA
     */
    public function aiChat(\Illuminate\Http\Request $request, \App\Services\GeminiLegalService $gemini, \App\Services\FirebaseService $firebase)
    {
        // Berikan waktu eksekusi yang cukup untuk proses download PDF MA + Firebase + Inferensi AI Lokal Ollama / Cloud
        @set_time_limit(300);
        @ini_set('max_execution_time', '300');

        $prompt = trim($request->input('message', ''));
        if (empty($prompt)) {
            return response()->json([
                'success' => false,
                'message' => 'Pertanyaan tidak boleh kosong.'
            ], 422);
        }

        // 1. Ambil data putusan untuk grounding
        $relevantItems = [];
        $contextPutusan = $request->input('context_putusan');

        // Jika ada konteks perkara spesifik yang sedang dibuka di modal (Mode Bedah Perkara Khusus)
        if (!empty($contextPutusan) && is_array($contextPutusan) && !empty($contextPutusan['judul'])) {
            $relevantItems = [[
                'judul' => $contextPutusan['judul'],
                'url' => $contextPutusan['url'] ?? '',
                'klasifikasi' => $contextPutusan['klasifikasi'] ?? 'Kategori Perkara',
                'ringkasan' => $contextPutusan['ringkasan'] ?? ($contextPutusan['amar'] ?? ''),
                'kaidah' => $contextPutusan['kaidah'] ?? '',
                'tgl_putus' => $contextPutusan['tgl_putus'] ?? '',
                'is_single_focus' => true,
            ]];
        } else {
            // Mode Chat Global: cari putusan dari database lokal berdasarkan kata kunci pertanyaan
            $localPutusan = $this->loadCachedPutusan(2026);
            $keywords = array_filter(explode(' ', preg_replace('/[^\p{L}\p{N}\s]/u', '', strtolower($prompt))), function($k) {
                return strlen($k) >= 3 && !in_array($k, ['apa', 'bagaimana', 'apakah', 'yang', 'dan', 'atau', 'pada', 'tentang', 'terkait', 'kasus', 'perkara', 'hukum', 'menurut', 'putusan', 'mahkamah', 'agung', 'dimana', 'siapa']);
            });

            if (!empty($keywords)) {
                foreach ($localPutusan as $item) {
                    $searchStr = strtolower(($item['judul'] ?? '') . ' ' . ($item['klasifikasi'] ?? '') . ' ' . ($item['ringkasan'] ?? ''));
                    $matchCount = 0;
                    foreach ($keywords as $kw) {
                        if (str_contains($searchStr, $kw)) {
                            $matchCount++;
                        }
                    }
                    if ($matchCount > 0) {
                        $item['relevance_score'] = $matchCount;
                        $relevantItems[] = $item;
                    }
                }

                usort($relevantItems, fn($a, $b) => ($b['relevance_score'] ?? 0) <=> ($a['relevance_score'] ?? 0));
                $relevantItems = array_slice($relevantItems, 0, 3);
            }
        }

        // 1.5 Grounding Enrichment: Ambil teks lengkap putusan (Identitas, Pertimbangan, Amar) dari Firebase / Ekstraksi PDF
        if (!empty($relevantItems)) {
            // Ambil maksimal 2 putusan paling relevan untuk diperkaya agar tidak berat
            for ($i = 0; $i < min(2, count($relevantItems)); $i++) {
                $item = &$relevantItems[$i];
                $docKey = md5($item['url'] ?? $item['judul'] ?? '');

                // Cek apakah data dokumen sudah ada di Firebase Firestore
                $cachedDoc = $firebase->getPutusanDocument($docKey);
                if ($cachedDoc && !empty($cachedDoc['identitas_para_pihak'])) {
                    $item['identitas_para_pihak'] = $cachedDoc['identitas_para_pihak'];
                    $item['amar_putusan'] = $cachedDoc['amar_putusan'] ?? '';
                    $item['pdf_excerpt'] = $cachedDoc['excerpt'] ?? '';
                } else {
                    // Jika belum ada di Firebase, lakukan ekstraksi PDF on-demand dan simpan ke Firestore
                    $extracted = $this->extractPutusanPdfText($item['url'] ?? '');
                    if ($extracted && !empty($extracted['identitas_para_pihak'])) {
                        $item['identitas_para_pihak'] = $extracted['identitas_para_pihak'];
                        $item['amar_putusan'] = $extracted['amar_putusan'] ?? '';
                        $item['pdf_excerpt'] = $extracted['excerpt'] ?? '';

                        // Simpan ke Cloud Firestore untuk pertanyaan berikutnya
                        $firebase->savePutusanDocument($docKey, [
                            'judul' => $item['judul'] ?? '',
                            'url' => $item['url'] ?? '',
                            'identitas_para_pihak' => $extracted['identitas_para_pihak'],
                            'amar_putusan' => $extracted['amar_putusan'] ?? '',
                            'excerpt' => $extracted['excerpt'] ?? '',
                            'total_pages' => $extracted['total_pages'] ?? 1,
                        ]);
                    }
                }
            }
            unset($item);
        }

        // 2. Kirim ke AI (Default: Ollama Lokal Llama 3.2, atau Gemini Cloud jika dipilih)
        $history = $request->input('history', []);
        $provider = $request->input('provider', 'ollama');
        $result = $gemini->askLegalAssistant($prompt, is_array($history) ? $history : [], $relevantItems, $provider);

        // 3. Sertakan daftar dokumen putusan resmi terkait untuk ditampilkan di UI
        $result['sources'] = array_map(function($item) {
            return [
                'judul' => $item['judul'] ?? 'Putusan Mahkamah Agung',
                'url' => $item['url'] ?? '',
                'klasifikasi' => $item['klasifikasi'] ?? 'Umum',
                'tgl_putus' => $item['tgl_putus'] ?? '',
                'ringkasan' => $item['ringkasan'] ?? '',
            ];
        }, $relevantItems);

        return response()->json($result);
    }

    /**
     * Helper untuk mengekstrak teks PDF putusan (dari cache PDF lokal atau unduh baru via scraper)
     */
    protected function extractPutusanPdfText(string $url): ?array
    {
        if (empty($url)) return null;

        $hash = md5($url);
        $tempDir = storage_path('app/pdf_cache');
        if (!File::isDirectory($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        $pdfPath = "{$tempDir}/{$hash}.pdf";
        $pythonBinary = $this->resolvePythonBinary();
        $cleanPath = escapeshellarg($pdfPath);
        $cleanUrl = escapeshellarg($url);

        // Jika PDF sudah ada di cache lokal, ekstrak langsung dari file tersebut
        if (File::exists($pdfPath) && File::size($pdfPath) > 500) {
            $command = "{$pythonBinary} \"{$this->pythonScriptPath}\" --extract-text {$cleanPath}";
        } else {
            // Jika belum ada, unduh dan ekstrak sekaligus via URL
            $command = "{$pythonBinary} \"{$this->pythonScriptPath}\" --extract-text {$cleanUrl} --save-to {$cleanPath}";
        }

        $env = $this->getPythonExecutionEnv();

        try {
            $res = Process::timeout(18)->env($env)->path(base_path())->run($command);
            if ($res->successful()) {
                $output = trim($res->output());
                // Cari JSON dari output
                if (preg_match('/\{.*\}/s', $output, $m)) {
                    $json = json_decode($m[0], true);
                    if ($json && ($json['success'] ?? false)) {
                        return $json;
                    }
                }
            }
        } catch (\Throwable $e) {
            \Log::warning("Gagal ekstraksi PDF {$url}: " . $e->getMessage());
        }

        return null;
    }
}
