<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jenis;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class JenisController extends Controller
{
    public function index(Request $request)
    {
        $query = Jenis::query();

        if ($request->filled('q')) {
            $query->where('kode_jenis', 'like', '%' . $request->q . '%')
                  ->orWhere('nama_jenis', 'like', '%' . $request->q . '%');
        }

        $jenis = $query->orderByRaw('CAST(kode_jenis AS UNSIGNED) ASC')->paginate(10);
        return view('superadmin.master.jenis.index', compact('jenis'));
    }

    public function store(Request $request)
{
    $request->validate([
        'items.*.kode_jenis' => 'required|string|max:10|distinct|unique:jenis,kode_jenis',
        'items.*.nama_jenis' => 'required|string|max:100',
    ]);

    foreach ($request->items as $item) {
        Jenis::create([
            'kode_jenis' => $item['kode_jenis'],
            'nama_jenis' => $item['nama_jenis'],
        ]);
    }

    return redirect()
        ->route('jenis.index')
        ->with('success', 'Data jenis berhasil ditambahkan');
}

public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xls,xlsx'
    ]);

    $rows = IOFactory::load($request->file('file')->getPathname())
        ->getActiveSheet()
        ->toArray();

    DB::beginTransaction();

    try {
        $inserted = 0;
        $duplicates = [];

        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // skip header

            $kode = trim($row[0] ?? '');
            $nama = trim($row[1] ?? '');

            if (!$kode || !$nama) continue;

            $exists = Jenis::where('kode_jenis', $kode)->exists();
            if ($exists) {
                $duplicates[] = $kode;
                continue;
            }

            Jenis::create([
                'kode_jenis' => $kode,
                'nama_jenis' => $nama,
            ]);

            $inserted++;
        }

        DB::commit();

        return back()->with([
            'success' => "Import selesai. {$inserted} data berhasil ditambahkan.",
            'duplicates' => $duplicates
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Import gagal: ' . $e->getMessage());
    }
}


public function update(Request $request, $id)
{
    // VALIDASI
    $request->validate([
        'nama_jenis' => 'required|string|max:100',
    ]);

    // AMBIL DATA
    $jenis = Jenis::findOrFail($id);

    // UPDATE DATA (kode tidak diubah)
    $jenis->update([
        'nama_jenis' => $request->nama_jenis,
    ]);

    return redirect()
        ->route('jenis.index')
        ->with('success', 'Jenis berhasil diperbarui.');
}

/**
 * Menghapus data jenis
 */
public function destroy($id)
    {
        $jenis = Jenis::findOrFail($id);
        $jenis->delete();

        return redirect()
            ->route('jenis.index')
            ->with('success', 'Jenis berhasil dihapus.');
    }

}
