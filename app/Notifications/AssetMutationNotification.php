<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage; // 🔑 WAJIB DIIMPORT

class AssetMutationNotification extends Notification
{
    use Queueable;

    protected $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * 1. DAFTARKAN CHANNEL DI SINI
     * Kita aktifkan 'database' (untuk navbar) dan 'mail' (untuk email) sekaligus!
     */
    public function via($notifiable)
    {
        return ['database', 'mail']; 
    }

    /**
     * 2. LOGIKA UNTUK NAVBAR (DATABASE)
     * Tetap aman, tidak perlu diubah.
     */
    public function toDatabase($notifiable)
    {
        return [
            'id_pindah' => $this->details['id_pindah'],
            'title'     => $this->details['title'],
            'message'   => $this->details['message'],
            'icon'      => $this->details['icon'] ?? 'fas fa-exchange-alt',
            'bg_color'  => $this->details['bg_color'] ?? 'bg-success',
            'url'       => $this->details['url'] ?? '#',
        ];
    }

    /**
     * 3. KODE BARU: LOGIKA UNTUK EMAIL
     * Otomatis dieksekusi Laravel untuk mengirim email resmi BAZNAS
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject($this->details['title']) // Subjek Email
                    ->greeting("Assalamu'alaikum, {$notifiable->name}") // Sapaan dinamis ke pimpinan/staf
                    ->line($this->details['message']) // Isi pesan
                    ->action('Lihat Pengajuan', $this->details['url'] ?? url('/')) // Tombol link di email
                    ->line('Terima kasih atas dedikasi Anda dalam pengelolaan aset BAZNAS.');
    }
}