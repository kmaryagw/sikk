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
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
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
                    <td>{{ $targetcapaian->ti_target }}</td>
                    <td>{{ $targetcapaian->ti_keterangan }}</td>
                    <td>{{ $targetcapaian->nama_prodi }}</td>
                    <td>{{ $targetcapaian->th_tahun }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>