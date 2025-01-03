<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Indikator Kinerja Utama/Tambahan</title>
    <style>
        /* Styling khusus untuk PDF */
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            background-color: #f2f2f2;
        }

        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

    </style>
</head>
<body>

    <h1>Laporan Indikator Kinerja Utama/Tambahan</h1>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Indikator Kinerja</th>
                <th>Target Capaian</th>
                <th>Keterangan</th>
                <th>Prodi</th>
                <th>Tahun</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($target_capaians as $key => $targetcapaian)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $targetcapaian->ik_nama }}</td>
                    <td>
                        @if ($targetcapaian->indikatorKinerja->ik_ketercapaian == 'persentase' && is_numeric($targetcapaian->ti_target))
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" 
                                     style="width: {{ intval($targetcapaian->ti_target) }}%;" 
                                     aria-valuenow="{{ intval($targetcapaian->ti_target) }}" 
                                     aria-valuemin="0" aria-valuemax="100">
                                    {{ $targetcapaian->ti_target }}%
                                </div>
                            </div>
                        @elseif ($targetcapaian->indikatorKinerja->ik_ketercapaian == 'nilai' && is_numeric($targetcapaian->ti_target))
                            <span class="badge badge-primary">{{ $targetcapaian->ti_target }}</span>
                        @elseif (in_array(strtolower($targetcapaian->ti_target), ['ada', 'tidak']))
                            @if (strtolower($targetcapaian->ti_target) === 'ada')
                                <span class="text-success"><i class="fa-solid fa-check-circle"></i> Ada</span>
                            @else
                                <span class="text-danger"><i class="fa-solid fa-times-circle"></i> Tidak</span>
                            @endif
                        @else
                            {{ $targetcapaian->ti_target }}
                        @endif
                    </td> 
                    <td>{{ $targetcapaian->ti_keterangan }}</td>
                    <td>{{ $targetcapaian->nama_prodi }}</td>
                    <td>{{ $targetcapaian->th_tahun }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>