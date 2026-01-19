<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\amil;
use App\Models\jabatan;
use App\Models\divisi;

class AmilController extends Controller
{
    public function index()
    {
        $amil = Amil::with([
            'jabatan',
            'divisi' // kalau departemen relasi dari divisi
        ])->get();
        $jabatan = Jabatan::all();
        $divisi = Divisi::all();

        return view('superadmin.master.amil.index', compact('amil', 'jabatan', 'divisi'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nia'        => 'required|string|max:50|unique:amils,nia',
            'nama'       => 'required|string|max:150',
            'id_jabatan' => 'required|exists:jabatans,id_jabatan',
            'id_divisi'  => 'required|exists:divisis,id_divisi',
        ]);

        Amil::create([
            'nia'        => $validated['nia'],
            'nama'       => $validated['nama'],
            'id_jabatan' => $validated['id_jabatan'],
            'id_divisi'  => $validated['id_divisi'],
            'nama_dept'  => $request->input('nama_dept', null),
        ]);

        return redirect()
            ->route('amil.index')
            ->with('success', 'Data amil berhasil ditambahkan');
    }

}
