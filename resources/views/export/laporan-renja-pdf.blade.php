<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rencana Kerja</title>
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

    <h1>Laporan Rencana Kerja</h1>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Program Kerja</th>
                <th>Unit Kerja</th>
                <th>Tahun</th>
                <th>Periode Monev</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rencanaKerjas as $key => $rencanaKerja)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $rencanaKerja->rk_nama }}</td>
                    <td>{{ $rencanaKerja->UnitKerja->unit_nama ?? '-' }}</td>
                    <td>{{ $rencanaKerja->tahunKerja->th_tahun ?? '-' }}</td>
                    <td>
                        @foreach ($rencanaKerja->periodes as $periode)
                            <span>{{ $periode->pm_nama }}</span>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>