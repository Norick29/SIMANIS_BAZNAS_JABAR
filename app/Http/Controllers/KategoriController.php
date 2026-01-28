<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = Kategori::orderBy('created_at', 'desc')->get();

        return view('superadmin.master.kategori.index', compact('kategori'));
    }

    /**
     * Menyimpan data kategori baru
     */
    public function store(Request $request)
    {
        // VALIDASI
        $request->validate([
            'kode_kategori' => 'required|string|max:50|unique:kategoris,kode_kategori',
            'nama_kategori' => 'required|string|max:100',
        ]);

        // SIMPAN DATA
        Kategori::create([
            'kode_kategori' => $request->kode_kategori,
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()
            ->route('kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }
}
