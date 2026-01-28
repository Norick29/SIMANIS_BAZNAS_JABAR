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
            ->route('jabatan.index')
            ->with('success', 'Jabatan berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:100',
        ]);

        $jabatan = Jabatan::findOrFail($id);
        $jabatan->update($request->only('nama_jabatan'));

        return back()->with('success', 'Data jabatan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jabatan = Jabatan::findOrFail($id);

        // OPSIONAL: cegah hapus jika masih dipakai
        if ($jabatan->amil()->count() > 0) {
            return back()->with('error', 'Jabatan tidak dapat dihapus karena masih digunakan oleh amil.');

        }

        $jabatan->delete();

        return back()->with('success', 'Data jabatan berhasil dihapus.');
    }

}
