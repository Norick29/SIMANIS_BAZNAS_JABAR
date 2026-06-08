<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Approval;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\PindahAset;
use App\Models\Signature;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
                'approved_at' => now(),
            ]);

            $pindah = $approval->pindahAset;

            $digitalSignature = $this->generateDigitalSignature(
                $pindah
            );

            Signature::create([
                'id_aset' => $pindah->id_aset,
                'id_pindah' => $pindah->id_pindah,
                'user_id' => Auth::id(),

                'signed_data' =>
                    $digitalSignature['signed_data'],

                'signature' =>
                    $digitalSignature['signature'],

                'public_key_fingerprint' =>
                    $digitalSignature['fingerprint'],

                'algorithm' => 'RSA-SHA256',

                'signed_at' => now(),
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
                    'approved_at' => null,
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
            'superadmin.aset.riwayat_pindah.berita-acara',
            compact('pindah')
        );
    }

    public function downloadBeritaAcaraPdf($id)
    {
        $pindah = PindahAset::with([
            'asset.barang',
            'asset.lokasi',
            'amilSebelum',
            'amilSesudah',
            'lokasiBaru',
            'approvals.approvedBy'
        ])->findOrFail($id);

        // GET SIGNATURES
        $approvals = $pindah->approvals()
            ->with('approvedBy')
            ->where('status', 'disetujui')
            ->get();

        $kadeptSignature = null;
        $kadivSignature = null;

        // Cari Kadept approval
        foreach ($approvals as $approval) {
            if ($approval->stage == 1 && $approval->approvedBy) {
                $kadeptSignature = $approval;
                break;
            }
        }

        // Cari Kadiv approval
        foreach ($approvals as $approval) {
            if ($approval->stage == 2 && $approval->approvedBy) {
                $kadivSignature = $approval;
                break;
            }
        }

        // GENERATE QR CODE UNTUK KADEPT
        $qrKadept = null;
        if ($kadeptSignature) {
            $qrData = json_encode([
                'approval_id' => $kadeptSignature->id_approval,
                'stage' => 'Kepala Departemen',
                'approved_by' => $kadeptSignature->approvedBy->name,
                'approved_at' => optional(
                    $kadeptSignature->approved_at
                )->format('Y-m-d H:i:s'),
                'kode_aset' => $pindah->asset->kode_aset,
            ]);
            
            $qrKadept = base64_encode(
                QrCode::format('png')
                    ->size(150)
                    ->generate($qrData)
            );
        }

        // GENERATE QR CODE UNTUK KADIV
        $qrKadiv = null;
        if ($kadivSignature) {
            $qrData = json_encode([
                'approval_id' => $kadivSignature->id_approval,
                'stage' => 'Kepala Divisi',
                'approved_by' => $kadivSignature->approvedBy->name,
                'approved_at' => optional(
                    $kadivSignature->approved_at
                )->format('Y-m-d H:i:s'),
                'kode_aset' => $pindah->asset->kode_aset,
            ]);
            
            $qrKadiv = base64_encode(
                QrCode::format('png')
                    ->size(150)
                    ->generate($qrData)
            );
        }

        // CONVERT LOGO IMAGE KE BASE64 (DOMPDF tidak bisa akses asset)
        $logoPath = public_path('image/logo-baznas.png');
        $logoBase64 = null;
        
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView(
            'superadmin.aset.riwayat_pindah.berita-acara-pdf',
            [
                'pindah' => $pindah,
                'qrKadept' => $qrKadept,
                'qrKadiv' => $qrKadiv,
                'kadeptSignature' => $kadeptSignature,
                'kadivSignature' => $kadivSignature,
                'logoBase64' => $logoBase64,
            ]
        );

        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('isPhpEnabled', true);

        return $pdf->download(
            'Berita_Acara_' . 
            $pindah->asset->kode_aset . 
            '_' . 
            now()->format('d-m-Y') . 
            '.pdf'
        );
    }

    private function generateDigitalSignature($pindah)
{
    $user = Auth::user();

    $data = json_encode([
        'id_pindah' => $pindah->id_pindah,
        'kode_aset' => $pindah->asset->kode_aset,
        'lokasi_lama' => $pindah->asset->lokasi->nama_lokasi,
        'lokasi_baru' => $pindah->lokasiBaru->nama_lokasi,
        'approved_by' => $user->name,
        'approved_at' => now()->toDateTimeString(),
    ]);

    $privateKey = openssl_pkey_get_private(
        $user->private_key
    );

    if (!$privateKey) {
        throw new \Exception(
            'Private key tidak valid'
        );
    }

    openssl_sign(
        $data,
        $rawSignature,
        $privateKey,
        OPENSSL_ALGO_SHA256
    );

    return [
        'signed_data' => $data,
        'signature' => base64_encode(
            $rawSignature
        ),
        'fingerprint' => sha1(
            $user->public_key
        )
    ];
}
}