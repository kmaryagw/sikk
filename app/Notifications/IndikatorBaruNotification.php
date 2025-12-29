<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class IndikatorBaruNotification extends Notification
{
    use Queueable;

    public $tipePesan;
    public $namaIndikator;

    /**
     * @param string 
     * @param string 
     */
    public function __construct($tipePesan = 'baru', $namaIndikator = '')
    {
        $this->tipePesan = $tipePesan;
        $this->namaIndikator = $namaIndikator;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        if ($this->tipePesan == 'update') {
            $judul = 'Perubahan Master Indikator';
            $pesan = "Admin telah <strong>memperbarui</strong> indikator: <em>{$this->namaIndikator}</em>. Silakan cek kembali target Prodi Anda.";
            $icon  = 'fas fa-sync-alt';
            $color = 'bg-info'; 
        } else {
            $judul = 'Indikator Kinerja Baru';
            $pesan = "Admin telah menambahkan <strong>Indikator Kinerja Baru</strong>. Segera tetapkan target untuk Prodi Anda.";
            $icon  = 'fas fa-clipboard-list'; 
            $color = 'bg-success'; 
        }

        // URL diarahkan ke halaman Prodi mengisi Target
        // Ganti 'targetcapaianprodi.index' sesuai nama route Anda
        $urlTujuan = route('targetcapaianprodi.index'); 

        return [
            'title'      => $judul,
            'message'    => $pesan,
            'icon'       => $icon,
            'color'      => $color,
            'url'        => $urlTujuan,
            'created_at' => now(),
        ];
    }
}