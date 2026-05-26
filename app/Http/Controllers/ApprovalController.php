<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Approval;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\PindahAset;

class ApprovalController extends Controller
{
    /**
     * LIST APPROVAL
     */
    public function index()
    {
        $user = Auth::user();

        $query = Approval::with([
            'pindahAset.asset.barang',
            'pindahAset.asset.lokasi',
            'pindahAset.amilSebelum',
            'pindahAset.amilSesudah',
            'pindahAset.lokasiBaru',
            'requestedBy'
        ])
        ->where('status', 'menunggu');

        // FILTER BERDASARKAN ROLE
        if ($user->role === 'kadept') {

            $query->where('stage', 1);

        } elseif ($user->role === 'kadiv') {

            $query->where('stage', 2);

        }

        // SUPER ADMIN LIHAT SEMUA

        $approvals = $query
            ->latest()
            ->get();

        return view('superadmin.approval.index', compact('approvals'));
    }

    /**
     * DETAIL APPROVAL
     */
    public function show($id)
    {
        $approval = Approval::with([
            'pindahAset.asset.barang',
            'pindahAset.asset.lokasi',
            'pindahAset.amilSebelum',
            'pindahAset.amilSesudah',
            'pindahAset.lokasiBaru',
            'requestedBy'
        ])->findOrFail($id);

        $user = Auth::user();

        // VALIDASI HAK APPROVAL
        if (
            ($approval->stage == 1 && $user->role != 'kadept') &&
            ($approval->stage == 2 && $user->role != 'kadiv') &&
            ($user->role != 'superadmin')
        ) {
            return redirect()
                ->route('approvals.index')
                ->with('error', 'Anda tidak memiliki akses');
        }

        return view('superadmin.approval.show', compact('approval'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'password' => 'required'
        ]);

        if (!Hash::check($request->password, Auth::user()->password)) {

            return back()->with(
                'error',
                'Password salah'
            );
        }

        DB::beginTransaction();

        try {

            $approval = Approval::findOrFail($id);

            // UPDATE APPROVAL SEKARANG
            $approval->update([
                'status' => 'disetujui',
                'approved_by' => Auth::id(),
                'approval_date' => now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | STAGE 1 → BUAT STAGE 2
            |--------------------------------------------------------------------------
            */

            if ($approval->stage == 1) {

                Approval::create([
                    'id_pindah' => $approval->id_pindah,
                    'stage' => 2,
                    'status' => 'menunggu',
                    'approved_by' => null,
                    'approval_date' => null,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | STAGE 2 → FINAL APPROVAL
            |--------------------------------------------------------------------------
            */

            if ($approval->stage == 2) {

                $pindah = $approval->pindahAset;

                // UPDATE DATA ASET
                $pindah->asset->update([
                    'id_amil' => $pindah->id_amil_sesudah,
                    'id_lokasi' => $pindah->id_lokasi_baru,
                ]);

                // UPDATE STATUS PINDAH
                $pindah->update([
                    'status' => 'disetujui'
                ]);
            }

            DB::commit();

            return redirect()
                ->route('approvals.index')
                ->with(
                    'success',
                    'Approval berhasil diproses'
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with(
                'error',
                $e->getMessage()
            );
        }
    }

    public function riwayatPindah()
    {
        $riwayat = PindahAset::with([
            'asset.barang',
            'asset.lokasi',
            'amilSebelum',
            'amilSesudah',
            'lokasiBaru',
            'requestedBy'
        ])
        ->latest()
        ->paginate(10);

        return view(
            'superadmin.aset.riwayat_pindah.index',
            compact('riwayat')
        );
    }

    public function showBeritaAcara($id)
    {
        $pindah = PindahAset::with([
            'asset.barang',
            'asset.lokasi',
            'amilSebelum',
            'amilSesudah',
            'lokasiBaru',
            'approvals.approvedBy',
            'requestedBy'
        ])->findOrFail($id);

        return view(
            'superadmin.riwayat_pindah.berita-acara',
            compact('pindah')
        );
    }
}