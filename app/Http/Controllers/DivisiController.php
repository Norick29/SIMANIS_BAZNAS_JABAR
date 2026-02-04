<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Divisi;
use Illuminate\Support\Facades\DB;

class DivisiController extends Controller
{
    public function index(Request $request)
    {
        $query = Divisi::query();

        if ($request->filled('q')) {
            $query->where('kode_divisi', 'like', '%' . $request->q . '%')
                  ->orWhere('nama_divisi', 'like', '%' . $request->q . '%');
        }

        $divisi = $query->orderBy('created_at', 'desc')->get();

        return view('superadmin.master.divisi.index', compact('divisi'));
    }

    public function store(Request $request)
{
    $request->validate([
        'nama_divisi'   => 'required|array|min:1',
        'nama_divisi.*' => 'required|string|max:100',
    ]);

    $inserted   = 0;
    $duplicates = [];

    DB::beginTransaction();

    try {
        foreach ($request->nama_divisi as $nama) {

            $nama = trim($nama);

            if (
                Divisi::whereRaw(
                    'LOWER(nama_divisi) = ?',
                    [strtolower($nama)]
                )->exists()
            ) {
                $duplicates[] = $nama;
                continue;
            }

            Divisi::create([
                'nama_divisi' => $nama
            ]);

            $inserted++;
        }

        DB::commit();

        if ($inserted === 0) {
            return back()->with('error', 'Semua data divisi sudah terdaftar.');
        }

        return back()->with([
            'success'    => "$inserted divisi berhasil ditambahkan.",
            'duplicates' => $duplicates
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal menyimpan data divisi.');
    }
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
