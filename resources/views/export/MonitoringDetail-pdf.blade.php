<!DOCTYPE html>
<html>
<head>
    <title>{{ $judul }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        h3, h4 { text-align: center; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 5px; vertical-align: top; word-wrap: break-word; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .text-wrap { white-space: normal; }
        .status-text { margin-top: 5px; font-weight: bold; font-style: italic; font-size: 9pt; color: #333; display: block; border-top: 0.5px solid #ccc; padding-top: 2px; }
    </style>
</head>
<body>

    <h3>{{ $judul }}</h3>
        
    <h4>
        FAKULTAS {{ strtoupper(optional($monitoring->prodi->Fakultasn)->nama_fakultas ?? 'FAKULTAS TIDAK DITEMUKAN') }}
    </h4>

    <h4>PROGRAM STUDI {{ strtoupper($monitoring->prodi->nama_prodi ?? '-') }}</h4>
    
    <h4>TAHUN {{ optional($monitoring->tahunKerja)->th_tahun ?? '-' }}</h4>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                
                <th width="15%">Standar</th>

                <th width="15%">Indikator Kinerja</th>

                @if($type == 'penetapan')
                    <th width="8%">Baseline</th>
                    <th width="8%">Target</th>
                @endif

                @if(in_array($type, ['pelaksanaan', 'evaluasi', 'pengendalian', 'peningkatan']))
                    <th width="15%">Keterlaksanaan</th>
                @endif

                @if(in_array($type, ['evaluasi', 'pengendalian', 'peningkatan']))
                    <th width="15%">Evaluasi</th>
                @endif

                @if(in_array($type, ['pengendalian', 'peningkatan']))
                    <th width="15%">Tindak Lanjut</th>
                @endif

                @if($type == 'peningkatan')
                    <th width="15%">Peningkatan</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($data->sortBy('indikatorKinerja.ik_kode', SORT_NATURAL) as $index => $item)
                @php
                    $ik_kode = optional($item->indikatorKinerja)->ik_kode ?? '';
                    $ik_nama = optional($item->indikatorKinerja)->ik_nama ?? '';
                    $indikator = trim($ik_kode ? ($ik_kode . ' - ' . $ik_nama) : $ik_nama);
                    $ketercapaian = strtolower(optional($item->indikatorKinerja)->ik_ketercapaian ?? '');
                    $standarDeskripsi = optional($item->indikatorKinerja->standar)->std_deskripsi ?? '-';
                    // --- LOGIKA PEMFORMATAN BASELINE ---
                    $baselineRaw = trim((string) ($item->fetched_baseline ?? '0')); 
                    $cleanNumBase = str_replace(['%', ' '], '', $baselineRaw);
                    $baselineDisplay = $baselineRaw;
                    if ($ketercapaian === 'persentase' && is_numeric($cleanNumBase)) {
                        $baselineDisplay = (strpos($baselineRaw, '%') === false) ? $cleanNumBase . '%' : $baselineRaw;
                    } elseif ($ketercapaian === 'rasio') {
                        $cleaned = preg_replace('/\s*/', '', $baselineRaw);
                        if (preg_match('/^\d+:\d+$/', $cleaned)) {
                            [$a, $b] = explode(':', $cleaned);
                            $baselineDisplay = "{$a} : {$b}";
                        }
                    }
                    // --- LOGIKA PEMFORMATAN TARGET ---
                    $targetRaw = trim($item->ti_target);
                    $cleanNumTarget = str_replace(['%', ' '], '', $targetRaw);
                    $targetDisplay = $targetRaw;
                    if ($ketercapaian === 'persentase' && is_numeric($cleanNumTarget)) {
                        $targetDisplay = $cleanNumTarget . '%';
                    }

                    $detail = $item->monitoringDetail;
                @endphp

                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-wrap">{{ $standarDeskripsi }}</td>
                    <td class="text-wrap">{{ $indikator }}</td>
                    
                    @if($type == 'penetapan')
                        <td class="text-center">{{ $baselineDisplay }}</td>
                        <td class="text-center">{{ $targetDisplay }}</td>
                    @endif

                    {{-- Data Pelaksanaan (Deskripsi + Status) --}}
                    @if(in_array($type, ['pelaksanaan', 'evaluasi', 'pengendalian', 'peningkatan']))
                        <td class="text-wrap">
                            {{ $detail->mtid_keterangan ?? '-' }}
                            <span class="status-text">Status: {{ ucfirst($detail->mtid_status ?? 'Draft') }}</span>
                        </td>
                    @endif

                    {{-- Data Evaluasi --}}
                    @if(in_array($type, ['evaluasi', 'pengendalian', 'peningkatan']))
                        <td class="text-wrap">{{ $detail->mtid_evaluasi ?? '-' }}</td>
                    @endif

                    {{-- Data Pengendalian --}}
                    @if(in_array($type, ['pengendalian', 'peningkatan']))
                        <td class="text-wrap">{{ $detail->mtid_tindaklanjut ?? '-' }}</td>
                    @endif

                    {{-- Data Peningkatan --}}
                    @if($type == 'peningkatan')
                        <td class="text-wrap">{{ $detail->mtid_peningkatan ?? '-' }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    @php
                        // Menyesuaikan colspan agar garis tabel tidak rusak saat data kosong
                        $colCount = 3;
                        if($type == 'penetapan') $colCount = 5;
                        elseif($type == 'pelaksanaan') $colCount = 4;
                        elseif($type == 'evaluasi') $colCount = 5;
                        elseif($type == 'pengendalian') $colCount = 6;
                        elseif($type == 'peningkatan') $colCount = 7;
                    @endphp
                    <td colspan="{{ $colCount }}" class="text-center">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>