<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\sumber;

class SumberController extends Controller
{
    public function index(Request $request)
    {
        $query = sumber::query();

        if ($request->filled('q')) {
            $query->where('kode_sumber_dana', 'like', '%' . $request->q . '%')
                  ->orWhere('nama_sumber_dana', 'like', '%' . $request->q . '%');
        }

        $sumber = $query->orderBy('kode_sumber_dana', 'asc')->get();

        return view('superadmin.master.sumber.index', compact('sumber'));
    }

    /**
     * Menyimpan data sumber dana baru
     */
    public function store(Request $request)
{
    $request->validate([
        'nama_sumber_dana.*' => 'required|string|max:100|distinct|unique:sumbers,nama_sumber_dana',
    ],[
        'nama_sumber_dana.*.unique' => 'Nama sumber dana sudah digunakan.',
        'nama_sumber_dana.*.required' => 'Nama sumber dana tidak boleh kosong.',
    ]);

    // Ambil kode terakhir
    $lastKode = sumber::orderBy('kode_sumber_dana', 'desc')
                      ->value('kode_sumber_dana');

    $currentCode = $lastKode ? ord($lastKode) : ord('A') - 1;

    foreach ($request->nama_sumber_dana as $nama) {
        $currentCode++;

        sumber::create([
            'kode_sumber_dana' => chr($currentCode),
            'nama_sumber_dana' => $nama,
        ]);
    }

    return redirect()
        ->route('sumber.index')
        ->with('success', 'Sumber Dana berhasil ditambahkan.');
}


public function update(Request $request, $id)
{
    // VALIDASI
    $request->validate([
        'nama_sumber_dana' => 'required|string|max:100|unique:sumbers,nama_sumber_dana,' . $id . ',id_sumber_dana',
    ],[
        'nama_sumber_dana.unique' => 'Nama sumber dana sudah digunakan.',
    ]);

    // AMBIL DATA
    $sumber = sumber::findOrFail($id);

    // UPDATE DATA (kode tidak diubah)
    $sumber->update([
        'nama_sumber_dana' => $request->nama_sumber_dana,
    ]);

    return redirect()
        ->route('sumber.index')
        ->with('success', 'Sumber Dana berhasil diperbarui.');
}
    /**
     * Menghapus data sumber dana
     */
    public function destroy($id)
    {
        $sumber = sumber::findOrFail($id);
        $sumber->delete();

        return redirect()
            ->route('sumber.index')
            ->with('success', 'Sumber Dana berhasil dihapus.');
    }
}
