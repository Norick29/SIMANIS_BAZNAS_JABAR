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

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_divisi'    => 'required|string|max:100',
        ]);

        $divisi = Divisi::findOrFail($id);
        $divisi->update($request->only(
            'nama_divisi'
        ));

        return back()->with('success', 'Data divisi berhasil diperbarui.');
    }

   public function destroy($id)
    {
        $divisi = Divisi::findOrFail($id);

        // Cegah hapus jika masih digunakan oleh amil
        if ($divisi->amil()->count() > 0) {
            return back()->with(
                'error',
                'Divisi tidak dapat dihapus karena masih digunakan oleh amil.'
            );
        }

        $divisi->delete();

        return back()->with('success', 'Data divisi berhasil dihapus.');
    }

}
