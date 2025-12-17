@extends('layouts.app')

@section('title', 'Analytics Hasil Monitoring')

@push('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    
    <style>
        :root {
            --primary: #4F46E5;   /* Indigo - Professional Blue */
            --terlampaui: #0e24e9; /* Sky Blue */
            --tercapai: #10B981;   /* Emerald Green */
            --warning: #F59E0B;    /* Amber */
            --danger: #EF4444;     /* Red */
            --dark: #1E293B;       /* Slate 800 */
            --muted: #64748B;      /* Slate 500 */
            --bg-body: #F1F5F9;    /* Slate 100 */
        }

        body { background-color: var(--bg-body); }

        .metric-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            height: 100%;
            transition: all 0.2s ease-in-out;
            position: relative;
            overflow: hidden;
        }

        .metric-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-color: #cbd5e1;
        }

        .metric-card .icon-box {
            width: 48px; height: 48px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            margin-bottom: 12px;
        }

        .metric-card .value {
            font-size: 28px;
            font-weight: 800;
            color: var(--dark);
            line-height: 1.2;
            margin-bottom: 4px;
        }

        .metric-card .label {
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--muted);
        }

        /* Border Left Accents */
        .border-l-primary { border-left: 4px solid var(--primary); }
        .border-l-info { border-left: 4px solid var(--terlampaui); }
        .border-l-success { border-left: 4px solid var(--tercapai); }
        .border-l-warning { border-left: 4px solid var(--warning); }
        .border-l-danger { border-left: 4px solid var(--danger); }

        /* Icon Colors */
        .bg-soft-primary { background: #EEF2FF; color: var(--primary); }
        .bg-soft-info { background: #E0F2FE; color: var(--terlampaui); }
        .bg-soft-success { background: #D1FAE5; color: var(--tercapai); }
        .bg-soft-warning { background: #FEF3C7; color: var(--warning); }
        .bg-soft-danger { background: #FEE2E2; color: var(--danger); }

        /* --- FILTERS --- */
        .filter-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }
        .form-select-pro {
            border-radius: 8px;
            border-color: #cbd5e1;
            font-size: 14px;
            padding: 10px 12px;
        }
        .form-select-pro:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
        }

         /* --- TABLE PRO STYLING --- */
    .table-responsive {
        border-radius: 12px;
        overflow: hidden;
    }

    .table-pro {
        width: 100% !important;
        border-collapse: separate;
        border-spacing: 0;
        border: none;
    }

    .table-pro thead th {
        background-color: #F8FAFC; /* Slate 50 */
        color: #64748B;            /* Slate 500 */
        font-size: 0.75rem;        /* 12px */
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 18px 16px;
        border-bottom: 1px solid #E2E8F0;
        white-space: nowrap;
    }

    .table-pro tbody td {
        padding: 16px;
        vertical-align: middle;
        border-bottom: 1px solid #F1F5F9;
        font-size: 0.875rem;       /* 14px */
        color: #334155;            /* Slate 700 */
        background: #fff;
    }

    .table-pro tbody tr:last-child td {
        border-bottom: none;
    }

    .table-pro tbody tr:hover td {
        background-color: #F8FAFC; /* Highlight row saat hover */
    }

    /* Column Specifics */
    .col-number {
        font-family: 'Courier New', Courier, monospace; /* Monospace agar angka sejajar */
        font-weight: 600;
        font-size: 0.9rem;
    }

    /* Status Pills Refined */
    .status-pill {
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        line-height: 1;
        border: 1px solid transparent;
    }
    
    /* Warna Status Presisi */
    .pill-terlampaui { background: #E0F2FE; color: #0306a1; border-color: #BAE6FD; }
    .pill-tercapai   { background: #DCFCE7; color: #15803D; border-color: #BBF7D0; }
    .pill-gagal      { background: #FEF3C7; color: #B45309; border-color: #FDE68A; }
    .pill-kosong     { background: #FEE2E2; color: #B91C1C; border-color: #FECACA; }

    /* --- DATATABLES CUSTOMIZATION --- */
    .dataTables_wrapper .dataTables_filter {
        float: right;
        margin-bottom: 15px;
        padding-right: 15px;
        padding-top: 15px;
    }
    
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 0.875rem;
        outline: none;
        transition: all 0.2s;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .dataTables_wrapper .dataTables_paginate {
        padding: 15px;
        display: flex;
        justify-content: flex-end;
        gap: 5px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 5px 12px;
        border-radius: 6px;
        border: 1px solid #E2E8F0;
        background: white;
        color: #64748B !important;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #F1F5F9 !important;
        color: var(--primary) !important;
        border-color: #CBD5E1;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--primary) !important;
        color: white !important;
        border-color: var(--primary);
    }
    
    .dataTables_wrapper .dataTables_info {
        padding: 20px;
        color: #94A3B8;
        font-size: 0.8rem;
        font-weight: 500;
    }
        
        /* Status Badges */
        .status-pill {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .pill-terlampaui { background: #E0F2FE; color: #0220c7; }
        .pill-tercapai { background: #D1FAE5; color: #059669; }
        .pill-gagal { background: #FEF3C7; color: #D97706; }
        .pill-kosong { background: #FEE2E2; color: #DC2626; }

    </style>
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        
        <div class="section-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="text-danger font-weight-bold" style="font-size: 24px; margin-bottom: 5px;">Dashboard Hasil Monitoring</h1>
                <p class="text-muted mb-0">Laporan hasil monitoring dan evaluasi indikator kinerja.</p>
            </div>
            <div>
                <a href="{{ url('dashboard') }}" class="btn btn-danger border shadow-sm rounded-lg px-4 py-2 font-weight-600">
                    <i class="fas fa-arrow-left mr-2"></i> Dashboard Utama
                </a>
            </div>
        </div>

        <div class="filter-card mb-4 p-4">
            <form method="GET" action="{{ route('hasil.monitoring') }}">
                <div class="row align-items-end g-3">
                    <div class="col-md-3">
                        <label class="font-weight-bold small text-muted mb-2">TAHUN ANGGARAN</label>
                        <select class="form-control form-select-pro" name="tahun" onchange="this.form.submit()">
                            @foreach($tahuns as $th)
                                <option value="{{ $th->th_id }}" {{ (optional($tahunAktif)->th_id == $th->th_id) ? 'selected' : '' }}>
                                    {{ $th->th_tahun }} {{ $th->th_is_aktif == 'y' ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="font-weight-bold small text-muted mb-2">UNIT KERJA</label>
                        <select class="form-control form-select-pro" name="unit" onchange="this.form.submit()">
                            <option value="">Semua Unit Kerja</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->unit_id }}" {{ $unitFilter == $unit->unit_id ? 'selected' : '' }}>
                                    {{ $unit->unit_nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="font-weight-bold small text-muted mb-2">PROGRAM STUDI</label>
                        <select class="form-control form-select-pro" name="prodi" onchange="this.form.submit()">
                            <option value="">Semua Program Studi</option>
                            @foreach($prodis as $prodi)
                                <option value="{{ $prodi->prodi_id }}" {{ $prodiFilter == $prodi->prodi_id ? 'selected' : '' }}>
                                    {{ $prodi->nama_prodi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('hasil.monitoring') }}" class="btn btn-light border btn-block py-2 rounded-lg font-weight-bold text-muted">
                            <i class="fas fa-redo-alt mr-2"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="row row-cols-1 row-cols-md-3 row-cols-xl-5 g-3 mb-4">
            
            <!-- 1. TOTAL -->
            <div class="col">
                <div class="metric-card border-l-primary">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="value">{{ $stats['total'] }}</div>
                            <div class="label">Total Indikator</div>
                        </div>
                        <div class="icon-box bg-soft-primary">
                            <i class="fas fa-layer-group"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. TERLAMPAUI -->
            <div class="col">
                <div class="metric-card border-l-info">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="value">{{ $stats['terlampaui'] }}</div>
                            <div class="label">Terlampaui</div>
                        </div>
                        <div class="icon-box bg-soft-info">
                            <i class="fas fa-arrow-trend-up"></i>
                        </div>
                    </div>
                    @php $p_terlampaui = $stats['total'] > 0 ? round(($stats['terlampaui']/$stats['total'])*100) : 0; @endphp
                    <div class="mt-2 small text-info font-weight-bold">{{ $p_terlampaui }}% dari total</div>
                </div>
            </div>

            <!-- 3. TERCAPAI -->
            <div class="col">
                <div class="metric-card border-l-success">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="value">{{ $stats['tercapai'] }}</div>
                            <div class="label">Tercapai</div>
                        </div>
                        <div class="icon-box bg-soft-success">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                    @php $p_tercapai = $stats['total'] > 0 ? round(($stats['tercapai']/$stats['total'])*100) : 0; @endphp
                    <div class="mt-2 small text-success font-weight-bold">{{ $p_tercapai }}% dari total</div>
                </div>
            </div>

            <!-- 4. TIDAK TERCAPAI -->
            <div class="col">
                <div class="metric-card border-l-warning">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="value">{{ $stats['tidak_tercapai'] }}</div>
                            <div class="label">Tidak Tercapai</div>
                        </div>
                        <div class="icon-box bg-soft-warning">
                            <i class="fas fa-exclamation"></i>
                        </div>
                    </div>
                    @php $p_gagal = $stats['total'] > 0 ? round(($stats['tidak_tercapai']/$stats['total'])*100) : 0; @endphp
                    <div class="mt-2 small text-warning font-weight-bold">{{ $p_gagal }}% dari total</div>
                </div>
            </div>

            <!-- 5. TIDAK TERLAKSANA -->
            <div class="col">
                <div class="metric-card border-l-danger">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="value">{{ $stats['tidak_terlaksana'] }}</div>
                            <div class="label">Belum Ada Data</div>
                        </div>
                        <div class="icon-box bg-soft-danger">
                            <i class="fas fa-times"></i>
                        </div>
                    </div>
                    @php $p_null = $stats['total'] > 0 ? round(($stats['tidak_terlaksana']/$stats['total'])*100) : 0; @endphp
                    <div class="mt-2 small text-danger font-weight-bold">{{ $p_null }}% dari total</div>
                </div>
            </div>
        </div>

        <!-- CHARTS SECTION -->
        <div class="row mb-4">
            <!-- Pie Chart -->
            <div class="col-lg-4 mb-4 mb-lg-0">
                <div class="card border-0 shadow-sm rounded-lg h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h6 class="font-weight-bold text-dark mb-0">Proporsi Capaian</h6>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div style="position: relative; height: 260px; width: 100%;">
                            <canvas id="doughnutChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bar Chart -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-lg h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between">
                        <h6 class="font-weight-bold text-dark mb-0">Analisis Kinerja</h6>
                    </div>
                    <div class="card-body">
                        <div style="position: relative; height: 260px; width: 100%;">
                            <canvas id="barChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DATA TABLE -->
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
            <div class="card-header bg-white border-bottom px-4 py-4">
                <h6 class="font-weight-bold text-dark mb-0" style="font-size: 1.1rem;">
                    <i class="fas fa-list-alt mr-2 text-primary"></i> Rincian Data Monitoring
                </h6>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-pro mb-0" id="table-monitoring">
                        <thead>
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th width="35%">Indikator Kinerja</th>
                                <th class="text-center" width="12%">Target</th>
                                <th class="text-center" width="12%">Capaian</th>
                                <th class="text-center" width="15%">Status</th>
                                <th width="21%">Unit Penanggung Jawab</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                            <tr>
                                <td class="text-center text-muted font-weight-bold">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <div class="mb-1">
                                            <span class="badge bg-light text-dark border me-1">{{ $item->indikatorKinerja->ik_kode }}</span>
                                        </div>
                                        <span class="text-dark font-weight-600 mb-1" style="line-height: 1.4;">
                                            {{ Str::limit($item->indikatorKinerja->ik_nama, 90) }}
                                        </span>
                                        <div class="d-flex align-items-center mt-1">
                                            <span class="text-xs text-muted font-weight-bold text-uppercase" style="font-size: 11px;">
                                                <i class="fas fa-graduation-cap mr-1"></i> {{ $item->prodi->nama_prodi ?? 'Umum' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="col-number text-dark bg-light rounded py-1 px-2 d-inline-block border">
                                        {{ $item->ti_target }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if(optional($item->monitoringDetail)->mtid_capaian)
                                        <div class="col-number text-primary font-weight-bold">
                                            {{ $item->monitoringDetail->mtid_capaian }}
                                        </div>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @php
                                        $st = optional($item->monitoringDetail)->mtid_status;
                                        $pillClass = 'pill-kosong'; 
                                        $icon = 'fa-ban'; 
                                        $label = 'Belum Ada';

                                        if(str_contains(strtolower($st), 'terlampaui')) { 
                                            $pillClass = 'pill-terlampaui'; $icon = 'fa-arrow-trend-up'; $label = 'Terlampaui'; 
                                        }
                                        elseif(str_contains(strtolower($st), 'tercapai')) { 
                                            $pillClass = 'pill-tercapai'; $icon = 'fa-check'; $label = 'Tercapai'; 
                                        }
                                        elseif(str_contains(strtolower($st), 'tidak tercapai')) { 
                                            $pillClass = 'pill-gagal'; $icon = 'fa-triangle-exclamation'; $label = 'Tidak Tercapai'; 
                                        }
                                        elseif(str_contains(strtolower($st), 'tidak terlaksana')) { 
                                            $pillClass = 'pill-gagal'; $icon = 'fa-times'; $label = 'Gagal'; 
                                        }
                                    @endphp
                                    <span class="status-pill {{ $pillClass }}">
                                        <i class="fas {{ $icon }}"></i> {{ $label }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        @foreach($item->indikatorKinerja->unitKerja as $uk)
                                            <div class="d-flex align-items-center text-muted">
                                                <i class="fas fa-building mr-2" style="font-size: 10px; opacity: 0.6;"></i> 
                                                <span style="font-size: 13px;">{{ $uk->unit_nama }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </section>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
        window.addEventListener('load', function() {
        $(document).ready(function() {
            $('#table-monitoring').DataTable({
                "pageLength": 10,
                "lengthChange": false, 
                "ordering": false,    
                "language": {
                    "search": "",
                    "searchPlaceholder": "Ketik untuk mencari...",
                    "info": "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    "infoEmpty": "Tidak ada data",
                    "infoFiltered": "(disaring dari _MAX_ total data)",
                    "zeroRecords": "Data tidak ditemukan",
                    "paginate": {
                        "next": "<i class='fas fa-chevron-right'></i>",
                        "previous": "<i class='fas fa-chevron-left'></i>"
                    }
                },
                // DOM Layout: 
                // f = filtering (search)
                // r = processing
                // t = table
                // i = information (showing 1 to 10...)
                // p = pagination
                "dom": "<'d-flex justify-content-end pb-2'f>rt<'d-flex justify-content-between align-items-center pt-3 px-3'ip>",
                
                "drawCallback": function() {
                    $('.dataTables_paginate > .paginate_button').addClass('btn btn-sm btn-white border mx-1');
                }
            });
        });
        });
    </script>

    <script>
        $(document).ready(function() {
            Chart.defaults.font.family = "'Inter', 'Segoe UI', sans-serif";
            Chart.defaults.font.size = 11;
            Chart.defaults.color = '#64748B';

            const chartData = {
                labels: ['Terlampaui', 'Tercapai', 'Tidak Tercapai', 'Belum Ada Data'],
                data: [
                    {{ $stats['terlampaui'] }},
                    {{ $stats['tercapai'] }},
                    {{ $stats['tidak_tercapai'] }},
                    {{ $stats['tidak_terlaksana'] }}
                ],
                colors: ['#0EA5E9', '#10B981', '#F59E0B', '#EF4444'],
                hoverColors: ['#0284C7', '#059669', '#D97706', '#DC2626']
            };

            const ctxDoughnut = document.getElementById('doughnutChart').getContext('2d');
            new Chart(ctxDoughnut, {
                type: 'doughnut',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        data: chartData.data,
                        backgroundColor: chartData.colors,
                        hoverBackgroundColor: chartData.hoverColors,
                        borderWidth: 0,
                        hoverOffset: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let value = context.raw;
                                    let total = context.chart._metasets[context.datasetIndex].total;
                                    let percentage = Math.round((value / total) * 100) + '%';
                                    return ` ${context.label}: ${value} (${percentage})`;
                                }
                            }
                        }
                    },
                    cutout: '75%',
                }
            });

            const ctxBar = document.getElementById('barChart').getContext('2d');
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Jumlah Indikator',
                        data: chartData.data,
                        backgroundColor: chartData.colors,
                        borderRadius: 6,
                        barPercentage: 0.5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1E293B',
                            padding: 10,
                            titleFont: { size: 13 },
                            bodyFont: { size: 12 }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [4, 4], color: '#E2E8F0' },
                            ticks: { precision: 0 }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        });
    </script>
@endpush