<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::query();

        if ($request->filled('q')) {
            $query->where('kode_barang', 'like', '%' . $request->q . '%')
                ->orWhere('nama_barang', 'like', '%' . $request->q . '%');
        }
        $barangs = $query->orderByRaw('CAST(kode_barang AS UNSIGNED) ASC')
            ->paginate(10);

        return view('superadmin.master.barang.index', compact('barangs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    
    public function store(Request $request)
{
    $request->validate([
        'nama_barang' => 'required|string|max:100|unique:barangs,nama_barang',
    ],[
            'nama_barang.unique' => 'Nama barang sudah terdaftar.'
    ]);

    $lastKode = barang::max(DB::raw('CAST(kode_barang AS UNSIGNED)')) ?? 0;
    $next = $lastKode + 1;

    $kodeBarang = $next < 100
        ? str_pad($next, 2, '0', STR_PAD_LEFT)
        : (string) $next;

    barang::create([
        'kode_barang' => $kodeBarang,
        'nama_barang' => trim($request->nama_barang),
    ]);

    return back()->with('success', 'Barang berhasil ditambahkan');
}

    /**
     * Export data barang to Excel.
     */
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
        $lastKode = barang::max(DB::raw('CAST(kode_barang AS UNSIGNED)')) ?? 0;
        $counter = (int) $lastKode;

        $duplicateNames = [];
        $inserted = 0;

        foreach ($rows as $index => $row) {
            if ($index === 0) continue;

            $nama = trim($row[0] ?? '');
            if (!$nama) continue;

            $exists = barang::whereRaw(
                'LOWER(nama_barang) = ?',
                [strtolower($nama)]
            )->exists();

            if ($exists) {
                $duplicateNames[] = $nama;
                continue;
            }

            $counter++;

            $kodeBarang = $counter < 100
                ? str_pad($counter, 2, '0', STR_PAD_LEFT)
                : (string) $counter;

            barang::create([
                'kode_barang' => $kodeBarang,
                'nama_barang' => $nama,
            ]);

            $inserted++;
        }

        DB::commit();

        if (count($duplicateNames) > 0) {
            return back()->with([
                'warning' => "Import selesai. {$inserted} data masuk, "
                    . count($duplicateNames)
                    . " data duplikat dilewati.",
                'duplicates' => $duplicateNames
            ]);
        }

        return back()->with('success', 'Import berhasil.');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Import gagal: ' . $e->getMessage());
    }
}


public function update(Request $request, $id)
{
    $request->validate(
        [
            'nama_barang' => 'required|string|max:100|unique:barangs,nama_barang,' . $id . ',id_barang',
        ],
        [
            'nama_barang.unique' => 'Nama barang sudah digunakan oleh data lain.'
        ]
    );

    $barang = barang::findOrFail($id);
    $barang->update([
        'nama_barang' => trim($request->nama_barang),
    ]);

    return back()->with('success', 'Barang berhasil diperbarui.');
}


/**
 * Menghapus data barang
 */
public function destroy($id)
    {
        $barang = barang::findOrFail($id);
        $barang->delete();

        return redirect()
            ->route('barang.index')
            ->with('success', 'Barang berhasil dihapus.');
    }
}
