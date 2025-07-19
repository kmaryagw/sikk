<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rencana Kerja - Program Studi {{ $namaProdi }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            color: #333;
            background-color: #fff;
            margin: 20px;
        }
    
        h1 {
            text-align: center;
            font-size: 20px;
            margin-bottom: 25px;
            color: #2c3e50;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 5px;
        }
    
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            vertical-align: top;
            font-size: 12px;
        }
    
        th {
            background-color: #f8f9fa;
            color: #212529;
            font-weight: 600;
            text-align: center;
        }
    
        tr:nth-child(even) {
            background-color: #fefefe;
        }
    
    
        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 10px;
            font-weight: 500;
            color: #fff;
            background-color: #17a2b8;
            border-radius: 10px;
            margin-right: 4px;
        }
    
        .text-muted {
            color: #6c757d;
        }
    
        .text-right {
            text-align: right;
        }
    
        .text-success {
            color: #28a745;
            font-weight: bold;
        }
    
        .text-center {
            text-align: center;
        }
    
        .table-footer {
            font-weight: bold;
            background-color: #e9ecef;
        }
    </style>
    
</head>
<body>

    <h1>Laporan Rencana Kerja - Program Studi {{ $namaProdi }}</h1>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tahun</th>
                <th>Program Studi</th>
                <th>Standar</th>
                <th>Program Kerja</th>
                <th>Unit Kerja</th>
                <th>Periode Monev</th>
                <th>Anggaran</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; $totalAnggaran = 0; @endphp
            @forelse ($rencanaKerjas as $rencanaKerja)
                @php
                    $filteredProdis = $rencanaKerja->programStudis->filter(fn($prodi) => $prodi->nama_prodi === $namaProdi);
                    $namaProdiTampil = $filteredProdis->pluck('nama_prodi')->first() ?? '-';
                    $namaPeriode = $rencanaKerja->periodes->pluck('pm_nama')->implode(', ');
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $no++ }}</td>
                    <td style="text-align: center;">{{ $rencanaKerja->tahunKerja->th_tahun ?? '-' }}</td>
                    <td style="text-align: center;">{{ $namaProdiTampil }}</td>
                    <td>
                        <strong>{{ $rencanaKerja->standar->std_nama ?? '-' }}</strong><br>
                        <span style="font-size: 11px; color: #555;">{{ $rencanaKerja->standar->std_deskripsi ?? '-' }}</span>
                    </td>
                    <td>{{ $rencanaKerja->rk_nama }}</td>
                    <td style="text-align: center;">{{ $rencanaKerja->UnitKerja->unit_nama ?? '-' }}</td>
                    <td style="text-align: center;">
                        {{ $namaPeriode ?: '-' }}
                    </td>
                    <td style="text-align: right;">
                        @if($rencanaKerja->anggaran !== null)
                            Rp {{ number_format($rencanaKerja->anggaran, 0, ',', '.') }}
                            @php $totalAnggaran += $rencanaKerja->anggaran; @endphp
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data rencana kerja.</td>
                </tr>
            @endforelse
        
            @if ($rencanaKerjas->isNotEmpty())
                <tr>
                    <td colspan="7" style="text-align: right; font-weight: bold;">Total Anggaran:</td>
                    <td style="text-align: right; font-weight: bold; color: green;">
                        Rp {{ number_format($totalAnggaran, 0, ',', '.') }}
                    </td>
                </tr>
            @endif
        </tbody>        
    </table>

</body>
</html>
