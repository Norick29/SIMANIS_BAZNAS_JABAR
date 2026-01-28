<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\amil;
use App\Models\jabatan;
use App\Models\divisi;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

class AmilController extends Controller
{
    public function index()
    {
        $amil = Amil::with([
            'jabatan',
            'divisi' // kalau departemen relasi dari divisi
        ])->paginate(10);
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

    public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xls,xlsx'
    ]);

    $file = $request->file('file');
    $spreadsheet = IOFactory::load($file->getPathname());
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    DB::beginTransaction();

    try {
        foreach ($rows as $index => $row) {

            // skip header
            if ($index === 0) continue;

            [$nia, $nama, $namaJabatan, $namaDivisi, $namaDept] = $row;

            if (!$nia || !$nama) continue;

            $jabatan = jabatan::where('nama_jabatan', $namaJabatan)->first();
            $divisi  = divisi::where('nama_divisi', $namaDivisi)->first();

            // skip jika relasi tidak ditemukan
            if (!$jabatan || !$divisi) {
                continue;
            }

            amil::create([
                'nia'        => $nia,
                'nama'       => $nama,
                'id_jabatan' => $jabatan->id_jabatan,
                'id_divisi'  => $divisi->id_divisi,
                'nama_dept'  => $namaDept,
            ]);
        }

        DB::commit();

        return back()->with('success', 'Import data amil berhasil');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Import gagal: ' . $e->getMessage());
    }
}

public function update(Request $request, $id)
    {
        $request->validate([
            'nia'         => 'required|string|max:50',
            'nama'        => 'required|string|max:100',
            'id_jabatan'  => 'required|exists:jabatans,id_jabatan',
            'id_divisi'   => 'required|exists:divisis,id_divisi',
            'nama_dept'   => 'nullable|string|max:100',
        ]);

        $amil = Amil::findOrFail($id);

        // Optional: cegah NIA dobel (kecuali dirinya sendiri)
        $cekNia = Amil::where('nia', $request->nia)
            ->where('id_amil', '!=', $id)
            ->exists();

        if ($cekNia) {
            return back()->withErrors([
                'nia' => 'NIA sudah digunakan oleh amil lain.'
            ])->withInput();
        }

        $amil->update([
            'nia'        => $request->nia,
            'nama'       => $request->nama,
            'id_jabatan' => $request->id_jabatan,
            'id_divisi'  => $request->id_divisi,
            'nama_dept'  => $request->nama_dept,
        ]);

        return redirect()
            ->route('amil.index')
            ->with('success', 'Data amil berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $amil = Amil::findOrFail($id);
        $amil->delete();

        return back()->with('success', 'Data amil berhasil dihapus.');
    }


}
