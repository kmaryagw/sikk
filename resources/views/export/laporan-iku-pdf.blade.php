<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Indikator Kinerja Utama/Tambahan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px; /* Sedikit diperkecil agar muat kolom baru */
        }

        h1 {
            text-align: center;
            font-size: 16px;
        }

        h4 {
            text-align: center;
            font-weight: normal;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            table-layout: fixed; /* Agar lebar kolom patuh pada persentase */
        }

        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            vertical-align: top; /* Agar teks panjang rata atas */
            word-wrap: break-word; /* Mencegah teks keluar tabel */
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 10px;
        }

        .badge-primary { background-color: #007bff; color: white; }
        .badge-info { background-color: #17a2b8; color: white; }

        .text-success { color: green; font-weight: bold; }
        .text-danger { color: red; font-weight: bold; }
        .text-warning { color: orange; font-weight: bold; }
        .text-primary { color: #007bff; font-weight: bold; }
        .text-muted { color: #888; font-style: italic; }
        
        /* Helper untuk rata kiri */
        .text-left { text-align: left !important; }
    </style>
</head>
<body>

    <h1>Laporan Indikator Kinerja Utama/Tambahan</h1>
    
    <h4>
        Tahun : {{ $tahun ?? 'Semua Tahun' }} | 
        Prodi : {{ $prodi ?? 'Semua Prodi' }} | 
        Unit : {{ $unit ?? 'Semua Unit' }}
    </h4>

    <table>
        <thead>
            <tr>
                {{-- Penyesuaian Lebar Kolom --}}
                <th style="width: 4%;">No</th>
                <th style="width: 8%;">Tahun</th>
                <th style="width: 12%;">Prodi</th>
                <th style="width: 12%;">Unit Kerja</th> <th style="width: 28%;">Indikator Kinerja</th>
                <th style="width: 10%;">Target</th>
                <th style="width: 10%;">Capaian</th>
                <th style="width: 12%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($target_capaians as $index => $target)
                @php
                    $indikatorRel = $target->indikatorKinerja;
                    $detailRel    = $target->monitoringDetail;
                    $tahunRel     = $target->tahunKerja;
                    $prodiRel     = $target->prodi;

                    $th_tahun   = $tahunRel->th_tahun ?? '-';
                    $nama_prodi = $prodiRel->nama_prodi ?? '-';
                    
                    // --- LOGIKA UNIT KERJA ---
                    // Mengambil relasi unitKerja (menggunakan pluck jaga-jaga jika many-to-many)
                    $units = $indikatorRel->unitKerja ?? collect([]);
                    $unitKerja = $units->pluck('unit_nama')->join(', ');
                    if(empty($unitKerja)) $unitKerja = '-';
                    // -------------------------

                    $ik_kode = $indikatorRel->ik_kode ?? '';
                    $ik_nama = $indikatorRel->ik_nama ?? '-';
                    $display_indikator = $ik_kode ? "{$ik_kode} - {$ik_nama}" : $ik_nama;

                    $ketercapaian = strtolower($indikatorRel->ik_ketercapaian ?? '');
                    $targetValue  = trim($target->ti_target ?? '');
                    $capaian      = trim($detailRel->mtid_capaian ?? '');
                    $status       = strtolower($detailRel->mtid_status ?? '');
                @endphp

                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $th_tahun }}</td>
                    <td>{{ $nama_prodi }}</td>
                    <td>{{ $unitKerja }}</td> <td class="text-left">{{ $display_indikator }}</td>

                    {{-- Target Capaian --}}
                    <td>
                        @if ($ketercapaian === 'persentase' && is_numeric(str_replace('%', '', $targetValue)))
                            {{ floatval(str_replace('%', '', $targetValue)) }}%
                        @elseif ($ketercapaian === 'nilai' && is_numeric($targetValue))
                            <span class="badge badge-primary">{{ $targetValue }}</span>
                        @elseif (in_array(strtolower($targetValue), ['ada', 'tidak']))
                            <span class="{{ strtolower($targetValue) === 'ada' ? 'text-success' : 'text-danger' }}">
                                {{ ucfirst(strtolower($targetValue)) }}
                            </span>
                        @elseif ($ketercapaian === 'rasio')
                            @php
                                $formattedRasio = $targetValue; 
                                $cleaned = preg_replace('/\s*/', '', $targetValue);
                                if (preg_match('/^\d+:\d+$/', $cleaned)) {
                                    [$left, $right] = explode(':', $cleaned);
                                    $formattedRasio = $left . ' : ' . $right;
                                }
                            @endphp
                            <span class="badge badge-info">{{ $formattedRasio }}</span>
                        @else
                            {{ $targetValue }}
                        @endif
                    </td>

                    {{-- Capaian --}}
                    <td>
                        @if ($ketercapaian === 'persentase' && is_numeric(str_replace('%', '', $capaian)))
                            {{ floatval(str_replace('%', '', $capaian)) }}%
                        @elseif ($ketercapaian === 'nilai' && is_numeric($capaian))
                            <span class="badge badge-primary">{{ $capaian }}</span>
                        @elseif ($ketercapaian === 'rasio')
                            @php
                                $formattedRasio = $capaian;
                                $cleaned = preg_replace('/\s*/', '', $capaian);
                                if (preg_match('/^\d+:\d+$/', $cleaned)) {
                                    [$left, $right] = explode(':', $cleaned);
                                    $formattedRasio = $left . ' : ' . $right;
                                }
                            @endphp
                            <span class="badge badge-info">{{ $formattedRasio }}</span>
                        @elseif (strtolower($capaian) === 'ada')
                            <span class="text-success">Ada</span>
                        @elseif (strtolower($capaian) === 'draft')
                            <span class="text-warning">Draft</span>
                        @elseif (!empty($capaian))
                            <span class="badge badge-primary">{{ $capaian }}</span>
                        @else
                            <span class="text-danger">Belum ada</span>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td>
                        @if ($status === 'tercapai')
                            <span class="text-success">Tercapai</span>
                        @elseif ($status === 'terlampaui')
                            <span class="text-primary">Terlampaui</span>
                        @elseif ($status === 'tidak tercapai')
                            <span class="text-warning">Tidak Tercapai</span>
                        @elseif ($status === 'tidak terlaksana')
                            <span class="text-danger">Tidak Terlaksana</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Tidak ada data</td> </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>