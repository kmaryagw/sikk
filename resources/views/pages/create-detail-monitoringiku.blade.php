@extends('layouts.app')

@section('title', 'Detail Monitoring IKU')

@push('style')
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
        .filter-capsule {
            background-color: #f8f9fa;
            border: 1px solid #e3e6f0;
            border-radius: 30px;
            padding: 4px 15px;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            min-width: 600px; 
        }

        .filter-capsule:hover, .filter-capsule:focus-within {
            background-color: #fff;
            border-color: #6777ef; 
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        }

        .filter-capsule .select2-container--default .select2-selection--single {
            background-color: transparent !important;
            border: none !important;
            height: 32px !important;
        }

        .filter-capsule .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 32px !important;
            color: #6c757d;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .filter-capsule .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 32px !important;
        }

        .btn-reset-filter {
            color: #fc544b;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            transition: 0.3s;
        }
        .btn-reset-filter:hover {
            background-color: #ffe5e5;
    }
    </style>
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Daftar Monitoring Indikator Kinerja</h1>
            </div>
                <div class="card">
                    <div class="card-header">
                        <div class="row w-100 align-items-center justify-content-between m-0">
                            <div class="col-md-7 col-12 p-0 mb-2 mb-md-0">
                                <h4 class="m-0" style="font-size: 1.1rem; line-height: 1.5;">
                                    Data Monitoring Prodi : 
                                    <span class="badge badge-info shadow-sm mx-1">
                                        {{ optional($monitoringiku->prodi)->nama_prodi ?? 'N/A' }}
                                    </span> 
                                    Tahun : 
                                    <span class="badge badge-primary shadow-sm mx-1">
                                        {{ optional($monitoringiku->tahunKerja)->th_tahun ?? 'N/A' }}
                                    </span>
                                </h4>
                            </div>
                            @if(Auth::user()->role === 'admin')
                                <div class="card-header-action">
                                    <form method="GET" action="{{ route('monitoringiku.create-detail', $monitoringiku->mti_id) }}">
                                        
                                        <div class="filter-capsule">
                                            <i class="fa-solid fa-filter text-muted mr-2"></i>
                                            <div style="flex-grow: 1;">
                                                <select class="form-control select2" name="unit_kerja" onchange="this.form.submit()">
                                                    <option value="">Filter Unit Kerja...</option>
                                                    @foreach($unitKerjas as $unit)
                                                        <option value="{{ $unit->unit_id }}" {{ $selectedUnit == $unit->unit_id ? 'selected' : '' }}>
                                                            {{ $unit->unit_nama }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @if($selectedUnit)
                                                <div class="border-left ml-2 pl-2">
                                                    <a href="{{ route('monitoringiku.create-detail', $monitoringiku->mti_id) }}" 
                                                    class="btn-reset-filter" 
                                                    title="Hapus Filter">
                                                        <i class="fa-solid fa-times"></i>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>  
                    <div class="card-body">
                        <form action="{{ route('monitoringiku.store-detail', ['mti_id' => $monitoringiku->mti_id]) }}" method="POST">
                            @csrf
                
                            <div class="table-responsive">
                                <table id="table-indikator" class="table table-hover table-bordered table-striped table-sm m-0">
                                    <thead>
                                        <tr class="text-center">
                                            @if(Auth::user()->role == 'admin')
                                                <th width="3%">No</th>
                                                <th width="20%">Indikator</th>
                                                <th width="5%">Baseline</th>
                                                <th width="5%">Target</th>
                                                <th width="12%">Jenis Ketercapaian</th>
                                                <th width="5%">URL</th>
                                                <th width="6.6%">Capaian</th>
                                                {{-- <th width="10%">Status</th> --}}
                                                <th width="10%">Keterangan</th>
                                                <th>Evaluasi</th>
                                                <th>Tindak Lanjut</th>
                                                <th>Peningkatan</th>
                                            @else
                                                <th width="3%">No</th>
                                                <th width="25%">Indikator</th>
                                                <th width="5%">Baseline</th>
                                                <th width="5%">Target</th>
                                                <th width="15%">Jenis Ketercapaian</th>
                                                <th width="10%">Capaian</th>
                                                {{-- <th width="10%">Status</th> --}}
                                                <th width="15%">Keterangan</th>
                                                <th width="10%">URL</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $no = 1;
                                            $isAdmin = Auth::user()->role === 'admin';
                                        @endphp
                                        
                                        @foreach ($targetIndikator as $indikator)
                                            @php
                                                $detail = $monitoringikuDetail->get($indikator->ti_id); 
                                                
                                                $indikatorKinerja = optional($indikator->indikatorKinerja);
                                                $idx = $loop->index;
                                                
                                                $baselineValue = optional($indikator->baselineTahun)->baseline;
                                                $targetValue   = $indikator->ti_target;                                                
                                                $isTargetMissing = ($targetValue === null || trim((string)$targetValue) === '');
                                                $isBaselineMissing = ($baselineValue === null || trim((string)$baselineValue) === '');

                                                $isLocked = $isTargetMissing || $isBaselineMissing;

                                                $capaianRaw = $detail->mtid_capaian ?? '';
                                                $capaianValue = old("mtid_capaian.$idx", $capaianRaw);

                                                if (strtolower($indikatorKinerja->ik_ketercapaian) === 'persentase' && !empty($capaianValue)) {
                                                    $capaianValue = str_replace('%', '', $capaianValue);
                                                }
                                            @endphp                                 
                                            <tr>
                                                <td class="text-center">{{ $no++ }}</td>

                                                @if($isAdmin)
                                                    <td class="text-justify">
                                                        {{ $indikatorKinerja->ik_kode ?? 'N/A' }} - {{ $indikatorKinerja->ik_nama ?? 'N/A' }}
                                                    </td>
                                                @else
                                                    <td>
                                                        {{ $indikatorKinerja->ik_kode ?? 'N/A' }} - {{ $indikatorKinerja->ik_nama ?? 'N/A' }}
                                                    </td>
                                                @endif

                                                <td class="text-center">{{ $baselineValue !== null && $baselineValue !== '' ? $baselineValue : '-' }}</td>
                                                <td class="text-center">{{ $targetValue !== null && $targetValue !== '' ? $targetValue : '-' }}</td>

                                                <td>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                @php
                                                                    $jenis = strtolower($indikatorKinerja->ik_ketercapaian ?? '');
                                                                    $icon = match ($jenis) {
                                                                        'nilai' => 'fa-solid fa-chart-line',
                                                                        'persentase' => 'fa-solid fa-percent',
                                                                        'rasio' => 'fa-solid fa-divide',
                                                                        'status' => 'fa-solid fa-flag-checkered',
                                                                        'ada/tidak' => 'fa-solid fa-toggle-on',
                                                                        'ketersediaan' => 'fa-solid fa-box-open',
                                                                        default => 'fa-solid fa-info-circle',
                                                                    };
                                                                @endphp
                                                                <i class="{{ $icon }}"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" class="form-control text-capitalize"
                                                            value="{{ $indikatorKinerja->ik_ketercapaian ?? '-' }}" readonly>
                                                        <input type="hidden" name="mtid_ketercapaian[{{ $idx }}]"
                                                            value="{{ $indikatorKinerja->ik_ketercapaian }}">
                                                    </div>
                                                </td>

                                                {{-- AREA ADMIN --}}
                                                @if($isAdmin)
                                                    <td>
                                                        <input type="url" class="form-control" style="max-width:200px"
                                                            name="mtid_url[{{ $idx }}]"
                                                            value="{{ old("mtid_url.$idx", $detail->mtid_url ?? '') }}" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="ti_id[{{ $idx }}]" value="{{ $indikator->ti_id }}">

                                                        @if(in_array(strtolower($indikatorKinerja->ik_ketercapaian), ['nilai', 'persentase']))
                                                            <input type="number" class="form-control" style="max-width:150px"
                                                                name="mtid_capaian[{{ $idx }}]" step="any"
                                                                value="{{ $capaianValue }}" readonly>
                                                        @elseif(strtolower($indikatorKinerja->ik_ketercapaian) === 'ketersediaan')
                                                            <select name="mtid_capaian[{{ $idx }}]" class="form-control" style="max-width:150px" disabled>
                                                                <option value="" {{ $capaianValue ? '' : 'selected' }}>Pilih</option>
                                                                <option value="ada"   {{ $capaianValue == 'ada' ? 'selected' : '' }}>Ada</option>
                                                                <option value="draft" {{ $capaianValue == 'draft' ? 'selected' : '' }}>Draft</option>
                                                            </select>
                                                            <input type="hidden" name="mtid_capaian[{{ $idx }}]" value="{{ $capaianValue }}">
                                                        @else
                                                            <input type="text" class="form-control" style="max-width:100px"
                                                                name="mtid_capaian[{{ $idx }}]" value="{{ $capaianValue }}" readonly>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        <textarea class="form-control" rows="3" style="max-width:200px"
                                                            name="mtid_keterangan[{{ $idx }}]" readonly>{{ old("mtid_keterangan.$idx", $detail->mtid_keterangan ?? '') }}</textarea>
                                                    </td>
                                                    
                                                    <td>
                                                        <textarea class="form-control" rows="3" style="max-width:200px"
                                                            name="mtid_evaluasi[{{ $idx }}]"
                                                            @if(empty($detail->mtid_capaian)) disabled @endif>{{ old("mtid_evaluasi.$idx", $detail->mtid_evaluasi ?? '') }}</textarea>
                                                    </td>

                                                    <td>
                                                        <textarea class="form-control" rows="3" style="max-width:200px"
                                                            name="mtid_tindaklanjut[{{ $idx }}]"
                                                            @if(empty($detail->mtid_capaian)) disabled @endif>{{ old("mtid_tindaklanjut.$idx", $detail->mtid_tindaklanjut ?? '') }}</textarea>
                                                    </td>

                                                    <td>
                                                        <textarea class="form-control" rows="3" style="max-width:200px"
                                                            name="mtid_peningkatan[{{ $idx }}]"
                                                            @if(empty($detail->mtid_capaian)) disabled @endif>{{ old("mtid_peningkatan.$idx", $detail->mtid_peningkatan ?? '') }}</textarea>
                                                    </td>

                                                @else
                                                    <td>
                                                        <input type="hidden" name="ti_id[{{ $idx }}]" value="{{ $indikator->ti_id }}">

                                                        @if(in_array(strtolower($indikatorKinerja->ik_ketercapaian), ['nilai', 'persentase']))
                                                            <input type="number" class="form-control" style="max-width:150px"
                                                                name="mtid_capaian[{{ $idx }}]" step="any"
                                                                value="{{ $capaianValue }}" @if($isLocked) readonly @endif>

                                                        @elseif(strtolower($indikatorKinerja->ik_ketercapaian) === 'ketersediaan')
                                                            <select name="mtid_capaian[{{ $idx }}]" class="form-control" style="max-width:150px"
                                                                    @if($isLocked) disabled @endif>
                                                                <option value="" {{ $capaianValue ? '' : 'selected' }}>Pilih</option>
                                                                <option value="ada"   {{ $capaianValue == 'ada' ? 'selected' : '' }}>Ada</option>
                                                                <option value="draft" {{ $capaianValue == 'draft' ? 'selected' : '' }}>Draft</option>
                                                            </select>
                                                            
                                                            @if($isLocked)
                                                                <input type="hidden" name="mtid_capaian[{{ $idx }}]" value="{{ $capaianValue }}">
                                                            @endif

                                                        @else
                                                            <input type="text" class="form-control" style="max-width:100px"
                                                                name="mtid_capaian[{{ $idx }}]" value="{{ $capaianValue }}"
                                                                @if($isLocked) readonly @endif>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        <textarea class="form-control" rows="3" style="max-width:200px"
                                                            name="mtid_keterangan[{{ $idx }}]"
                                                            @if($isLocked) readonly @endif>{{ old("mtid_keterangan.$idx", $detail->mtid_keterangan ?? '') }}</textarea>
                                                    </td>

                                                    <td>
                                                        <input type="url" class="form-control" style="max-width:200px"
                                                            name="mtid_url[{{ $idx }}]"
                                                            value="{{ old("mtid_url.$idx", $detail->mtid_url ?? '') }}"
                                                            placeholder="https://..."
                                                            @if($isLocked) readonly @endif>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3 text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-save"></i> Simpan
                                </button>
                                <a href="{{ route('monitoringiku.index-detail', $monitoringiku->mti_id) }}" class="btn btn-danger">
                                    <i class="fa-solid fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('library/cleave.js/dist/cleave.min.js') }}"></script>
    <script src="{{ asset('library/cleave.js/dist/addons/cleave-phone.us.js') }}"></script>
    <script src="{{ asset('library/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('library/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}"></script>
    <script src="{{ asset('library/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ asset('library/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('library/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>
    
    <script src="{{ asset('library/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('library/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('library/datatables/media/js/jquery.dataTables.js') }}"></script>
    @include('sweetalert::alert')

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/forms-advanced-forms.js') }}"></script>
    <script>
        $(document).ready(function () {
            $("#table-indikator").DataTable({
                "columnDefs": [
                    { "sortable": false, "targets": [4, 5, 6] }
                ],
                "paging": false, 
                "order": [[1, 'asc']],
                info: true,
                infoCallback: function(settings, start, end, max, total, pre) {
                    return `
                        <span class="badge bg-primary text-light px-3 py-2 m-3">
                            Total Data : ${total}
                        </span>
                    `;
                }
            });
        });
    </script>
@endpush