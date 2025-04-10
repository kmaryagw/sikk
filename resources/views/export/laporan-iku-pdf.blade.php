<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Indikator Kinerja Utama/Tambahan</title>
    <link rel="stylesheet" href="{{ asset('css/circular-progress-bar.css') }}">
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
    </style>
</head>
<body>

    <h1>Laporan Indikator Kinerja Utama/Tambahan</h1>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tahun</th>
                <th>Prodi</th>
                <th>Indikator Kinerja</th>
                <th>Target Capaian</th>
                <th>Capaian</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($target_capaians as $index => $target)
                @php
                    $ketercapaian = strtolower(optional($target->indikatorKinerja)->ik_ketercapaian ?? '');
                    $targetValue = trim($target->ti_target);
                    $capaian = optional($target->monitoringDetail)->mtid_capaian;
                    $status = strtolower(optional($target->monitoringDetail)->mtid_status ?? '');
                @endphp

                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $target->th_tahun }}</td>
                    <td>{{ $target->nama_prodi }}</td>
                    <td>{{ $target->ik_nama }}</td>

                    {{-- Target --}}
                    <td>
                        @if ($ketercapaian === 'persentase' && is_numeric(str_replace('%', '', $targetValue)))
                            {{ floatval($targetValue) }}%
                        @elseif ($ketercapaian === 'nilai' && is_numeric($targetValue))
                            <span class="badge badge-primary">{{ $targetValue }}</span>
                        @elseif (in_array(strtolower($targetValue), ['ada', 'tidak']))
                            {!! strtolower($targetValue) === 'ada' 
                                ? '<span class="text-success">Ada</span>' 
                                : '<span class="text-danger">Tidak</span>' !!}
                        @elseif ($ketercapaian === 'rasio')
                            <span class="badge badge-info">{{ $targetValue }}</span>
                        @else
                            {{ $targetValue }}
                        @endif
                    </td>

                    {{-- Capaian --}}
                    <td>
                        @if (strpos($capaian, '%') !== false)
                            {{ floatval(str_replace('%', '', $capaian)) }}%
                        @elseif (is_numeric($capaian) && $ketercapaian == 'nilai')
                            <span class="badge badge-primary">{{ $capaian }}</span>
                        @elseif (preg_match('/^\d+:\d+$/', $capaian))
                            <span class="badge badge-info">{{ $capaian }}</span>
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

                    {{-- Keterangan Status --}}
                    <td>
                        @if ($status === 'tercapai')
                            <span class="text-success">Tercapai</span>
                        @elseif ($status === 'tidak tercapai')
                            <span class="text-warning">Tidak Tercapai</span>
                        @elseif ($status === 'tidak terlaksana')
                            <span class="text-danger">Tidak Terlaksana</span>
                        @else
                            Belum ada
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
