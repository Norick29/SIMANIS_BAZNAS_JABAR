<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterPenyusutan;
use App\Models\Jenis;

class MasterPenyusutanController extends Controller
{
    public function index()
{
    $masterPenyusutan = MasterPenyusutan::with('jenis')
                        ->paginate(10);

    $jenis = Jenis::whereDoesntHave('masterPenyusutan')
    ->orderBy('nama_jenis')
    ->get();

    return view(
        'superadmin.master.penyusutan.index',
        compact(
            'masterPenyusutan',
            'jenis'
        )
    );
}

/**
     * Menyimpan kebijakan penyusutan baru
     */
    public function store(Request $request)
{
    $request->validate([

        'items.*.id_jenis' => 'required|exists:jenis,id_jenis',

        'items.*.umur_ekonomis' => 'required|integer|min:1',

        'items.*.persentase' => 'required|numeric|min:0|max:100',

        'items.*.status' => 'required|in:aktif,nonaktif',

    ]);

    foreach($request->items as $item){

        if(
            MasterPenyusutan::where('id_jenis',$item['id_jenis'])->exists()
        ){
            continue;
        }

        MasterPenyusutan::create([

            'id_jenis' => $item['id_jenis'],

            'umur_ekonomis' => $item['umur_ekonomis'],

            'persentase' => $item['persentase'],

            'status' => $item['status'],

        ]);

    }

    return redirect()
        ->route('master-penyusutan.index')
        ->with('success','Data master penyusutan berhasil ditambahkan.');

}

    /**
     * Mengubah kebijakan penyusutan
     */
    public function update(Request $request, $id)
    {
        $master = MasterPenyusutan::findOrFail($id);

        $request->validate([
            'id_jenis'      => 'required|exists:jenis,id_jenis|unique:master_penyusutan,id_jenis,' . $master->id_master_penyusutan . ',id_master_penyusutan',
            'umur_ekonomis' => 'required|integer|min:1',
            'persentase'    => 'required|numeric|min:0|max:100',
            'status'        => 'required|in:aktif,nonaktif',
        ]);

        $master->update([
            'id_jenis'      => $request->id_jenis,
            'umur_ekonomis' => $request->umur_ekonomis,
            'persentase'    => $request->persentase,
            'status'        => $request->status,
        ]);

        return redirect()
            ->route('master-penyusutan.index')
            ->with('success','Kebijakan penyusutan berhasil diperbarui.');
    }

    /**
     * Menghapus kebijakan penyusutan
     */
    public function destroy($id)
    {
        $master = MasterPenyusutan::findOrFail($id);

        $master->delete();

        return redirect()
            ->route('master-penyusutan.index')
            ->with('success','Kebijakan penyusutan berhasil dihapus.');
    }
}
