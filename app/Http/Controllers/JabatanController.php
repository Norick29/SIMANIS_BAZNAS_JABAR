<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\jabatan;

class JabatanController extends Controller
{
    public function index()
    {
        $jabatan = jabatan::orderBy('created_at', 'desc')->get();

        return view('superadmin.master.jabatan.index', compact('jabatan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:100'
        ]);

        Jabatan::create([
            'nama_jabatan' => $request->nama_jabatan
        ]);

        return redirect()
            ->route('superadmin.master.jabatan.index')
            ->with('success', 'Jabatan berhasil ditambahkan');
    }
}
