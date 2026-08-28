<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Jenis;
use App\Models\Lokasi;
use App\Models\Amil;
use App\Models\Sumber;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use App\Models\PindahAset;
use App\Models\Approval;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Notifications\AssetMutationNotification;


class AssetController extends Controller
{
    

public function index(Request $request)
{
    $query = Asset::with([
        'barang',
        'kategori',
        'jenis',
        'lokasi',
        'amil',
        'sumberDana'
    ]);

    // =========================================================================
    // 🔍 FITUR SEARCH GLOBAL (TAMBAHAN BARU - AMAN DARI BENTROKAN FILTER)
    // =========================================================================
    if ($request->filled('q')) {
        $search = $request->q;
        $query->where(function ($subQuery) use ($search) {
            // Search di tabel asset sendiri
            $subQuery->where('kode_aset', 'like', '%' . $search . '%')
                     ->orWhere('merk', 'like', '%' . $search . '%')
                     ->orWhere('kondisi', 'like', '%' . $search . '%')
                     ->orWhere('status', 'like', '%' . $search . '%');

            // Search merujuk ke relasi nama_barang di tabel barangs
            $subQuery->orWhereHas('barang', function ($q) use ($search) {
                $q->where('nama_barang', 'like', '%' . $search . '%');
            });

            // Search merujuk ke relasi nama_kategori di tabel kategoris
            $subQuery->orWhereHas('kategori', function ($q) use ($search) {
                $q->where('nama_kategori', 'like', '%' . $search . '%');
            });

            // Search merujuk ke relasi nama_lokasi di tabel lokasis
            $subQuery->orWhereHas('lokasi', function ($q) use ($search) {
                $q->where('nama_lokasi', 'like', '%' . $search . '%');
            });

            // Search merujuk ke relasi nama amil di tabel amils
            $subQuery->orWhereHas('amil', function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%');
            });
        });
    }

    // FILTER LOKASI
    if ($request->filled('id_lokasi')) {
        $query->where('id_lokasi', $request->id_lokasi);
    }

    // FILTER KATEGORI
    if ($request->filled('id_kategori')) {
        $query->where('id_kategori', $request->id_kategori);
    }

    // FILTER TAHUN
    if ($request->filled('tahun')) {
        $query->where('tahun', $request->tahun);
    }

    // FILTER KONDISI
    if ($request->filled('kondisi')) {
        $query->where('kondisi', $request->kondisi);
    }

    $assets = $query
        ->latest()
        ->paginate(10)
        ->appends($request->query());

    $barangs   = Barang::orderBy('nama_barang')->get();
    $kategoris = Kategori::orderBy('nama_kategori')->get();
    $jenis     = Jenis::orderBy('nama_jenis')->get();
    $lokasis   = Lokasi::orderBy('nama_lokasi')->get();
    $amils     = Amil::orderBy('nama')->get();
    $sumbers   = Sumber::orderBy('kode_sumber_dana')->get();

    return view('superadmin.aset.data_aset.index', compact(
        'assets',
        'barangs',
        'kategoris',
        'jenis',
        'lokasis',
        'amils',
        'sumbers'
    ));
}


public function store(Request $request)
{
    $request->validate([
        'id_barang'        => 'required|exists:barangs,id_barang',
        'id_kategori'      => 'required|exists:kategoris,id_kategori',
        'id_jenis'         => 'required|exists:jenis,id_jenis',
        'id_lokasi'        => 'required|exists:lokasis,id_lokasi',
        'id_amil'          => 'required|exists:amils,id_amil',
        'id_sumber_dana'   => 'required|exists:sumbers,id_sumber_dana',
        'merk'             => 'nullable|string|max:100',
        'tahun'            => 'required|digits:4',
        'volume'           => 'required|integer|min:1',
        'satuan'           => 'nullable|string|max:20',
        'harga'            => 'required|numeric|min:0',
        'kondisi'          => 'required|in:baik,rusak,hilang',
        'status'           => 'required|in:aktif,nonaktif',
    ]);

    DB::beginTransaction();

    try {

        /* ==========================
         * 1. CEK DUPLIKAT DATA ASET
         * ========================== */
        $duplikat = Asset::where('id_barang', $request->id_barang)
            ->where('id_lokasi', $request->id_lokasi)
            ->where('tahun', $request->tahun)
            ->where('id_sumber_dana', $request->id_sumber_dana)
            ->whereNull('deleted_at')
            ->exists();

        if ($duplikat) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Data aset sudah terdaftar sebelumnya.');
        }

        /* ==========================
         * 2. GENERATE KODE ASET FIX
         * ========================== */

        // Ambil data master
        $barang   = Barang::findOrFail($request->id_barang);
        $kategori = Kategori::findOrFail($request->id_kategori);
        $jenis    = Jenis::findOrFail($request->id_jenis);
        $lokasi   = Lokasi::findOrFail($request->id_lokasi);
        $sumber   = Sumber::findOrFail($request->id_sumber_dana);

        // Ambil KODE BUKAN ID
        $kodeLokasi   = str_pad($lokasi->kode_lokasi, 2, '0', STR_PAD_LEFT);
        $kodeKategori = str_pad($kategori->kode_kategori, 2, '0', STR_PAD_LEFT);
        $kodeJenis    = str_pad($jenis->kode_jenis, 2, '0', STR_PAD_LEFT);
        $kodeBarang   = str_pad($barang->kode_barang, 2, '0', STR_PAD_LEFT);

        // Sumber dana (pakai kode, bukan asumsi A/B/C)
        $kodeSumber = $sumber->kode_sumber_dana;

        // Tahun pembelian
        $tahun = substr($request->tahun, 2, 2);

        // Nomor urut
        $lastAset = Asset::where('id_lokasi', $request->id_lokasi)
            ->where('id_kategori', $request->id_kategori)
            ->orderBy('id_aset', 'desc') // ambil terakhir
            ->first();

        if ($lastAset) {
            // ambil 3 digit terakhir dari kode_aset
            $lastUrut = (int) substr($lastAset->kode_aset, -3);
            $nextUrut = $lastUrut + 1;
        } else {
            $nextUrut = 1;
        }

        $urut = str_pad($nextUrut, 3, '0', STR_PAD_LEFT);

        // Final kode aset
        $kodeAset = "{$kodeLokasi}.{$kodeKategori}.{$kodeJenis}.{$kodeBarang}.{$kodeSumber}.{$tahun}.{$urut}";


        /* ==========================
         * 3. HITUNG TOTAL HARGA
         * ========================== */
        $totalHarga = $request->volume * $request->harga;

        /* ==========================
         * 4. GENERATE QR CODE
         * ========================== */
        $qrDirectory = public_path('qr-assets');

        if (!file_exists($qrDirectory)) {
            mkdir($qrDirectory, 0755, true);
        }
        
        $qrFileName = 'qr_' . Str::uuid() . '.png';
        $qrPath     = 'qr-assets/' . $qrFileName;
        $signedUrl = URL::signedRoute('assets.scan', [
            'kode_aset' => $kodeAset
        ]);

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($signedUrl)
            ->size(300)
            ->margin(5)
            ->build();

        $result->saveToFile(public_path($qrPath));

        /* ==========================
         * 5. SIMPAN DATA
         * ========================== */
        Asset::create([
            'kode_aset'      => $kodeAset,
            'id_barang'      => $request->id_barang,
            'id_kategori'    => $request->id_kategori,
            'id_jenis'       => $request->id_jenis,
            'id_lokasi'      => $request->id_lokasi,
            'id_amil'        => $request->id_amil,
            'id_sumber_dana' => $request->id_sumber_dana,
            'merk'           => $request->merk,
            'tahun'          => $request->tahun,
            'volume'         => $request->volume,
            'satuan'         => $request->satuan,
            'harga'          => $request->harga,
            'total_harga'    => $totalHarga,
            'kondisi'        => $request->kondisi,
            'status'         => $request->status,
            'qr_code'        => $qrPath,
        ]);

        DB::commit();

        return redirect()
            ->route('assets.index')
            ->with('success', 'Data aset berhasil ditambahkan.');

    } catch (\Exception $e) {

        DB::rollBack();

        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Gagal menyimpan data aset: ' . $e->getMessage());
    }
}

public function show($id)

{

    $aset = Asset::with([

        'barang',

        'kategori',

        'jenis',

        'lokasi',

        'amil',

        'sumberDana'

    ])->findOrFail($id);



    return response()->json([

        'id_aset'      => $aset->id_aset,

        'kode_aset'    => $aset->kode_aset,

        'qr_code'      => $aset->qr_code ? asset($aset->qr_code) : null,

        'barang'       => $aset->barang->nama_barang ?? '-',

        'merk'         => $aset->merk ?? '-',

        'tahun'        => $aset->tahun ?? '-',

        'volume'       => $aset->volume ?? '-',

        'satuan'       => $aset->satuan ?? '-',

        'kondisi'      => $aset->kondisi ?? '-',

        'status'       => $aset->status,

        'harga'        => $aset->harga ?? '-',

        'total_harga'  => $aset->total_harga ?? '-',

        'kategori'     => $aset->kategori->nama_kategori ?? '-',

        'jenis'        => $aset->jenis->nama_jenis ?? '-',

        'lokasi'       => $aset->lokasi->nama_lokasi ?? '-',

        'amil'         => $aset->amil->nama ?? '-',

        'sumber_dana'  => $aset->sumberDana->nama_sumber_dana ?? '-',

    ]);

}

public function getReferensiData()
{
    return response()->json([
        'barangs'   => Barang::orderBy('nama_barang')->get(),
        'kategoris' => Kategori::orderBy('nama_kategori')->get(),
        'jenis'     => Jenis::orderBy('nama_jenis')->get(),
        'lokasis'   => Lokasi::orderBy('nama_lokasi')->get(),
        'amils'     => Amil::orderBy('nama')->get(),
        'sumbers'   => Sumber::orderBy('kode_sumber_dana')->get(),
    ]);
}

public function getReferensiPindah()
{
    return response()->json([
        'amils' => Amil::orderBy('nama')->get(),
        'lokasis' => Lokasi::orderBy('nama_lokasi')->get(),
    ]);
}

public function pindahAset(Request $request)
{
    $request->validate([
        'id_aset' => 'required|exists:assets,id_aset',
        'id_amil_sesudah' => 'required|exists:amils,id_amil',
        'id_lokasi_baru' => 'required|exists:lokasis,id_lokasi',
        'alasan' => 'required|string|max:500',
        'from_scan_qr' => 'nullable|boolean',
    ]);

    $logoutAfterSuccess = $request->boolean('from_scan_qr');

    DB::beginTransaction();

    try {

        $aset = Asset::where('id_aset', $request->id_aset)
            ->lockForUpdate()
            ->firstOrFail();

        // VALIDASI LOGIC
        if (in_array(Auth::user()->role, ['kadept', 'kadiv'])) {
            throw new \Exception('Pengajuan perpindahan aset hanya dapat dilakukan oleh Staff / Operator Logistik.');
        }

        if ($aset->id_amil == $request->id_amil_sesudah) {
            throw new \Exception('Penanggung jawab baru tidak boleh sama.');
        }

        if ($aset->id_lokasi == $request->id_lokasi_baru) {
            throw new \Exception('Lokasi baru tidak boleh sama.');
        }

        // CEK PENDING

        $pending = PindahAset::where('id_aset', $aset->id_aset)
            ->where('status', 'menunggu')
            ->exists();

        if ($pending) {
            throw new \Exception('Aset masih memiliki permintaan perpindahan yang pending.');
        }

        // SIMPAN KE TABEL PINDAH_ASETS
        $pindah = PindahAset::create([
            'id_aset' => $aset->id_aset,
            'id_lokasi_lama' => $aset->id_lokasi,
            'id_amil_sebelum' => $aset->id_amil,
            'id_amil_sesudah' => $request->id_amil_sesudah,
            'id_lokasi_baru' => $request->id_lokasi_baru,
            'alasan' => $request->alasan,
            'status' => 'menunggu',
            'requested_by' => Auth::id(),
        ]);

        // BUAT APPROVAL RECORD - STAGE 1 (KADEPT)
        $approval = Approval::create([
            'id_pindah' => $pindah->id_pindah,
            'stage' => 1, // Stage 1 untuk Kepala Departemen
            'status' => 'menunggu',
            'requested_by' => Auth::id(),
            'approved_by' => null, // Belum ada yang approve
            'approved_at' => null,
        ]);

        // JIKA BUTUH STAGE 2, BUAT APPROVAL KEDUA
        // Biarkan stage 1 selesai dulu, nanti stage 2 dibuat otomatis

        DB::commit();

        /**
         * =========================================================================
         * 🔑 TRIGGER NOTIFIKASI DI SINI (SETELAH DB COMMIT)
         * =========================================================================
         */
        // 1. Ambil semua user dengan role 'kadept'
        $kadeptUsers = User::where('role', 'kadept')->get();

        // 2. Susun detail informasi cetakan notifikasinya
        $details = [
            'id_pindah' => $pindah->id_pindah, // Ambil ID perpindahan yang baru dibuat
            'title'     => 'Pengajuan Mutasi Aset Baru',
            'message'   => "Aset dengan kode {$aset->kode_aset} memerlukan persetujuan Anda pada Stage 1.",
            'icon'      => 'fas fa-file-alt',
            'bg_color'  => 'bg-success', // Nuansa hijau BAZNAS
            'url'       => route('approvals.index') // Sesuaikan dengan route halaman approval kadept kamu
        ];

        // 3. Blast notifikasi ke semua Kadept (Otomatis masuk database navbar & kirim email)
        foreach ($kadeptUsers as $kadept) {
            $kadept->notify(new AssetMutationNotification($details));
        }

        if ($logoutAfterSuccess) {
            $redirectUrl = URL::signedRoute('assets.scan', [
                'kode_aset' => $aset->kode_aset,
            ]);

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'success' => true,
                'logout' => true,
                'redirect_url' => $redirectUrl,
                'message' => 'Permintaan pindah aset berhasil dikirim. Sesi Anda telah diakhiri dan halaman kembali ke akses publik.'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Permintaan pindah aset berhasil dikirim. Menunggu approval dari Kepala Departemen.'
        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

public function scanLoginBridge(Request $request)
{
    // 1. Ambil URL halaman scan asli beserta signature-nya (UTUH TANPA DIUBAH)
    $targetUrl = $request->query('target');

    // 2. Masukkan URL utuh tersebut ke intended target pasca-login
    session(['url.intended' => $targetUrl]);

    // 3. TITIPKAN FLASH SESSION sebagai penanda bahwa setelah login, modal harus terbuka
    session()->flash('trigger_pindah_scan', true);

    // 4. Lempar ke halaman login
    return redirect()->route('login');
}

public function scanQr($kode_aset)
{
    $aset = Asset::where('kode_aset', $kode_aset)
        ->with([
            'barang',
            'kategori',
            'jenis',
            'lokasi',
            'amil',
            'sumberDana'
        ])
        ->firstOrFail();

    // 1. AMBIL DATA MASTER AMIL DAN LOKASI UNTUK DROPDOWN MODAL PINDAH
    $amils = Amil::orderBy('nama', 'asc')->get();
    $lokasis = Lokasi::orderBy('nama_lokasi', 'asc')->get();
    $hasPendingPindah = PindahAset::where('id_aset', $aset->id_aset)
        ->where('status', 'menunggu')
        ->exists();

    // 2. KIRIMKAN SEMUANYA (aset, amils, lokasis) KE VIEW SCAN RESULT
    return view('superadmin.aset.data_aset.scan-result', compact('aset', 'amils', 'lokasis', 'hasPendingPindah'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'merk'    => 'nullable|string|max:100',
        'volume'  => 'required|integer|min:1',
        'satuan'  => 'nullable|string|max:20',
        'harga'   => 'required|numeric|min:0',
        'kondisi' => 'required|in:baik,rusak,hilang',
        'status'  => 'required|in:aktif,nonaktif',
    ]);

    $aset = Asset::findOrFail($id);
    $kondisiSebelumnya = $aset->kondisi;

    $totalHarga = $request->volume * $request->harga;

    // Update data informasi umum aset
    $aset->update([
        'merk'        => $request->merk,
        'volume'      => $request->volume,
        'satuan'      => $request->satuan,
        'harga'       => $request->harga,
        'total_harga' => $totalHarga,
        'kondisi'     => $request->kondisi,
        // Jika diubah ke rusak, status operasional OTOMATIS dikunci ke 'nonaktif'
        'status'      => in_array($request->kondisi, ['rusak', 'hilang']) ? 'nonaktif' : $request->status,
    ]);

    // =========================================================================
    // ⚡ TRIGGER OTOMATIS 3: SINKRONISASI UPDATE HARGA KE LOG PENYUSUTAN (BARU)
    // =========================================================================
    // Ambil rekor log penyusutan milik aset ini yang statusnya MASIH MENUNGGU AUDIT
    $logPenyusutanAktif = \App\Models\PenyusutanAset::where('id_aset', $aset->id_aset)
        ->where('status', 'menunggu')
        ->whereNull('requested_by') // Pastikan ini log murni dari engine tahunan
        ->first();

    if ($logPenyusutanAktif) {
        // Ambil settingan master penyusutan untuk menghitung ulang persentase
        $setting = \App\Models\MasterPenyusutan::where('id_jenis', $aset->id_jenis)
            ->where('status', 'aktif')
            ->first();

        if ($setting) {
            $nilaiResiduMinimumTotal = $totalHarga * 0.10; // Batas 10%
            $persentaseTahunan = $setting->persentase / 100;

            $nilaiBukuSebelumSusut = $totalHarga;
            $penyusutanTahunIni = $totalHarga * $persentaseTahunan;
            $ruangPenyusutan = max($nilaiBukuSebelumSusut - $nilaiResiduMinimumTotal, 0);
            $totalNilaiPenyusutan = min($penyusutanTahunIni, $ruangPenyusutan);
            $totalNilaiSetelah = max(
                $nilaiBukuSebelumSusut - $totalNilaiPenyusutan,
                $nilaiResiduMinimumTotal
            );

            // Update baris log di tabel penyusutan_aset agar angkanya sinkron
            $logPenyusutanAktif->update([
                'nilai_awal'               => $totalHarga,
                'nilai_penyusutan'         => $totalNilaiPenyusutan,
                'nilai_setelah_penyusutan' => $totalNilaiSetelah,
            ]);

        }
    }

    // TRIGGER OTOMATIS: Jika kondisi berubah dari Baik -> Rusak
    if ($kondisiSebelumnya != 'rusak' && $request->kondisi == 'rusak') {
        \App\Models\Maintenance::create([
            'id_aset'         => $aset->id_aset,
            'jenis'           => 'perbaikan',
            'status'          => 'proses', // Langsung berstatus proses pengerjaan
            'biaya'           => 0,
            'tanggal_mulai'   => now(), // Tanggal mulai adalah hari ini
            'tanggal_selesai' => null,
            'durasi'          => 'Menunggu Pemeriksaan', // Akan di-update berdasarkan tingkat kerusakan
            'deskripsi'       => 'Aset dilaporkan rusak dari menu data aset. Menunggu pemeriksaan tingkat kerusakan oleh teknisi.',
        ]);
    }

    // =========================================================================
    // 🔍 TRIGGER OTOMATIS 2: Antisipasi jika diubah MANUAL dari Hilang -> Baik/Rusak
    // (Jika dari menu induk diubah dari hilang ke kondisi lain, bersihkan record hilangnya)
    // =========================================================================
    if ($kondisiSebelumnya == 'hilang' && $request->kondisi != 'hilang') {
        // Cek apakah ada record kronologi aktif di tabel aset_hilang
        if ($aset->asetHilangRecord) {
            $aset->asetHilangRecord->delete(); // Softdelete record kehilangannya
        }
    }

    // Berikan respons pesan sukses yang adaptif
    $pesan = 'Data aset berhasil diperbarui.';
    if ($request->kondisi == 'hilang') {
        return redirect()
            ->route('hilang.index')
            ->with('warning', 'Aset berhasil diubah ke kondisi Hilang. Silakan lengkapi kronologi Berita Acara.');
    }

    return redirect()
        ->route('assets.index')
        ->with('success', $pesan);
}

public function getJsonDetail($id)
{
    // Mengambil data terupdate beserta relasi lokasi dan amil barunya
    $aset = Asset::with(['barang', 'lokasi', 'amil', 'kategori', 'jenis', 'sumber_dana'])->findOrFail($id);
    return response()->json($aset);
}

private function buildImportHeaderMap(array $headerRow): array
{
    $map = [];

    foreach ($headerRow as $index => $header) {
        $normalized = $this->normalizeImportHeader($header);

        if ($normalized !== '' && !array_key_exists($normalized, $map)) {
            $map[$normalized] = $index;
        }
    }

    return $map;
}

private function normalizeImportHeader($value): string
{
    $normalized = strtolower(trim((string) $value));
    $normalized = str_replace(['_', '-', '/', '\\', '.', ':', '(', ')'], ' ', $normalized);
    $normalized = preg_replace('/\s+/', ' ', $normalized);

    return trim($normalized);
}

private function importCell(array $row, array $headerMap, array $headerCandidates, ?int $fallbackIndex = null)
{
    foreach ($headerCandidates as $candidate) {
        $key = $this->normalizeImportHeader($candidate);

        if (array_key_exists($key, $headerMap)) {
            return $row[$headerMap[$key]] ?? '';
        }
    }

    if ($fallbackIndex !== null) {
        return $row[$fallbackIndex] ?? '';
    }

    return '';
}

private function importValueFilled($value): bool
{
    return $value !== null && trim((string) $value) !== '';
}

private function resolveImportFinancialValues($volumeRaw, $hargaRaw, $totalRaw, ?Asset $asset = null): array
{
    $volume = $this->importValueFilled($volumeRaw)
        ? (int) round($this->parseImportNumber($volumeRaw, 1))
        : (int) ($asset->volume ?? 1);
    $volume = $volume > 0 ? $volume : 1;

    $harga = $this->importValueFilled($hargaRaw)
        ? $this->parseImportNumber($hargaRaw, 0)
        : (float) ($asset->harga ?? 0);

    if ($this->importValueFilled($hargaRaw)) {
        $totalHarga = $volume * $harga;
    } elseif ($this->importValueFilled($totalRaw)) {
        $totalHarga = $this->parseImportNumber($totalRaw, 0);
        $harga = $totalHarga > 0 ? ($totalHarga / $volume) : 0;
    } else {
        $totalHarga = $volume * $harga;
    }

    return [$volume, $harga, $totalHarga];
}

private function parseImportNumber($value, float $default = 0): float
{
    if ($value === null || $value === '') {
        return $default;
    }

    if (is_numeric($value)) {
        return max(0, (float) $value);
    }

    $number = trim((string) $value);
    $number = str_ireplace(['rp', 'idr'], '', $number);
    $number = preg_replace('/[^0-9,.\-]/', '', $number);

    if ($number === '' || $number === '-') {
        return $default;
    }

    if (str_contains($number, ',') && str_contains($number, '.')) {
        if (strrpos($number, ',') > strrpos($number, '.')) {
            $number = str_replace('.', '', $number);
            $number = str_replace(',', '.', $number);
        } else {
            $number = str_replace(',', '', $number);
        }
    } elseif (str_contains($number, ',')) {
        $parts = explode(',', $number);
        $lastPart = end($parts);

        if (count($parts) > 2 || strlen($lastPart) === 3) {
            $number = str_replace(',', '', $number);
        } else {
            $number = str_replace(',', '.', $number);
        }
    } elseif (str_contains($number, '.')) {
        $parts = explode('.', $number);
        $lastPart = end($parts);

        if (count($parts) > 2 || strlen($lastPart) === 3) {
            $number = str_replace('.', '', $number);
        }
    }

    return is_numeric($number) ? max(0, (float) $number) : $default;
}

private function syncPendingPenyusutanAfterAssetValueChange(Asset $aset, float $totalHarga): void
{
    $logPenyusutanAktif = \App\Models\PenyusutanAset::where('id_aset', $aset->id_aset)
        ->where('status', 'menunggu')
        ->whereNull('requested_by')
        ->first();

    if (!$logPenyusutanAktif) {
        return;
    }

    $setting = \App\Models\MasterPenyusutan::where('id_jenis', $aset->id_jenis)
        ->where('status', 'aktif')
        ->first();

    if (!$setting) {
        return;
    }

    $nilaiResiduMinimumTotal = $totalHarga * 0.10;
    $persentaseTahunan = $setting->persentase / 100;

    $nilaiBukuSebelumSusut = $totalHarga;
    $penyusutanTahunIni = $totalHarga * $persentaseTahunan;
    $ruangPenyusutan = max($nilaiBukuSebelumSusut - $nilaiResiduMinimumTotal, 0);
    $totalNilaiPenyusutan = min($penyusutanTahunIni, $ruangPenyusutan);
    $totalNilaiSetelah = max(
        $nilaiBukuSebelumSusut - $totalNilaiPenyusutan,
        $nilaiResiduMinimumTotal
    );

    $logPenyusutanAktif->update([
        'nilai_awal'               => $totalHarga,
        'nilai_penyusutan'         => $totalNilaiPenyusutan,
        'nilai_setelah_penyusutan' => $totalNilaiSetelah,
    ]);

}

public function import(Request $request)
    {
        // 1. Validasi Awal: Pastikan Master Data Utama Tidak Kosong
        if (Barang::count() === 0 || Kategori::count() === 0 || Lokasi::count() === 0 || Amil::count() === 0 || Sumber::count() === 0) {
            return back()->with('error', 'Import gagal. Pastikan seluruh data master (Barang, Kategori, Jenis, Lokasi, Amil, Sumber Dana) sudah di-input terlebih dahulu.');
        }

        // 2. Validasi File Upload
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        // Load file excel ke dalam array
        $rows = IOFactory::load($request->file('file')->getPathname())->getActiveSheet()->toArray();
        $headerMap = $this->buildImportHeaderMap($rows[0] ?? []);

        DB::beginTransaction();
        
        $skipped = [];
        $inserted = 0;
        $updated = 0;
        
        // === TRACKER INTERNAL ANTI-DUPLICATE DALAM MEMORI TRANSAKSI ===
        $generatedCodes = []; 

        // Ambil 1 data Amil default sebagai penanggung jawab awal jika di Excel tidak tercatat
        $defaultAmil = Amil::whereHas('jabatan', function($query) {
        // 1. Kunci jabatannya: Harus ada kata 'kepala' atau 'ketua'
        $query->where('nama_jabatan', 'LIKE', '%kepala%')
            ->orWhere('nama_jabatan', 'LIKE', '%ketua%');
        })
        ->whereHas('divisi', function($query) {
            // 2. Kunci divisinya: Harus berasal dari Divisi Sauh
            $query->where('nama_divisi', 'LIKE', '%sauh%');
        })
        // 3. Kunci departemennya: Langsung tembak kolom di tabel amils sendiri!
        ->where('nama_dept', 'LIKE', '%general affair%') 
        ->first();

        // JARING PENGAMAN AKHIR (FALLBACK LOGIC)
        // Sekarang fallback ini dijamin aman dan hanya jalan jika data kamu benar-benar belum di-input admin
        if (!$defaultAmil) {
            $defaultAmil = Amil::where('nama', 'LIKE', '%inal%')
                                ->first() ?? Amil::first();
        }

        try {
            foreach ($rows as $index => $row) {
                // Lewati baris pertama (Header warna kuning pada Excel BAZNAS)
                if ($index === 0) continue;

                // Bersihkan spasi liar di setiap kolom
                $cleanRow = array_map(function($val) {
                    return $val !== null ? trim($val) : '';
                }, $row);

                // Mapping kolom tetap kompatibel dengan format lama, tetapi bisa membaca header baru.
                $txtBarang   = $this->importCell($cleanRow, $headerMap, ['nama barang', 'barang'], 2);
                $merk        = $this->importCell($cleanRow, $headerMap, ['merek', 'merk'], 3);
                $txtWarna    = $this->importCell($cleanRow, $headerMap, ['warna'], 4);
                $txtLokasi   = $this->importCell($cleanRow, $headerMap, ['ruangan', 'lokasi', 'lokasi ruangan'], 5);
                $txtKategori = $this->importCell($cleanRow, $headerMap, ['kategori'], 6);
                $txtJenis    = $this->importCell($cleanRow, $headerMap, ['jenis', 'jenis aset'], 7);
                $txtSumber   = $this->importCell($cleanRow, $headerMap, ['sumber dana', 'sumber'], 8);
                $tahun       = $this->importCell($cleanRow, $headerMap, ['tahun beli', 'tahun perolehan', 'tahun'], 9) ?: date('Y');
                
                // === AMBIL DATA NO URUT ASLI EXCEL DARI KOLOM K (INDEX 10) ===
                $noUrutExcel = $this->importCell($cleanRow, $headerMap, ['no urut regist', 'no urut register', 'no urut', 'nomor urut'], 10);
                
                $statusExcel = $this->importCell($cleanRow, $headerMap, ['status kondisi', 'status', 'kondisi'], 11) ?: 'Baik';
                $keterangan  = $this->importCell($cleanRow, $headerMap, ['keterangan', 'ket'], 12);

                $volumeRaw = $this->importCell($cleanRow, $headerMap, ['volume', 'volume unit', 'jumlah', 'jumlah unit', 'qty', 'qty unit', 'kuantitas']);
                $hargaRaw = $this->importCell($cleanRow, $headerMap, ['harga satuan', 'harga satuan rp', 'harga', 'harga rp', 'nilai satuan', 'nilai satuan rp', 'harga unit', 'harga per unit']);
                $totalHargaRaw = $this->importCell($cleanRow, $headerMap, ['total harga', 'total harga rp', 'harga total', 'harga total rp', 'nilai total', 'nilai total rp', 'total nilai', 'total nilai rp', 'nilai aset', 'nilai aset rp', 'nilai perolehan', 'nilai perolehan rp']);
                $hasFinancialValue = $this->importValueFilled($volumeRaw)
                    || $this->importValueFilled($hargaRaw)
                    || $this->importValueFilled($totalHargaRaw);

                [$volumeImport, $hargaImport, $totalHargaImport] = $this->resolveImportFinancialValues(
                    $volumeRaw,
                    $hargaRaw,
                    $totalHargaRaw
                );

                // Jika kolom nama barang kosong, abaikan baris tersebut
                if (!$txtBarang) {
                    continue;
                }

                // 3. PENCARIAN ID DATA MASTER BERDASARKAN TEKS EXCEL
                $barang   = Barang::whereRaw('LOWER(nama_barang) = ?', [strtolower($txtBarang)])->first();
                $kategori = Kategori::whereRaw('LOWER(nama_kategori) = ?', [strtolower($txtKategori)])->first();
                $jenis    = Jenis::whereRaw('LOWER(nama_jenis) = ?', [strtolower($txtJenis)])->first();
                $lokasi   = Lokasi::whereRaw('LOWER(nama_lokasi) = ?', [strtolower($txtLokasi)])->first();
                $sumber   = Sumber::whereRaw('LOWER(nama_sumber_dana) = ?', [strtolower($txtSumber)])->first();

                // Jika salah satu referensi master tidak ditemukan, skip dan catat barisnya
                if (!$barang || !$kategori || !$jenis || !$lokasi || !$sumber) {
                    $skipped[] = "Baris " . ($index + 1) . " ({$txtBarang}): Gagal karena data Master (Kategori/Jenis/Lokasi/Sumber Dana) belum terdaftar di aplikasi.";
                    continue;
                }

                // 4. STANDARISASI STATUS EXCEL MENJADI KONDISI DATABASE (baik, rusak, hilang)
                $kondisiDB = 'baik';
                if (Str::contains(strtolower($statusExcel), ['rusak', 'jelek'])) {
                    $kondisiDB = 'rusak';
                } elseif (Str::contains(strtolower($statusExcel), 'hilang')) {
                    $kondisiDB = 'hilang';
                }

                // 5. GABUNGKAN DATA SPESIFIKASI (Warna & Keterangan) KE KOLOM MERK
                $detailMerk = $merk ?: '-';
                if ($txtWarna) {
                    $detailMerk .= " (Warna: " . $txtWarna . ")";
                }
                if ($keterangan) {
                    $detailMerk .= " [Ket: " . $keterangan . "]";
                }

                // 6. SINKRONISASI KODE ASET BERFORMAT TITIK (MENIRU LOGIKA STORE MANUAL)
                $kodeLokasi   = str_pad($lokasi->kode_lokasi, 2, '0', STR_PAD_LEFT);
                $kodeKategori = str_pad($kategori->kode_kategori, 2, '0', STR_PAD_LEFT);
                $kodeJenis    = str_pad($jenis->kode_jenis, 2, '0', STR_PAD_LEFT);
                $kodeBarang   = str_pad($barang->kode_barang, 2, '0', STR_PAD_LEFT);
                $kodeSumber   = $sumber->kode_sumber_dana;
                $tahunDuaDig  = substr($tahun, -2);

                if ($noUrutExcel !== '' && is_numeric($noUrutExcel)) {
                    $urutDariExcel = str_pad((int) $noUrutExcel, 3, '0', STR_PAD_LEFT);
                    $kodeAsetDariExcel = "{$kodeLokasi}.{$kodeKategori}.{$kodeJenis}.{$kodeBarang}.{$kodeSumber}.{$tahunDuaDig}.{$urutDariExcel}";
                    $asetExisting = Asset::where('kode_aset', $kodeAsetDariExcel)->first();

                    if ($asetExisting && $hasFinancialValue) {
                        [$volumeUpdate, $hargaUpdate, $totalHargaUpdate] = $this->resolveImportFinancialValues(
                            $volumeRaw,
                            $hargaRaw,
                            $totalHargaRaw,
                            $asetExisting
                        );

                        $asetExisting->update([
                            'volume'      => $volumeUpdate,
                            'harga'       => $hargaUpdate,
                            'total_harga' => $totalHargaUpdate,
                        ]);

                        $this->syncPendingPenyusutanAfterAssetValueChange($asetExisting->fresh(), $totalHargaUpdate);
                        $updated++;
                        continue;
                    }
                }

                // =========================================================================
                // PERBAIKAN LOGIKA NOMOR URUT: PRIORITAS NO URUT EXCEL -> FALLBACK AUTO INCREMENT
                // =========================================================================
                if ($noUrutExcel !== '' && is_numeric($noUrutExcel)) {
                    // Jika kolom K di Excel tercatat angkanya, pakai angka tersebut secara mutlak
                    $nextUrut = (int) $noUrutExcel;
                } else {
                    // Jika kolom K kosong (karena stiker luntur), baru sistem mencari nomor urut terakhir di database real
                    $lastAset = Asset::where('id_lokasi', $lokasi->id_lokasi)
                        ->where('id_kategori', $kategori->id_kategori)
                        ->orderBy('id_aset', 'desc')
                        ->first();

                    $nextUrut = 1;
                    if ($lastAset) {
                        $nextUrut = ((int) substr($lastAset->kode_aset, -3)) + 1;
                    }
                }

                // LOOPING VALIDASI BERDASARKAN DATABASE + MEMORI ARRAY INTERNAL TRACKER
                do {
                    $urut = str_pad($nextUrut, 3, '0', STR_PAD_LEFT);
                    $kodeAsetResmi = "{$kodeLokasi}.{$kodeKategori}.{$kodeJenis}.{$kodeBarang}.{$kodeSumber}.{$tahunDuaDig}.{$urut}";
                    
                    // Cek apakah kode sudah terpakai di database nyata OR sudah ada di list antrean memori array
                    $isDuplicateInDb    = Asset::where('kode_aset', $kodeAsetResmi)->exists();
                    $isDuplicateInQueue = in_array($kodeAsetResmi, $generatedCodes);
                    
                    if ($isDuplicateInDb || $isDuplicateInQueue) {
                        $nextUrut++; // Jika duplikat/tabrakan, otomatis naikkan nomor urutnya ke urutan berikutnya
                        $isDuplicate = true;
                    } else {
                        $isDuplicate = false;
                    }
                } while ($isDuplicate);

                // Masukkan kode resmi yang sudah divalidasi aman ke dalam list memori array tracker
                $generatedCodes[] = $kodeAsetResmi;
                // =========================================================================

                // 7. OTOMATISASI GENERATE FILE QR CODE PER BARIS DATA IMPORT
                $qrDirectory = public_path('qr-assets');
                if (!file_exists($qrDirectory)) {
                    mkdir($qrDirectory, 0755, true);
                }

                $qrFileName = 'qr_' . Str::uuid() . '.png';
                $qrPath     = 'qr-assets/' . $qrFileName;
                $signedUrl  = URL::signedRoute('assets.scan', [
                    'kode_aset' => $kodeAsetResmi
                ]);

                // Eksekusi pembuatan file PNG QR Code
                $resultQr = Builder::create()
                    ->writer(new PngWriter())
                    ->data($signedUrl)
                    ->size(300)
                    ->margin(5)
                    ->build();

                $resultQr->saveToFile(public_path($qrPath));

                // 8. INSERT DATA KE DATABASE DENGAN NILAI FINANSIAL DARI EXCEL JIKA TERSEDIA
                Asset::create([
                    'kode_aset'       => $kodeAsetResmi, // Kode resmi terstruktur
                    'id_barang'       => $barang->id_barang,
                    'id_kategori'     => $kategori->id_kategori,
                    'id_jenis'        => $jenis->id_jenis,
                    'id_lokasi'       => $lokasi->id_lokasi,
                    'id_amil'         => $defaultAmil->id_amil, // Otomatis diarahkan ke akun penanggung jawab default
                    'id_sumber_dana'  => $sumber->id_sumber_dana,
                    'merk'            => $detailMerk, // Menyimpan kombinasi Merk + Warna + Keterangan Excel
                    'tahun'           => $tahun,
                    'volume'          => $volumeImport,
                    'satuan'          => 'Unit',      // Default value
                    'harga'           => $hargaImport,
                    'total_harga'     => $totalHargaImport,
                    'kondisi'         => $kondisiDB,  // Hasil konversi status dari Excel
                    'status'          => 'aktif',     // Default value: Langsung siap beroperasi
                    'qr_code'         => $qrPath,     // Menyimpan path file QR Code yang baru dibuat
                ]);

                $inserted++;
            }

            DB::commit();

            $hasilImport = "Berhasil menyimpan {$inserted} data aset baru";
            if ($updated > 0) {
                $hasilImport .= " dan memperbarui nilai {$updated} data aset lama";
            }
            $hasilImport .= '.';

            // Jika ada baris data yang terlewat (karena nama master belum terdaftar)
            if ($skipped) {
                return back()->with([
                    'warning'    => "Proses import selesai. {$hasilImport}",
                    'duplicates' => $skipped
                ]);
            }

            return back()->with('success', "Luar biasa! Sukses import Excel. {$hasilImport}");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Proses eksekusi import hancur total: ' . $e->getMessage());
        }
    }

    public function overtakeAset(Request $request)
{
    $request->validate([
        'id_amil_lama' => 'required|exists:amils,id_amil',
        'id_amil_baru' => 'required|exists:amils,id_amil',
    ]);

    DB::beginTransaction();
    try {
        // Ambil alih semua aset dalam satu baris perintah
        $jumlahAset = Asset::where('id_amil', $request->id_amil_lama)
                           ->update(['id_amil' => $request->id_amil_baru]);

        DB::commit();
        return back()->with('success', "Sukses mutasi! Sebanyak {$jumlahAset} aset telah berhasil dialihkan tanggung jawabnya.");
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal memindahkan aset: ' . $e->getMessage());
    }
}

public function reportIndex()
{
    // Mengambil data master untuk pilihan dropdown di form filter
    $lokasis   = Lokasi::orderBy('nama_lokasi', 'asc')->get();
    $kategoris = Kategori::orderBy('nama_kategori', 'asc')->get();
    $sumbers   = Sumber::orderBy('nama_sumber_dana', 'asc')->get();
    
    // 🔑 KUNCI PERBAIKAN: Ambil data master Jenis Aset agar Card 4 & 5 tidak crash!
    // Catatan: Sesuaikan nama Model 'Jenis' jika di proyekmu namanya berbeda (misal: JenisAsset)
    $jenises   = \App\Models\Jenis::orderBy('nama_jenis', 'asc')->get(); 

    // Mengambil list tahun unik yang ada di database untuk filter tahun beli
    $tahuns = Asset::select('tahun')
        ->distinct()
        ->orderBy('tahun', 'desc')
        ->pluck('tahun');

    // 🔑 FIXED: Tambahkan 'jenises' ke dalam compact
    return view('superadmin.reports.index', compact('lokasis', 'kategoris', 'sumbers', 'tahuns', 'jenises'));
}

public function verifyReport(Request $request)
{
    return view('superadmin.reports.verify-public', [
        'document' => $request->query('document', 'Laporan'),
        'role' => $request->query('role', '-'),
        'signer' => $request->query('signer', '-'),
        'printedBy' => $request->query('printed_by', '-'),
        'printedAt' => $request->query('printed_at', '-'),
        'fingerprint' => $request->query('fingerprint', '-'),
        'signature' => $request->query('digital_signature', '-'),
    ]);
}

private function makeReportVerificationUrl($document, $role, $signer, $printedBy, $printedAt, $fingerprint, $signature)
{
    return URL::signedRoute('reports.verify', [
        'document' => $document,
        'role' => $role,
        'signer' => $signer,
        'printed_by' => $printedBy,
        'printed_at' => $printedAt,
        'fingerprint' => 'KEY-' . $fingerprint,
        'digital_signature' => substr($signature, 0, 25) . '...',
    ]);
}

public function reportAssetPrint(Request $request)
{
    // 1. Logika Query & Filtering Aset (Tetap sama seperti kode kamu)
    $query = Asset::with(['barang', 'kategori', 'jenis', 'lokasi', 'amil', 'sumberDana', 'penyusutanRecords']);
    if ($request->filled('id_lokasi')) { $query->where('id_lokasi', $request->id_lokasi); }
    if ($request->filled('id_kategori')) { $query->where('id_kategori', $request->id_kategori); }
    if ($request->filled('id_sumber_dana')) { $query->where('id_sumber_dana', $request->id_sumber_dana); }
    if ($request->filled('tahun')) { $query->where('tahun', $request->tahun); }
    if ($request->filled('kondisi')) { $query->where('kondisi', $request->kondisi); }

    $assets = $query->orderBy('kode_aset', 'asc')->get();
    $totalVolume = $assets->sum('volume');
    $totalHarga  = $assets->sum(function ($asset) {
        return $asset->nilai_buku;
    });
    $totalNilaiPerolehan = $assets->sum(function ($asset) {
        return $asset->nilai_perolehan;
    });

    $filterInfo = [];
    if ($request->filled('id_lokasi')) { $filterInfo[] = 'Lokasi: ' . \App\Models\Lokasi::find($request->id_lokasi)->nama_lokasi; }
    $stringFilter = count($filterInfo) > 0 ? implode(' | ', $filterInfo) : 'Semua Data';

    // Metadata Audit Cetak
    $printedBy = $request->user()->name;
    $role      = $request->user()->role;
    $timestamp = now()->format('d M Y, H:i:s');

    // 2. Ambil Data User Pimpinan Aktif
    $userKadept = User::where('role', 'kadept')->first();
$userKadiv  = User::where('role', 'kadiv')->first();

$kepalaDept   = $userKadept->name ?? '........................................';
$kepalaDivisi = $userKadiv->name ?? '........................................';

// --- 🔑 PERBAIKAN: DIGITAL SIGNATURE INSTAN DENGAN SYSTEM KEY (ANTI-ERROR) ---

// 1. Ambil ID atau Kode unik pimpinan sebagai Fingerprint penanda
$fingerprintKadept = $userKadept ? substr(md5($userKadept->id_user . 'kadept'), 0, 12) : 'KADEPT-EMPTY';
$fingerprintKadiv  = $userKadiv ? substr(md5($userKadiv->id_user . 'kadiv'), 0, 12) : 'KADIV-EMPTY';

// 2. Kunci Data Laporan Menggunakan Kriptografi HMAC-SHA256 berbasis Data Manifes
$manifestKadept = "BAZNAS JABAR | Jenis: Laporan Aset | Oleh Kadept: {$kepalaDept} | Waktu: {$timestamp} | Nilai: Rp " . number_format($totalHarga, 0, ',', '.');
$sigKadept = base64_encode(hash_hmac('sha256', $manifestKadept, 'SECRET_KEY_SYSTEM_BAZNAS_JABAR'));

$manifestKadiv = "BAZNAS JABAR | Jenis: Laporan Aset | Oleh Kadiv: {$kepalaDivisi} | Waktu: {$timestamp} | Nilai: Rp " . number_format($totalHarga, 0, ',', '.');
$sigKadiv = base64_encode(hash_hmac('sha256', $manifestKadiv, 'SECRET_KEY_SYSTEM_BAZNAS_JABAR'));

// 3. Susun link verifikasi publik untuk QR Code masing-masing
$qrDataKadept = $this->makeReportVerificationUrl('Laporan Inventaris Aset', 'Kepala Departemen', $kepalaDept, $printedBy, $timestamp, $fingerprintKadept, $sigKadept);
$qrDataKadiv  = $this->makeReportVerificationUrl('Laporan Inventaris Aset', 'Kepala Divisi', $kepalaDivisi, $printedBy, $timestamp, $fingerprintKadiv, $sigKadiv);

// Log Tracker untuk bagian tengah (Audit Stamp)
$qrCodeSystem = "SIMASET AUDIT STAMP | Operator Cetak: {$printedBy} ({$role}) | Waktu: {$timestamp} | Status Data: Valid.";

    // 4. Kirim Notifikasi Pengawasan Ke Email Pimpinan (Kadiv)
    try {
        if ($userKadiv) {
            $userKadiv->notify(new \App\Notifications\AssetMutationNotification([
                'id_pindah' => null,
                'title'     => 'Log Pengawasan: Cetak Laporan Aset',
                'message'   => "User '{$printedBy}' ({$role}) baru saja mendownload/mencetak Laporan Inventaris Aset. Tanda tangan digital otomatis tersemat menggunakan Key ID Anda.",
                'icon'      => 'fas fa-print',
                'bg_color'  => 'bg-info',
                'url'       => url('/laporan-aset')
            ]));
        }
        sleep(2);

    // 2. Notif ke Kadept
    if ($userKadept) {
        $userKadept->notify(new \App\Notifications\AssetMutationNotification([
            'id_pindah' => null,
            'title'     => 'Log Pengawasan: Cetak Laporan Aset',
            'message'   => "User '{$printedBy}' ({$role}) baru saja mendownload Laporan Inventaris Aset.",
            'icon'      => 'fas fa-print',
            'bg_color'  => 'bg-info',
            'url'       => url('/laporan-aset')
        ]));
    }
    } catch (\Exception $eMail) { }

    return view('superadmin.reports.print_asset', compact(
    'assets', 'totalVolume', 'totalHarga', 'totalNilaiPerolehan', 'stringFilter', 
    'qrDataKadept', 'qrDataKadiv', 'qrCodeSystem', 'printedBy', 'timestamp', 'kepalaDivisi', 'kepalaDept'
));
}

public function reportMutasiPrint(Request $request)
{
    // Eager loading relasi berdasarkan model PindahAset kamu
    $query = PindahAset::with(['asset.barang', 'amilSebelum', 'amilSesudah', 'lokasiBaru', 'requestedBy']);

    // --- LOGIKA FILTERING ---
    if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
        $query->whereBetween('created_at', [$request->tanggal_awal . ' 00:00:00', $request->tanggal_akhir . ' 23:59:59']);
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Ambil data hasil filter rekap
    $mutasis = $query->orderBy('created_at', 'desc')->get();

    // Menyusun string informasi parameter filter untuk judul dokumen resmi
    $filterInfo = [];
    if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
        $filterInfo[] = 'Periode: ' . date('d/m/Y', strtotime($request->tanggal_awal)) . ' s.d ' . date('d/m/Y', strtotime($request->tanggal_akhir));
    }
    if ($request->filled('status')) {
        $filterInfo[] = 'Status: ' . strtoupper($request->status);
    }
    
    $stringFilter = count($filterInfo) > 0 ? implode(' | ', $filterInfo) : 'Semua Riwayat Pergerakan Aset';

    // Metadata Audit Cetak
    $printedBy = $request->user()->name;
    $role      = $request->user()->role;
    $timestamp = now()->format('d M Y, H:i:s');

    // Ambil data user pimpinan aktif
    $userKadept = User::where('role', 'kadept')->first();
    $userKadiv  = User::where('role', 'kadiv')->first();

    $kepalaDept   = $userKadept->name ?? '........................................';
    $kepalaDivisi = $userKadiv->name ?? '........................................';

    // --- 🔑 DIGITAL SIGNATURE DOUBLE TTD INSTAN (MUTASI) ---
    $fingerprintKadept = $userKadept ? substr(md5($userKadept->id_user . 'kadept_mutasi'), 0, 12) : 'KADEPT-EMPTY';
    $fingerprintKadiv  = $userKadiv ? substr(md5($userKadiv->id_user . 'kadiv_mutasi'), 0, 12) : 'KADIV-EMPTY';

    $manifestKadept = "BAZNAS JABAR | Jenis: Laporan Mutasi | Oleh Kadept: {$kepalaDept} | Waktu: {$timestamp} | Total: {$mutasis->count()} Transaksi";
    $sigKadept = base64_encode(hash_hmac('sha256', $manifestKadept, 'SECRET_KEY_SYSTEM_BAZNAS_JABAR'));

    $manifestKadiv = "BAZNAS JABAR | Jenis: Laporan Mutasi | Oleh Kadiv: {$kepalaDivisi} | Waktu: {$timestamp} | Total: {$mutasis->count()} Transaksi";
    $sigKadiv = base64_encode(hash_hmac('sha256', $manifestKadiv, 'SECRET_KEY_SYSTEM_BAZNAS_JABAR'));

    $qrDataKadept = $this->makeReportVerificationUrl('Laporan Mutasi Aset', 'Kepala Departemen', $kepalaDept, $printedBy, $timestamp, $fingerprintKadept, $sigKadept);
    $qrDataKadiv  = $this->makeReportVerificationUrl('Laporan Mutasi Aset', 'Kepala Divisi', $kepalaDivisi, $printedBy, $timestamp, $fingerprintKadiv, $sigKadiv);

    // Log Tracker Audit Tengah
    $qrCodeSystem = "SIMASET AUDIT STAMP | Operator Cetak: {$printedBy} ({$role}) | Waktu: {$timestamp} | Status Data: Valid Terintegrasi.";

    // Kirim Notifikasi Pengawasan Ke Email Pimpinan (Kadiv)
    try {
        if ($userKadiv) {
            $userKadiv->notify(new \App\Notifications\AssetMutationNotification([
                'id_pindah' => null,
                'title'     => 'Log Pengawasan: Cetak Laporan Mutasi',
                'message'   => "User '{$printedBy}' ({$role}) baru saja mengunduh Laporan Rekap Mutasi Aset untuk periode filter: {$stringFilter}.",
                'icon'      => 'fas fa-print',
                'bg_color'  => 'bg-info',
                'url'       => url('/laporan-mutasi')
            ]));
        }
        sleep(2);

    if ($userKadept) {
        $userKadept->notify(new \App\Notifications\AssetMutationNotification([
            'id_pindah' => null,
            'title'     => 'Log Pengawasan: Cetak Laporan Mutasi',
            'message'   => "User '{$printedBy}' ({$role}) baru saja mengunduh Laporan Rekap Mutasi Aset untuk periode filter: {$stringFilter}.",
            'icon'      => 'fas fa-print',
            'bg_color'  => 'bg-info',
            'url'       => url('/laporan-mutasi')
        ]));
    }
    } catch (\Exception $eMail) { }

    return view('superadmin.reports.print_mutasi', compact(
        'mutasis', 'stringFilter', 'qrDataKadept', 'qrDataKadiv', 
        'qrCodeSystem', 'printedBy', 'timestamp', 'kepalaDivisi', 'kepalaDept'
    ));
}

public function bulkPrintQr(Request $request)
{
    // Build query dasar dengan eager loading relasi barang
    $query = Asset::with('barang');

    // --- SINKRONISASI FILTER (MENGADOPSI FILTER DARI HALAMAN INDEKS) ---
    if ($request->filled('id_lokasi')) {
        $query->where('id_lokasi', $request->id_lokasi);
    }
    if ($request->filled('id_kategori')) {
        $query->where('id_kategori', $request->id_kategori);
    }
    if ($request->filled('tahun')) {
        $query->where('tahun', $request->tahun);
    }
    if ($request->filled('kondisi')) {
        $query->where('kondisi', $request->kondisi);
    }

    // Ambil semua data hasil filter tanpa pagination (karena mau dicetak stiker semua)
    $assets = $query->orderBy('kode_aset', 'asc')->get();

    // Jaring pengaman jika data kosong
    if ($assets->isEmpty()) {
        return redirect()->back()->with('warning', 'Tidak ada data aset yang tersedia untuk dicetak.');
    }

    return view('superadmin.reports.bulk_qr', compact('assets'));
}

public function selectedPrintQr(Request $request)
{
    $request->validate([
        'asset_ids' => 'required|array'
    ]);

    $assets = Asset::with('barang')
        ->whereIn('id_aset', $request->asset_ids)
        ->orderBy('kode_aset')
        ->get();

    if ($assets->isEmpty()) {
        return back()->with(
            'warning',
            'Tidak ada aset yang dipilih.'
        );
    }

    return view(
        'superadmin.reports.bulk_qr',
        compact('assets')
    );
}
}
