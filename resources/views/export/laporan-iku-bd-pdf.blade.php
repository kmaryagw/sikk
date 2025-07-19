<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan IKU - Program Studi Bisnis Digital</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
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
            vertical-align: middle;
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
            font-weight: bold;
        }

        .text-danger {
            color: red;
            font-weight: bold;
        }

        .text-warning {
            color: orange;
            font-weight: bold;
        }

        .text-primary {
            color: #007bff;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h1>Laporan IKU - Program Studi Bisnis Digital</h1>

    <table>
        <thead>
            <tr>
                <th style="width: 1%;">No</th>
                <th style="width: 10%;">Tahun</th>
                <th>Indikator Kinerja</th>
                <th style="width: 10%;">Satuan</th>
                <th style="width: 10%;">Target</th>
                <th style="width: 10%;">Capaian</th>
                <th style="width: 15%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($target_capaians as $index => $target)
                @php
                    $ik = $target->indikatorKinerja;
                    $monitoring = $target->monitoringDetail;

                    $satuan = strtolower($ik->ik_ketercapaian ?? '-');
                    $targetVal = trim($target->ti_target ?? '-');
                    $capaian = trim($monitoring->mtid_capaian ?? '');
                    $status = strtolower($monitoring->mtid_status ?? '');
                @endphp

                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $target->th_tahun }}</td>
                    <td>{{ $ik->ik_nama ?? '-' }}</td>
                    <td>{{ ucfirst($satuan) }}</td>

                    {{-- Target --}}
                    <td>
                        @if ($satuan === 'persentase' && is_numeric(str_replace('%', '', $targetVal)))
                            {{ floatval(str_replace('%', '', $targetVal)) }}%
                        @elseif ($satuan === 'nilai' && is_numeric($targetVal))
                            <span class="badge badge-primary">{{ $targetVal }}</span>
                        @elseif (in_array(strtolower($targetVal), ['ada', 'tidak']))
                            <span class="{{ strtolower($targetVal) === 'ada' ? 'text-success' : 'text-danger' }}">
                                {{ ucfirst(strtolower($targetVal)) }}
                            </span>
                        @elseif ($satuan === 'rasio')
                            @php
                                $formattedRasio = 'Format salah';
                                $cleaned = preg_replace('/\s*/', '', $targetVal);
                                if (preg_match('/^\d+:\d+$/', $cleaned)) {
                                    [$left, $right] = explode(':', $cleaned);
                                    $formattedRasio = $left . ' : ' . $right;
                                }
                            @endphp
                            <span class="badge badge-info">{{ $formattedRasio }}</span>
                        @else
                            {{ $targetVal }}
                        @endif
                    </td>

                    {{-- Capaian --}}
                    <td>
                        @if ($satuan === 'persentase' && is_numeric(str_replace('%', '', $capaian)))
                            {{ floatval(str_replace('%', '', $capaian)) }}%
                        @elseif ($satuan === 'nilai' && is_numeric($capaian))
                            <span class="badge badge-primary">{{ $capaian }}</span>
                        @elseif ($satuan === 'rasio')
                            @php
                                $formattedRasio = 'Format salah';
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
                            Belum ada Status
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
