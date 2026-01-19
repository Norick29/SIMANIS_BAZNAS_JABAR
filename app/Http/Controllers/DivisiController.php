<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Divisi;

class DivisiController extends Controller
{
    public function index()
    {
        $divisi = Divisi::orderBy('created_at', 'desc')->get();

        return view('superadmin.master.divisi.index', compact('divisi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_divisi' => 'required|string|max:100'
        ]);

        Divisi::create([
            'nama_divisi' => $request->nama_divisi
        ]);

        return redirect()
            ->route('divisi.index')
            ->with('success', 'Divisi berhasil ditambahkan');
    }

}
