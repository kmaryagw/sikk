@extends('layouts.app')

@section('title', 'Isi Target Indikator')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/bootstrap-daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">
    <link rel="stylesheet" href="{{ asset('library/datatables/media/css/jquery.dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('library/datatables/media/css/jquery.dataTables.min.css') }}">
    
    <style>
        .table-responsive {
            max-height: 50rem;  
            overflow-y: auto;  
        }

        .table thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #f8f9fa !important;
        }

        .baseline-readonly {
            background-color: #f0f4f8;
            padding: 8px;
            border-radius: 5px;
            display: inline-block;
            min-width: 80px;
            font-weight: bold;
            color: #2d3748;
        }
    </style>
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Form Target Indikator</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i> ERROR</div>
                                        <div class="alert-body">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            <ul class="mb-0 pl-3">
                                                @foreach ($errors->all() as $err)
                                                    <li>{{ $err }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif

                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 pb-3 border-bottom">
                                    <div class="mb-3 mb-md-0 w-100" style="max-width: 400px;">
                                        <form method="GET" action="{{ route('targetcapaianprodi.create') }}">
                                            <label class="text-muted font-weight-bold small text-uppercase mb-1">Filter Indikator:</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-white border-right-0">
                                                        <i class="fa-solid fa-filter text-primary"></i>
                                                    </span>
                                                </div>
                                                <select class="custom-select border-left-0" name="unit_kerja" onchange="this.form.submit()">
                                                    <option value="">Semua Unit Kerja</option>
                                                    @foreach($unitKerjas as $unit)
                                                        <option value="{{ $unit->unit_id }}" {{ $selectedUnit == $unit->unit_id ? 'selected' : '' }}>
                                                            {{ $unit->unit_nama }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if($selectedUnit)
                                                    <div class="input-group-append">
                                                        <a href="{{ route('targetcapaianprodi.create') }}" class="btn btn-outline-secondary" title="Reset Filter">
                                                            <i class="fa-solid fa-times"></i>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </form>
                                    </div>
                                    <div class="d-flex align-items-center flex-wrap gap-3">
                                        <div class="d-flex align-items-center bg-white shadow-sm rounded-lg p-2 mr-3 border-left-primary" style="border-left: 4px solid #4e73df; min-width: 160px;">
                                            <div class="p-2 mr-2 bg-primary-soft rounded-circle text-primary" style="background-color: #f0f4ff;">
                                                <i class="fa-solid fa-calendar-check fa-lg"></i>
                                            </div>
                                            <div>
                                                <small class="text-uppercase text-muted font-weight-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Tahun Aktif</small>
                                                <h6 class="mb-0 text-dark font-weight-bold">{{ $tahuns->th_tahun }}</h6>
                                            </div>
                                        </div>

                                        @if ($userRole === 'prodi' && $userProdi)
                                        <div class="d-flex align-items-center bg-white shadow-sm rounded-lg p-2 border-left-success" style="border-left: 4px solid #1cc88a; min-width: 200px;">
                                            <div class="p-2 mr-2 bg-success-soft rounded-circle text-success" style="background-color: #e6fffa;">
                                                <i class="fa-solid fa-university fa-lg"></i>
                                            </div>
                                            <div>
                                                <small class="text-uppercase text-muted font-weight-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Program Studi</small>
                                                <h6 class="mb-0 text-dark font-weight-bold" style="line-height: 1.2;">{{ $userProdi->nama_prodi }}</h6>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <form method="POST" action="{{ route('targetcapaianprodi.store') }}">
                            @csrf
                            <input type="hidden" name="prodi_id" value="{{ $userProdi->prodi_id ?? Auth::user()->prodi_id ?? '' }}">
                            <input type="hidden" name="th_id" value="{{ $tahuns->th_id }}">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Data Indikator</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-bordered table-striped m-0" id="table-indikator">
                                            <thead>
                                                <tr class="text-center">
                                                    <th>No</th>
                                                    <th>Indikator Kinerja</th>
                                                    <th>Jenis</th>
                                                    <th>Pengukuran</th>
                                                    <th>Nilai Baseline</th>
                                                    <th>Target</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php 
                                                    $no = 1; 
                                                    $data_tersimpan = collect($targetindikators);
                                                @endphp
                                                @foreach ($indikatorkinerjas as $ik)
                                                    @php
                                                        $found = $data_tersimpan->where('ik_id', $ik->ik_id)->first();
                                                        $db_baseline = $found ? $found['baseline'] : null;
                                                        
                                                        // Tentukan nilai baseline
                                                        if ($db_baseline !== null && $db_baseline !== '') {
                                                            $raw_baseline = $db_baseline;
                                                        } elseif (isset($baseline_from_prev[$ik->ik_id])) {
                                                            $raw_baseline = $baseline_from_prev[$ik->ik_id];
                                                        } else {
                                                            // Fallback default
                                                            $raw_baseline = match($ik->ik_ketercapaian) {
                                                                'nilai', 'persentase' => '0',
                                                                'ketersediaan' => 'draft',
                                                                'rasio' => '0:0',
                                                                default => ''
                                                            };
                                                        }

                                                        $baseline_value = old("indikator.$no.baseline", $raw_baseline);

                                                        // Tentukan nilai target
                                                        $db_target = $found ? $found['ti_target'] : null;
                                                        if ($db_target !== null && $db_target !== '') {
                                                            $raw_target = $db_target;
                                                        } else {
                                                            $raw_target = match($ik->ik_ketercapaian) {
                                                                'nilai', 'persentase' => '0',
                                                                'ketersediaan' => 'ada',
                                                                'rasio' => '0:0',
                                                                default => ''
                                                            };
                                                        }
                                                        $target_value = old("indikator.$no.target", $raw_target);
                                                    @endphp

                                                    <tr>
                                                        <td class="text-center">{{ $no }}</td>
                                                        <td>{{ $ik->ik_kode }} - {{ $ik->ik_nama }}</td>
                                                        <td class="text-center">
                                                            @if (strtoupper($ik->ik_jenis) == 'IKU')
                                                                <span class="badge badge-success">IKU</span>
                                                            @elseif (strtoupper($ik->ik_jenis) == 'IKT')
                                                                <span class="badge badge-primary">IKT</span>
                                                            @else
                                                                <span class="badge badge-danger">IKU/IKT</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center text-capitalize">
                                                            <span class="text-primary font-weight-bold"> {{ $ik->ik_ketercapaian }}</span>
                                                        </td>
                                                        
                                                        {{-- KOLOM BASELINE --}}
                                                        <td class="text-center">
                                                            @if($isFirstYear)
                                                                {{-- Jika Tahun Pertama: Tampilkan Input --}}
                                                                @if($ik->ik_ketercapaian == 'nilai' || $ik->ik_ketercapaian == 'persentase')
                                                                    <input type="number" class="form-control" name="indikator[{{ $no }}][baseline]" step="any" value="{{ $baseline_value }}">
                                                                @elseif($ik->ik_ketercapaian == 'ketersediaan')
                                                                    <select class="form-control" name="indikator[{{ $no }}][baseline]">
                                                                        <option value="ada" {{ strtolower($baseline_value) == 'ada' ? 'selected' : '' }}>Ada</option>
                                                                        <option value="draft" {{ strtolower($baseline_value) == 'draft' ? 'selected' : '' }}>Draft</option>
                                                                    </select>
                                                                @elseif($ik->ik_ketercapaian == 'rasio')
                                                                    <input type="text" class="form-control" name="indikator[{{ $no }}][baseline]" pattern="^\d+\s*:\s*\d+$" placeholder="0:0" value="{{ $baseline_value }}">
                                                                @else
                                                                    <input type="text" class="form-control" name="indikator[{{ $no }}][baseline]" value="{{ $baseline_value }}">
                                                                @endif
                                                            @else
                                                                {{-- Jika Bukan Tahun Pertama: Tampilkan View Only + Hidden Input --}}
                                                                <span class="baseline-readonly">
                                                                    {{ ($baseline_value !== null && $baseline_value !== '') ? $baseline_value : '-' }}
                                                                </span>
                                                                <input type="hidden" name="indikator[{{ $no }}][baseline]" value="{{ $baseline_value }}">
                                                            @endif
                                                            <input type="hidden" name="indikator[{{ $no }}][ik_id]" value="{{ $ik->ik_id }}">
                                                        </td>     

                                                        {{-- KOLOM TARGET --}}
                                                        <td class="text-center">
                                                            @if($ik->ik_ketercapaian == 'nilai' || $ik->ik_ketercapaian == 'persentase')
                                                                <input type="number" class="form-control" name="indikator[{{ $no }}][target]" step="any" value="{{ $target_value }}">
                                                            @elseif($ik->ik_ketercapaian == 'ketersediaan')
                                                                <select class="form-control" name="indikator[{{ $no }}][target]">
                                                                    <option value="ada" {{ strtolower($target_value) == 'ada' ? 'selected' : '' }}>Ada</option>
                                                                    <option value="draft" {{ strtolower($target_value) == 'draft' ? 'selected' : '' }}>Draft</option>
                                                                </select>
                                                            @elseif($ik->ik_ketercapaian == 'rasio')
                                                                <input type="text" class="form-control" name="indikator[{{ $no }}][target]" pattern="^\d+\s*:\s*\d+$" placeholder="0:0" value="{{ $target_value }}">
                                                            @else
                                                                <input type="text" class="form-control" name="indikator[{{ $no }}][target]" value="{{ $target_value }}">
                                                            @endif
                                                        </td>
                                                        @php $no++; @endphp
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan</button>
                                    <a href="{{ url('targetcapaianprodi') }}" class="btn btn-danger"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
                                </div>
                            </div>
                        </form>  
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraries -->
    <script src="{{ asset('library/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('library/select2/dist/js/select2.full.min.js') }}"></script>

    @include('sweetalert::alert')

    <script>
        $(document).ready(function () {
            // Inisialisasi DataTable
            $("#table-indikator").DataTable({
                "columnDefs": [
                    { "sortable": false, "targets": [4, 5] }
                ],
                "paging": false, 
                "searching": true, 
                "order": [[1, 'asc']],
                "info": true,
                "language": {
                    "search": "Cari Indikator:",
                }
            });
        });
    </script>
@endpush