<!DOCTYPE html>
<html>
<head>
    <title>{{ $judul }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        h3, h4 { text-align: center; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 5px; vertical-align: top; word-wrap: break-word; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .meta-info { margin-bottom: 20px; }
        .badge { display: inline-block; padding: 2px 5px; font-size: 0.8em; border-radius: 3px; color: #fff; background-color: #6c757d; }
    </style>
</head>
<body>

    <h3>{{ $judul }}</h3>
        
        <h4>
            {{ strtoupper(optional($monitoring->prodi->Fakultasn)->nama_fakultas ?? 'FAKULTAS TIDAK DITEMUKAN') }}
        </h4>

        <h4>PROGRAM STUDI {{ strtoupper($monitoring->prodi->nama_prodi ?? '-') }}</h4>
        
        <h4>TAHUN {{ optional($monitoring->tahunKerja)->th_tahun ?? '-' }}</h4>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="25%">Indikator Kinerja</th>
                <th width="8%">Baseline</th>
                <th width="8%">Target</th>
                
                {{-- Logika Header Sesuai Type --}}
                @if(in_array($type, ['pelaksanaan', 'evaluasi', 'pengendalian', 'peningkatan']))
                    <th width="8%">Capaian</th>
                    <th width="10%">URL</th>
                @endif

                @if(in_array($type, ['evaluasi', 'pengendalian', 'peningkatan']))
                    <th width="8%">Status</th>
                @endif

                @if(in_array($type, ['pengendalian', 'peningkatan']))
                    <th width="10%">Keterangan</th>
                    <th width="10%">Evaluasi</th>
                    <th width="10%">Tindak Lanjut</th>
                @endif

                @if($type == 'peningkatan')
                    <th width="10%">Peningkatan</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                @php
                    // --- LOGIKA INDIKATOR ---
                    $ik_kode = optional($item->indikatorKinerja)->ik_kode ?? '';
                    $ik_nama = optional($item->indikatorKinerja)->ik_nama ?? '';
                    $indikator = trim($ik_kode ? ($ik_kode . ' - ' . $ik_nama) : $ik_nama);
                    $ketercapaian = strtolower(optional($item->indikatorKinerja)->ik_ketercapaian ?? '');

                    // --- LOGIKA BASELINE (PERBAIKAN) ---
                    // Kita ambil dari atribut 'fetched_baseline' yang kita buat di controller
                    $baselineRaw = trim((string) ($item->fetched_baseline ?? '0')); 
                    
                    $cleanNumBase = str_replace(['%', ' '], '', $baselineRaw);
                    
                    // Format Tampilan Baseline
                    $baselineDisplay = $baselineRaw;
                    
                    if ($ketercapaian === 'persentase' && is_numeric($cleanNumBase)) {
                        // Cek jika baselineRaw sudah mengandung %, jika tidak tambahkan
                        if (strpos($baselineRaw, '%') === false) {
                            $baselineDisplay = $cleanNumBase . '%';
                        }
                    } elseif ($ketercapaian === 'rasio') {
                        // Format Rasio x:y
                        $cleaned = preg_replace('/\s*/', '', $baselineRaw);
                        if (preg_match('/^\d+:\d+$/', $cleaned)) {
                            [$a, $b] = explode(':', $cleaned);
                            $baselineDisplay = "{$a} : {$b}";
                        }
                    } elseif (in_array(strtolower($baselineRaw), ['ada', 'draft'])) {
                        $baselineDisplay = ucfirst($baselineRaw); 
                    }
                    // --- LOGIKA TARGET ---
                    $targetRaw = trim($item->ti_target);
                    $cleanNumTarget = str_replace(['%', ' '], '', $targetRaw);
                    $targetDisplay = $targetRaw;
                    
                    if ($ketercapaian === 'persentase' && is_numeric($cleanNumTarget)) {
                        $targetDisplay = $cleanNumTarget . '%';
                    }

                    // --- LOGIKA CAPAIAN (PORTING DARI WEB) ---
                    $detail = $item->monitoringDetail;
                    $capaianRaw = optional($detail)->mtid_capaian ?? '';
                    $cleanNumCapaian = str_replace(['%', ' '], '', $capaianRaw);
                    
                    $capaianDisplay = '-';
                    if ($capaianRaw !== '') {
                        if (strpos($capaianRaw, '%') !== false || $ketercapaian === 'persentase') {
                            $capaianDisplay = ((float)$cleanNumCapaian) . '%';
                        } elseif (is_numeric($capaianRaw) && $ketercapaian == 'nilai') {
                            $capaianDisplay = $capaianRaw;
                        } elseif (preg_match('/^\d+\s*:\s*\d+$/', $capaianRaw)) {
                            $cleanedRasio = preg_replace('/\s*/', '', $capaianRaw);
                            [$left, $right] = explode(':', $cleanedRasio);
                            $capaianDisplay = $left . ' : ' . $right;
                        } elseif (in_array(strtolower($capaianRaw), ['ada', 'draft'])) {
                            $capaianDisplay = ucfirst($capaianRaw);
                        } else {
                            $capaianDisplay = $capaianRaw;
                        }
                    }
                @endphp

                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $indikator }}</td>
                    
                    {{-- Baseline --}}
                    <td class="text-center">{{ $baselineDisplay }}</td>
                    
                    {{-- Target --}}
                    <td class="text-center">{{ $targetDisplay }}</td>

                    {{-- Data Pelaksanaan --}}
                    @if(in_array($type, ['pelaksanaan', 'evaluasi', 'pengendalian', 'peningkatan']))
                        <td class="text-center">{{ $capaianDisplay }}</td>
                        <td class="text-center" style="font-size: 9pt;">
                            @if(!empty($detail->mtid_url))
                                <a href="{{ $detail->mtid_url }}" target="_blank">Link</a>
                            @else
                                -
                            @endif
                        </td>
                    @endif

                    {{-- Data Evaluasi --}}
                    @if(in_array($type, ['evaluasi', 'pengendalian', 'peningkatan']))
                        <td class="text-center">
                            {{ ucfirst($detail->mtid_status ?? '-') }}
                        </td>
                    @endif

                    {{-- Data Pengendalian --}}
                    @if(in_array($type, ['pengendalian', 'peningkatan']))
                        <td class="text-wrap">{{ $detail->mtid_keterangan ?? '-' }}</td>
                        <td class="text-wrap">{{ $detail->mtid_evaluasi ?? '-' }}</td>
                        <td class="text-wrap">{{ $detail->mtid_tindaklanjut ?? '-' }}</td>
                    @endif

                    {{-- Data Peningkatan --}}
                    @if($type == 'peningkatan')
                        <td class="text-wrap">{{ $detail->mtid_peningkatan ?? '-' }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center">Tidak ada data target indikator.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>