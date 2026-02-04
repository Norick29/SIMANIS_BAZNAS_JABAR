<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\jabatan;
use Illuminate\Support\Facades\DB;

class JabatanController extends Controller
{
    public function index(Request $request)
    {
        $query = Jabatan::query();

        if ($request->filled('q')) {
            $query->where('kode_jabatan', 'like', '%' . $request->q . '%')
                  ->orWhere('nama_jabatan', 'like', '%' . $request->q . '%');
        }

        $jabatan = $query->orderBy('created_at', 'desc')->get();

        return view('superadmin.master.jabatan.index', compact('jabatan'));
    }

    public function store(Request $request)
{
    $request->validate([
        'nama_jabatan'   => 'required|array|min:1',
        'nama_jabatan.*' => 'required|string|max:100',
    ]);

    $inserted  = 0;
    $duplicates = [];

    DB::beginTransaction();

    try {
        foreach ($request->nama_jabatan as $nama) {

            $nama = trim($nama);

            if (
                Jabatan::whereRaw(
                    'LOWER(nama_jabatan) = ?',
                    [strtolower($nama)]
                )->exists()
            ) {
                $duplicates[] = $nama;
                continue;
            }

            Jabatan::create([
                'nama_jabatan' => $nama
            ]);

            $inserted++;
        }

        DB::commit();

        if ($inserted === 0) {
            return back()->with('error', 'Semua data jabatan sudah ada.');
        }

        return back()->with([
            'success' => "$inserted jabatan berhasil ditambahkan.",
            'duplicates' => $duplicates
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal menyimpan data.');
    }
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
