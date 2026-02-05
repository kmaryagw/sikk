<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Indikator Kinerja Utama/Tambahan</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            margin: 0.5cm;
        }

        /* --- Style Header Formal --- */
        .header-container {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #000; /* Garis ganda formal */
            padding-bottom: 10px;
        }

        .header-container h1 {
            text-transform: uppercase;
            margin: 0;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .header-container p {
            margin: 2px 0;
            font-size: 12px;
            font-weight: normal;
        }

        /* Tabel Informasi Metadata */
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            border: none;
        }

        .info-table td {
            border: none !important; /* Menghilangkan border untuk info */
            text-align: left;
            padding: 2px 0;
            vertical-align: middle;
            font-size: 11px;
        }

        .info-label {
            width: 15%;
            font-weight: bold;
        }

        .info-separator {
            width: 2%;
        }
        /* --------------------------- */

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            vertical-align: top;
            word-wrap: break-word;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
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
        .text-left { text-align: left !important; }
    </style>
</head>
<body>

    <div class="header-container">
        <h1>LAPORAN CAPAIAN INDIKATOR KINERJA</h1>
        <p>Institut Bisnis dan Teknologi Indonesia</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="info-label">Tahun Kerja</td>
            <td class="info-separator">:</td>
            <td>{{ $tahun ?? 'Semua Tahun' }}</td>
        </tr>
        <tr>
            <td class="info-label">Program Studi</td>
            <td class="info-separator">:</td>
            <td>{{ $prodi ?? 'Semua Program Studi' }}</td>
        </tr>
        <tr>
            <td class="info-label">Unit Kerja</td>
            <td class="info-separator">:</td>
            <td>{{ $unit ?? 'Semua Unit Kerja' }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 28%;">Indikator Kinerja</th>
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
                    <td class="text-left">{{ $display_indikator }}</td>
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
                    <td colspan="5">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>