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
            max-height: none !important; /* Menghilangkan batas tinggi */
            overflow-y: visible !important; /* Menghilangkan scroll bar vertical */
            overflow-x: auto; /* Tetap izinkan scroll horizontal jika layar hp/kecil */      
        }
        .table thead th {
           position: sticky;
            top: 0; 
            z-index: 100;
            background-color: #f8f9fa !important;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
        }

        /* Styling Filter Baru yang Lebih Compact */
        .filter-group-clean {
            display: flex;
            align-items: center;
            background-color: #fff;
            border: 1px solid #e3e6f0;
            border-radius: 0.25rem;
            padding: 0;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .filter-group-clean:focus-within {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .filter-group-clean .input-group-icon {
            padding: 0.375rem 0.75rem;
            background: transparent;
            border: none;
            color: #4e73df; /* Warna Primary */
        }

        .filter-group-clean .custom-select {
            border: none !important;
            box-shadow: none !important;
            background-color: transparent;
            height: calc(2.25rem + 2px);
        }

        .filter-group-clean .btn-reset {
            border: none;
            background: transparent;
            color: #e74a3b; /* Warna Danger */
            padding: 0.375rem 0.75rem;
            display: flex;
            align-items: center;
            cursor: pointer;
            text-decoration: none;
        }
        
        .filter-group-clean .btn-reset:hover {
            background-color: #f8f9fa;
            color: #c0392b;
        }

        /* Merapikan area info dan paging */
        .dataTables_wrapper .dataTables_info {
            padding-top: 1rem;
            font-weight: 600;
            color: #6c757d;
        }

        .dataTables_wrapper .dataTables_paginate {
            padding-top: 1rem;
        }

        /* Mempercantik Tombol Pagination */
        .paginate_button.page-item.active .page-link {
            background-color: #4e73df !important; /* Warna Primary Anda */
            border-color: #4e73df !important;
            box-shadow: 0 4px 6px rgba(78, 115, 223, 0.2);
        }

        .paginate_button.page-item .page-link {
            border-radius: 5px !important;
            margin: 0 3px;
            color: #4e73df;
            transition: all 0.3s ease;
        }

        .paginate_button.page-item:hover .page-link {
            background-color: #f8f9fa;
            transform: translateY(-1px);
        }

        /* Sticky Bottom Action Bar (UX SANGAT PENTING) */
        /* Karena tabel panjang, tombol simpan harus selalu terlihat */
        .action-bar-sticky {
            position: sticky;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
            padding: 15px;
            border-top: 1px solid #e3e6f0;
            z-index: 100;
            box-shadow: 0 -5px 15px rgba(0,0,0,0.05);
        }
    </style>
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Form Pengisian Capaian Indikator Kinerja</h1>
            </div>
                <div class="card">
                    <div class="card-header bg-white pt-4 pb-3">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center w-100">
                            
                            <div class="d-flex align-items-center flex-wrap mb-3 mb-md-0">
                                
                                <div class="d-flex align-items-center bg-white shadow-sm rounded p-2 mr-3 border" style="border-left: 4px solid #4e73df !important;">
                                    <div class="p-2 mr-2 rounded-circle text-primary" style="background-color: #f0f4ff;">
                                        <i class="fa-solid fa-calendar-check fa-lg"></i>
                                    </div>
                                    <div>
                                        <small class="text-uppercase text-muted font-weight-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Tahun</small>
                                        <h6 class="mb-0 text-dark font-weight-bold">{{ optional($monitoringiku->tahunKerja)->th_tahun ?? 'N/A' }}</h6>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center bg-white shadow-sm rounded p-2 border" style="border-left: 4px solid #36b9cc !important;">
                                    <div class="p-2 mr-2 rounded-circle text-info" style="background-color: #e0faff;">
                                        <i class="fa-solid fa-university fa-lg"></i>
                                    </div>
                                    <div>
                                        <small class="text-uppercase text-muted font-weight-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Prodi</small>
                                        <h6 class="mb-0 text-dark font-weight-bold">{{ optional($monitoringiku->prodi)->nama_prodi ?? 'N/A' }}</h6>
                                    </div>
                                </div>
                                
                            </div>

                            @if(Auth::user()->role === 'admin')
                                <div style="width: 100%; max-width: 300px;"> 
                                    <form method="GET" action="{{ route('monitoringiku.create-detail', $monitoringiku->mti_id) }}">
                                        
                                        <div class="filter-group-clean shadow-sm">
                                            
                                            <div class="input-group-icon">
                                                <i class="fa-solid fa-filter"></i>
                                            </div>
                                            <select class="custom-select" name="unit_kerja" onchange="this.form.submit()">
                                                <option value="">Semua Unit Kerja...</option>
                                                @foreach($unitKerjas as $unit)
                                                    <option value="{{ $unit->unit_id }}" {{ $selectedUnit == $unit->unit_id ? 'selected' : '' }}>
                                                        {{ $unit->unit_nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if($selectedUnit)
                                                <a href="{{ route('monitoringiku.create-detail', $monitoringiku->mti_id) }}" 
                                                class="btn-reset" 
                                                title="Hapus Filter">
                                                    <i class="fa-solid fa-times"></i>
                                                </a>
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
                                                        
                                                        {{-- 1. TAMBAHKAN: Hidden input untuk mengirimkan TIPE ketercapaian ke Controller --}}
                                                        <input type="hidden" name="mtid_capaian[{{ $idx }}]" value="{{ strtolower($indikatorKinerja->ik_ketercapaian) }}">

                                                        {{-- 2. UBAH: Nama atribut input dari mtid_capaian menjadi capaian_value --}}
                                                        @if(in_array(strtolower($indikatorKinerja->ik_ketercapaian), ['nilai', 'persentase']))
                                                            <input type="number" class="form-control" style="max-width:150px"
                                                                name="capaian_value[{{ $idx }}]" {{-- UBAH DI SINI --}}
                                                                step="any"
                                                                value="{{ $capaianValue }}" 
                                                                @if($isLocked) readonly @endif>

                                                        @elseif(strtolower($indikatorKinerja->ik_ketercapaian) === 'ketersediaan')
                                                            <select name="capaian_value[{{ $idx }}]" class="form-control" style="max-width:150px" {{-- UBAH DI SINI --}}
                                                                    @if($isLocked) disabled @endif>
                                                                <option value="" {{ $capaianValue ? '' : 'selected' }}>Pilih</option>
                                                                <option value="ada"   {{ $capaianValue == 'ada' ? 'selected' : '' }}>Ada</option>
                                                                <option value="draft" {{ $capaianValue == 'draft' ? 'selected' : '' }}>Draft</option>
                                                            </select>
                                                            
                                                            @if($isLocked)
                                                                <input type="hidden" name="capaian_value[{{ $idx }}]" value="{{ $capaianValue }}">
                                                            @endif

                                                        @elseif(strtolower($indikatorKinerja->ik_ketercapaian) === 'rasio')
                                                            {{-- KHUSUS RASIO --}}
                                                            <input type="text" class="form-control" style="max-width:150px"
                                                                name="capaian_value[{{ $idx }}]" {{-- UBAH DI SINI --}}
                                                                value="{{ $capaianRaw }}" 
                                                                placeholder="Contoh: 1:20"
                                                                @if($isLocked) readonly @endif>
                                                        @else
                                                            {{-- DEFAULT LAINNYA --}}
                                                            <input type="text" class="form-control" style="max-width:100px"
                                                                name="capaian_value[{{ $idx }}]" {{-- UBAH DI SINI --}}
                                                                value="{{ $capaianValue }}"
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
            var table = $("#table-indikator").DataTable({
                "paging": true,
                "pageLength": 10, // Menampilkan 10 data per halaman agar tidak terlalu berat
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                "order": [[1, 'asc']],
                "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                "language": {
                    "search": "_INPUT_",
                    "searchPlaceholder": "Cari Indikator...",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "paginate": {
                        "previous": "<i class='fas fa-chevron-left'></i>",
                        "next": "<i class='fas fa-chevron-right'></i>",
                        "first": "<i class='fas fa-angle-double-left'></i>",
                        "last": "<i class='fas fa-angle-double-right'></i>"
                    }
                },
                "pagingType": "full_numbers", // Menampilkan angka halaman lengkap + First/Last
                "drawCallback": function() {
                    // Merapikan style tombol pagination agar serasi dengan Bootstrap
                    $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
                }
            });
        });

        $('form').on('submit', function(e) {
            var form = this;

            // Ambil semua data input dari seluruh halaman tabel (termasuk yang tidak terlihat)
            var params = table.$('input,select,textarea').serializeArray();

            // Tambahkan input tersembunyi tersebut ke dalam form sebelum submit
            $.each(params, function() {
                if (!$.contains(document, form[this.name])) {
                    $(form).append(
                        $('<input>')
                            .attr('type', 'hidden')
                            .attr('name', this.name)
                            .val(this.value)
                    );
                }
            });
        });
    </script>
@endpush