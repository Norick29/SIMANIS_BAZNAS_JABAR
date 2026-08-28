<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Amil;
use App\Models\Jabatan;
use App\Models\Divisi;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

class AmilController extends Controller
{
    public function index(Request $request)
    {
        $query = Amil::with([
            'jabatan',
            'divisi' // kalau departemen relasi dari divisi
        ]);

        if ($request->filled('q')) {
            $query->where('nia', 'like', '%' . $request->q . '%')
                  ->orWhere('nama', 'like', '%' . $request->q . '%')
                  ->orWhereHas('jabatan', function ($q) use ($request) {
                      $q->where('nama_jabatan', 'like', '%' . $request->q . '%');
                  })
                  ->orWhereHas('divisi', function ($q) use ($request) {
                      $q->where('nama_divisi', 'like', '%' . $request->q . '%');
                  });
        }

        $amil = $query->paginate(10);
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
    if (Jabatan::count() === 0 || Divisi::count() === 0) {
    return back()->with(
        'error',
        'Import gagal. Data master Jabatan atau Divisi masih kosong.'
    );
}

    $request->validate([
        'file' => 'required|mimes:xls,xlsx'
    ]);

    $rows = IOFactory::load(
        $request->file('file')->getPathname()
    )->getActiveSheet()->toArray();

    DB::beginTransaction();

    $duplicates = [];
    $skipped    = [];
    $inserted   = 0;

    try {
        foreach ($rows as $index => $row) {

            if ($index === 0) continue;

            [$nia, $nama, $namaJabatan, $namaDivisi, $namaDept] = array_map('trim', $row);

            if (!$nia || !$nama) {
                $skipped[] = "Baris ".($index+1)." : NIA / Nama kosong";
                continue;
            }

            // cek duplikasi NIA
            if (Amil::where('nia', $nia)->exists()) {
                $duplicates[] = "NIA $nia (baris ".($index+1).")";
                continue;
            }

            $jabatan = Jabatan::whereRaw('LOWER(nama_jabatan) = ?', [strtolower($namaJabatan)])->first();
            $divisi  = Divisi::whereRaw('LOWER(nama_divisi) = ?', [strtolower($namaDivisi)])->first();

            if (!$jabatan || !$divisi) {
                $skipped[] = "Baris ".($index+1)." : Jabatan / Divisi tidak ditemukan";
                continue;
            }

            Amil::create([
                'nia'        => $nia,
                'nama'       => $nama,
                'id_jabatan' => $jabatan->id_jabatan,
                'id_divisi'  => $divisi->id_divisi,
                'nama_dept'  => $namaDept ?: null,
            ]);

            $inserted++;
        }

        DB::commit();

        if ($duplicates || $skipped) {
            return back()->with([
                'warning'    => "Import selesai. $inserted data berhasil disimpan.",
                'duplicates' => array_merge($duplicates, $skipped),
            ]);
        }

        return back()->with('success', "Import berhasil ($inserted data)");

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
