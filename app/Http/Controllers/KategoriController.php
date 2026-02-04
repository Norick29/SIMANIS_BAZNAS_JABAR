<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        $query = Kategori::query();

        if ($request->filled('q')) {
            $query->where('kode_kategori', 'like', '%' . $request->q . '%')
                ->orWhere('nama_kategori', 'like', '%' . $request->q . '%');
        }
        $kategori = $query->orderBy('created_at', 'desc')->get();

        return view('superadmin.master.kategori.index', compact('kategori'));
    }

    /**
     * Menyimpan data kategori baru
     */
    public function store(Request $request)
{
    $request->validate([
        'nama_kategori' => 'required|array|min:1',
        'nama_kategori.*' => 'required|string|max:100'
    ]);

    $names = collect($request->nama_kategori)
        ->map(fn ($v) => trim(strtolower($v)));

    // 🔴 Cek duplikasi dalam input
    if ($names->count() !== $names->unique()->count()) {
        return back()->with('error', 'Terdapat nama kategori yang duplikat dalam input.');
    }

    // 🔴 Cek duplikasi di database
    $existing = Kategori::whereIn(
        DB::raw('LOWER(nama_kategori)'),
        $names
    )->pluck('nama_kategori');

    if ($existing->count() > 0) {
        return back()->with(
            'error',
            'Kategori sudah ada: ' . $existing->implode(', ')
        );
    }

    DB::transaction(function () use ($names) {

        $lastKode = (int) Kategori::max('kode_kategori');
        $counter = $lastKode;

        foreach ($names as $nama) {
            $counter++;

            Kategori::create([
                'kode_kategori' => str_pad($counter, 2, '0', STR_PAD_LEFT),
                'nama_kategori' => ucfirst($nama),
            ]);
        }
    });

    return redirect()
        ->route('kategori.index')
        ->with('success', 'Kategori berhasil ditambahkan.');
}

public function update(Request $request, $id)
{
    $request->validate([
        'nama_kategori' => 'required|string|max:100',
    ]);

    $kategori = Kategori::findOrFail($id);

    // 🔴 Cek duplikasi (kecuali dirinya sendiri)
    $exists = Kategori::whereRaw(
        'LOWER(nama_kategori) = ?',
        [strtolower($request->nama_kategori)]
    )
    ->where('id_kategori', '!=', $id)
    ->exists();

    if ($exists) {
        return back()->with('error', 'Nama kategori sudah digunakan.');
    }

    $kategori->update([
        'nama_kategori' => $request->nama_kategori,
    ]);

    return redirect()
        ->route('kategori.index')
        ->with('success', 'Kategori berhasil diperbarui.');
}
    /**
     * Menghapus data kategori
     */
    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->delete();

        return redirect()
            ->route('kategori.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }

}
