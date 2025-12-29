<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TargetTersediaNotification extends Notification
{
    use Queueable;

    public $namaProdi;
    public $tahun;
    public $mti_id;
    public $tipePesan; 

    public function __construct($namaProdi, $tahun, $mti_id, $tipePesan = 'baru')
    {
        $this->namaProdi = $namaProdi;
        $this->tahun     = $tahun;
        $this->mti_id    = $mti_id;
        $this->tipePesan = $tipePesan;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        if ($this->tipePesan == 'update') {
            $judul = 'Perubahan Target Indikator';
            $pesan = "Prodi <strong>{$this->namaProdi}</strong> telah melakukan <strong>perubahan/update</strong> pada Target Indikator.";
            $icon  = 'fas fa-edit';
            $color = 'bg-warning';
        } else {
            $judul = 'Target Baru Tersedia';
            $pesan = "Prodi <strong>{$this->namaProdi}</strong> telah selesai menginputkan Target Indikator baru.";
            $icon  = 'fas fa-bullseye';
            $color = 'bg-primary';
        }

        return [
            'title'   => $judul,
            'message' => $pesan,
            'icon'    => $icon,
            'color'   => $color,
            'url'     => route('monitoringiku.index-detail', ['mti_id' => $this->mti_id]),
            'created_at' => now(),
        ];
    }
}