<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Indikator Kinerja Utama/Tambahan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        h1 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 11px;
        }

        .badge-primary {
            background-color: #007bff;
            color: white;
        }

        .badge-info {
            background-color: #17a2b8;
            color: white;
        }

        .text-success {
            color: green;
        }

        .text-danger {
            color: red;
        }

        .text-warning {
            color: orange;
        }

        .text-primary {
            color: #007bff;
        }
        
        /* Helper untuk rata kiri indikator */
        .text-left {
            text-align: left !important;
        }
    </style>
</head>
<body>

    <h1>
        Laporan Indikator Kinerja Utama/Tambahan
    </h1>
    <br>
    <h4>
        Tahun : {{ $tahun ?? 'Semua Tahun' }} | 
        Prodi : {{ $prodi ?? 'Semua Prodi' }} | 
        Unit : {{ $unit ?? 'Semua Unit' }}
    </h4>

    <table>
        <thead>
            <tr>
                <th style="width: 1%;">No</th>
                <th style="width: 10%;">Tahun</th>
                <th style="width: 10%;">Prodi</th>
                <th style="width: 30%;">Indikator Kinerja</th>
                <th style="width: 10%;">Target Capaian</th>
                <th style="width: 10%;">Capaian</th>
                <th style="width: 14%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($target_capaians as $index => $target)
                @php
                    // --- PERBAIKAN AKSES DATA RELASI ---
                    $indikatorRel = $target->indikatorKinerja;
                    $detailRel    = $target->monitoringDetail;
                    $tahunRel     = $target->tahunKerja;
                    $prodiRel     = $target->prodi;

                    // Ambil Data dari Relasi (Fallback ke '-' jika null)
                    $th_tahun   = $tahunRel->th_tahun ?? '-';
                    $nama_prodi = $prodiRel->nama_prodi ?? '-';
                    
                    // Gabungkan Kode & Nama Indikator
                    $ik_kode = $indikatorRel->ik_kode ?? '';
                    $ik_nama = $indikatorRel->ik_nama ?? '-';
                    $display_indikator = $ik_kode ? "{$ik_kode} - {$ik_nama}" : $ik_nama;

                    // Logic Formatting
                    $ketercapaian = strtolower($indikatorRel->ik_ketercapaian ?? '');
                    $targetValue  = trim($target->ti_target ?? '');
                    $capaian      = trim($detailRel->mtid_capaian ?? '');
                    $status       = strtolower($detailRel->mtid_status ?? '');
                @endphp

                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $th_tahun }}</td>
                    <td>{{ $nama_prodi }}</td>
                    
                    {{-- Indikator Kinerja (Rata Kiri) --}}
                    <td class="text-left">{{ $display_indikator }}</td>

                    {{-- Target --}}
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
                                $formattedRasio = $targetValue; // Default
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
                                $formattedRasio = $capaian; // Default
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
                            <span class="text-muted">Belum ada status</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>