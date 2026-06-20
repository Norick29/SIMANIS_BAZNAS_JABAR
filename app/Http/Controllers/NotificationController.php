<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function read($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return redirect('/login');
        }

        // Sekarang VS Code dijamin paham dan garis merah pada 'notifications()' pasti hilang!
        $notification = $user->notifications()->findOrFail($id);
        
        $notification->markAsRead();

        $destinationUrl = $notification->data['url'] ?? url('/dashboard');

        return redirect($destinationUrl);
    }
}