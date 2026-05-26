<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\RSAService;

class UserController extends Controller
{

    protected RSAService $rsaService;

    public function __construct(RSAService $rsaService)
    {
        $this->rsaService = $rsaService;
    }

    public function index()
    {
        $users = User::orderby('id_user')->get();
        return view('superadmin.user.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'no_hp'    => 'required|string|max:15',
            'role'     => 'required|in:super admin,kadiv,kadept,staf',
        ]);

        DB::beginTransaction();

        try {

            // Buat user
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => bcrypt($validated['password']),
                'no_hp'    => $validated['no_hp'],
                'role'     => $validated['role'],
            ]);

            // Generate RSA key hanya untuk kadiv dan kadept
            if (in_array($validated['role'], ['kadiv', 'kadept'])) {

                $keyPair = $this->rsaService->generateKeyPair();

                $user->update([
                    'public_key'  => $keyPair['public_key'],
                    'private_key' => $keyPair['private_key'],
                ]);
            }

            DB::commit();

            $role = $validated['role'];

            $message = "User {$role} berhasil dibuat";

            if (in_array($validated['role'], ['kadiv', 'kadept'])) {
                $message .= " dengan RSA digital signature";
            }

            return redirect()
                ->back()
                ->with('success', $message . '!');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with(
                'error',
                'Gagal membuat user: ' . $e->getMessage()
            );
        }
    }
    
    public function update(Request $request, $id_user)
    {
        $user = User::findOrFail($id_user);

        // Cek apakah user mencoba mengubah rolenya sendiri
        if ($id_user == Auth::id() && $request->role !== $user->role) {
            return redirect()->back()->with('error', 'Anda tidak dapat mengubah role Anda sendiri.');
        }

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id_user . ',id_user',
            'no_hp' => 'required|string|max:15',
            'role'  => 'required|in:super admin,kadiv,kadept,staf',
        ]);

        DB::beginTransaction();

        try {
            $oldRole = $user->role;
            $newRole = $validated['role'];

            // Update data dasar
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->no_hp = $validated['no_hp'];
            $user->role = $newRole;

            // Jika password diisi
            if ($request->filled('password')) {
                $user->password = bcrypt($request->password);
            }

            // Jika role berubah dari non-approval ke approval role
            if (!in_array($oldRole, ['kadiv', 'kadept']) && 
                in_array($newRole, ['kadiv', 'kadept']) && 
                is_null($user->public_key)) {
                
                // Generate RSA key untuk approval authority
                $keyGenerated = $user->generateRSAKeyPair();

                if (!$keyGenerated) {
                    throw new \Exception('Gagal generate RSA key pair untuk role baru.');
                }
            }

            // Jika role berubah dari approval ke non-approval role
            if (in_array($oldRole, ['kadiv', 'kadept']) && 
                !in_array($newRole, ['kadiv', 'kadept'])) {
                
                // Hapus RSA keys
                $user->public_key = null;
                $user->private_key = null;
            }

            $user->save();
            DB::commit();

            return redirect()->back()->with('success', 'User berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal update user: ' . $e->getMessage());
        }
    }

    public function destroy($id_user)
    {
        // Cek apakah user mencoba menghapus dirinya sendiri
        if ($id_user == Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }
        
        DB::beginTransaction();

        try {
            $user = User::findOrFail($id_user);
            
            // Soft delete
            $user->delete();

            DB::commit();

            return redirect()->back()->with('success', 'User berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }

    /**
     * Download private key untuk user (hanya untuk kadiv & kadept)
     * Route: GET /users/{id}/download-private-key
     */
    public function downloadPrivateKey($id_user)
    {
        $user = User::findOrFail($id_user);

        // Cek authorization
        if (Auth::id() != $id_user && Auth::user()->role !== 'super admin') {
            return redirect()->back()->with('error', 'Anda tidak berhak mengakses private key user lain.');
        }

        // Cek apakah user memiliki private key
        if (!$user->private_key) {
            return redirect()->back()->with('error', 'Private key tidak tersedia untuk user ini.');
        }

        // Dekripsi private key
        $privateKey = decrypt($user->private_key);

        // Download file
        return response($privateKey)
            ->header('Content-Type', 'application/octet-stream')
            ->header('Content-Disposition', 'attachment; filename="' . $user->email . '_private_key.pem"');
    }
}
