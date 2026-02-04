<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lokasi;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;


class LokasiController extends Controller
{
   public function index(Request $request)
    {
        $query = Lokasi::query();

        if ($request->filled('q')) {
            $query->where('kode_lokasi', 'like', '%' . $request->q . '%')
                  ->orWhere('nama_lokasi', 'like', '%' . $request->q . '%');
        }

        $lokasis = $query->orderBy('kode_lokasi', 'asc')
            ->paginate(10);

        return view('superadmin.master.lokasi.index', compact('lokasis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    
    public function store(Request $request)
{
    $request->validate([
        'nama_lokasi' => 'required|string|max:100',
    ]);

    // Ambil kode lokasi terbesar (numerik)
    $lastKode = Lokasi::max('kode_lokasi');

    // Jika belum ada data, mulai dari 1
    $nextNumber = $lastKode ? ((int) $lastKode + 1) : 1;

    // Format jadi 2 digit: 01, 02, ...
    $kodeLokasi = str_pad($nextNumber, 2, '0', STR_PAD_LEFT);

    Lokasi::create([
        'kode_lokasi' => $kodeLokasi,
        'nama_lokasi' => $request->nama_lokasi,
    ]);

    return redirect()
        ->route('lokasi.index')
        ->with('success', 'Lokasi berhasil ditambahkan.');
}

    /**
     * Export data lokasi to Excel.
     */
    public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xls,xlsx'
    ]);

    $file = $request->file('file');
    $spreadsheet = IOFactory::load($file->getPathname());
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    DB::beginTransaction();

    try {
        foreach ($rows as $index => $row) {

            // skip header
            if ($index === 0) continue;

            [$kode, $nama] = $row;

            if (!$kode || !$nama) continue;

            lokasi::create([
                'kode_lokasi' => $kode,
                'nama_lokasi' => $nama,
            ]);
        }

        DB::commit();

        return back()->with('success', 'Import data lokasi berhasil');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Import gagal: ' . $e->getMessage());
    }
}

public function update(Request $request, $id)
{
    // VALIDASI
    $request->validate([
        'nama_lokasi' => 'required|string|max:100',
    ]);

    // AMBIL DATA
    $lokasi = Lokasi::findOrFail($id);

    // UPDATE DATA (kode tidak diubah)
    $lokasi->update([
        'nama_lokasi' => $request->nama_lokasi,
    ]);

    return redirect()
        ->route('lokasi.index')
        ->with('success', 'Lokasi berhasil diperbarui.');
}

/**
 * Menghapus data lokasi
 */
public function destroy($id)
    {
        $lokasi = Lokasi::findOrFail($id);
        $lokasi->delete();

        return redirect()
            ->route('lokasi.index')
            ->with('success', 'Lokasi berhasil dihapus.');
    }
}
