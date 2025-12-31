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

                                {{-- <div class="alert alert-light border-left-primary shadow-sm mb-4" role="alert">
                                    <div class="d-flex align-items-start">
                                        <div class="alert-icon text-primary mt-1 mr-3">
                                            <i class="far fa-lightbulb fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="alert-heading font-weight-bold text-primary mb-1">Catatan Pengisian</h6>
                                            <p class="mb-0 text-muted small">
                                                Jika kolom <b>Nilai Baseline</b> atau <b>Target</b> dikosongkan, sistem akan otomatis menyimpannya sebagai nilai <b>0 (Nol)</b> atau <b>0:0</b> (untuk Rasio).
                                                <br>
                                                <i class="fa-solid fa-info-circle mr-1"></i> <em>Kecuali untuk indikator jenis <b>Ketersediaan</b>, otomatis terisi 'Draft'.</em>
                                            </p>
                                        </div>
                                    </div>
                                </div> --}}
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
                                    {{-- <div class="form-footer text-right">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <a href="{{ url('targetcapaianprodi') }}" class="btn btn-danger">Kembali</a>
                                    </div> --}}
                                    <br/>

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
                                                    {{-- <th>Keterangan</th> --}}
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php 
                                                    $no = 1; 
                                                    $data_tersimpan = collect($targetindikators);
                                                @endphp
                                                @foreach ($indikatorkinerjas as $ik)
                                                    @php
                                                        $found = $data_tersimpan->where('th_id', $tahuns->th_id)
                                                                ->where('ik_id', $ik->ik_id)
                                                                ->where('prodi_id', Auth::user()->prodi_id)
                                                                ->first();

                                                        $db_baseline = $found ? $found['baseline'] : null;
                                                        
                                                        // Prioritas 1: Ambil dari DB (izinkan angka 0 karena pakai !== '')
                                                        if ($db_baseline !== null && $db_baseline !== '') {
                                                            $raw_baseline = $db_baseline;
                                                        } 
                                                        // Prioritas 2: Ambil dari tahun lalu
                                                        elseif (isset($baseline_from_prev[$ik->ik_id])) {
                                                            $raw_baseline = $baseline_from_prev[$ik->ik_id];
                                                        } 
                                                        // Prioritas 3: Default Kosong
                                                        else {
                                                            $raw_baseline = '';
                                                        }

                                                        if ($raw_baseline === '') {
                                                            if ($ik->ik_ketercapaian == 'nilai' || $ik->ik_ketercapaian == 'persentase') {
                                                                $raw_baseline = '0';
                                                            }
                                                            elseif ($ik->ik_ketercapaian == 'ketersediaan') {
                                                                $raw_baseline = 'draft';
                                                            }
                                                            elseif ($ik->ik_ketercapaian == 'rasio') {
                                                                $raw_baseline = '0:0';
                                                            }
                                                        }

                                                        $baseline_value = old("indikator.$no.baseline", $raw_baseline);

                                                        $db_target = ($found && isset($found['ti_target'])) ? $found['ti_target'] : null;

                                                        if ($db_target !== null && $db_target !== '') {
                                                            $raw_target = $db_target;
                                                        } else {
                                                            $raw_target = '';
                                                        }

                                                        if ($raw_target === '') {
                                                            if ($ik->ik_ketercapaian == 'nilai' || $ik->ik_ketercapaian == 'persentase') {
                                                                $raw_target = '0';
                                                            }
                                                            elseif ($ik->ik_ketercapaian == 'ketersediaan') {
                                                                $raw_target = 'Ada';
                                                            }
                                                            elseif ($ik->ik_ketercapaian == 'rasio') {
                                                                $raw_target = '0:0';
                                                            }
                                                        }

                                                        $target_value = old("indikator.$no.target", $raw_target);
                                                        $target_keterangan = $found ? $found['ti_keterangan'] : '';
                                                    @endphp

                                                    <tr>
                                                        <td>{{ $no }}</td>
                                                        <td>{{ $ik->ik_kode }} - {{ $ik->ik_nama }}</td>
                                                        <td>
                                                            @if (strtolower($ik->ik_jenis == 'IKU'))
                                                                <span class="badge badge-success">IKU</span>
                                                            @elseif (strtolower($ik->ik_jenis == 'IKT'))
                                                                <span class="badge badge-primary">IKT</span>
                                                            @elseif (strtolower($ik->ik_jenis == 'IKU/IKT'))
                                                                <span class="badge badge-danger">IKU/IKT</span>
                                                            @else
                                                                <span class="badge badge-secondary">Tidak Diketahui</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="text-primary"> {{ $ik->ik_ketercapaian }}</span>
                                                        </td>
                                                        
                                                        <td class="text-center">
                                                            @if($ik->ik_ketercapaian == 'nilai' || $ik->ik_ketercapaian == 'persentase')
                                                                <input type="number" 
                                                                    class="form-control @error('indikator.' . $no . '.baseline') is-invalid @enderror" 
                                                                    name="indikator[{{ $no }}][baseline]" 
                                                                    step="any" 
                                                                    value="{{ $baseline_value }}">

                                                                @error('indikator.' . $no . '.baseline')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror

                                                            @elseif($ik->ik_ketercapaian == 'ketersediaan')
                                                                <select class="form-control @error('indikator.' . $no . '.baseline') is-invalid @enderror" name="indikator[{{ $no }}][baseline]">
                                                                    <option value="" disabled {{ $baseline_value == '' ? 'selected' : '' }}>-- Pilih --</option>
                                                                    <option value="ada" {{ strtolower($baseline_value) == 'ada' ? 'selected' : '' }}>Ada</option>
                                                                    <option value="draft" {{ strtolower($baseline_value) == 'draft' ? 'selected' : '' }}>Draft</option>
                                                                </select>

                                                                @error('indikator.' . $no . '.baseline')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror

                                                            @elseif($ik->ik_ketercapaian == 'rasio')
                                                                <input type="text" 
                                                                    class="form-control @error('indikator.' . $no . '.baseline') is-invalid @enderror" 
                                                                    name="indikator[{{ $no }}][baseline]" 
                                                                    pattern="^\d+\s*:\s*\d+$"
                                                                    placeholder="Contoh: 0:0" 
                                                                    value="{{ $baseline_value }}">
                                                                @error('indikator.' . $no . '.baseline')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror

                                                            @else
                                                                <input type="text" 
                                                                    class="form-control @error('indikator.' . $no . '.baseline') is-invalid @enderror" 
                                                                    name="indikator[{{ $no }}][baseline]" 
                                                                    value="{{ $baseline_value }}">
                                                            @endif
                                                            <input type="hidden" name="indikator[{{ $no }}][ik_id]" value="{{ $ik->ik_id }}">
                                                        </td>     

                                                        <td>
                                                            @if($ik->ik_ketercapaian == 'nilai' || $ik->ik_ketercapaian == 'persentase')
                                                                <input type="number" 
                                                                    class="form-control @error('indikator.' . $no . '.target') is-invalid @enderror" 
                                                                    name="indikator[{{ $no }}][target]" 
                                                                    step="any" 
                                                                    value="{{ $target_value }}">

                                                                @error('indikator.' . $no . '.target')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror

                                                            @elseif($ik->ik_ketercapaian == 'ketersediaan')
                                                                <select class="form-control @error('indikator.' . $no . '.target') is-invalid @enderror" name="indikator[{{ $no }}][target]">
                                                                    <option value="" disabled {{ $target_value == '' ? 'selected' : '' }}>-- Pilih --</option>
                                                                    <option value="ada" {{ strtolower($target_value) == 'ada' ? 'selected' : '' }}>Ada</option>
                                                                    <option value="draft" {{ strtolower($target_value) == 'draft' ? 'selected' : '' }}>Draft</option>
                                                                </select>
                                                                @error('indikator.' . $no . '.target')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror

                                                            @elseif($ik->ik_ketercapaian == 'rasio')
                                                                <input type="text" 
                                                                    class="form-control @error('indikator.' . $no . '.target') is-invalid @enderror" 
                                                                    name="indikator[{{ $no }}][target]"
                                                                    pattern="^(0|\d+\s*:\s*\d+)$"
                                                                    placeholder="Contoh: 0:0" 
                                                                    value="{{ $target_value }}">
                                                                @error('indikator.' . $no . '.target')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            @else
                                                                <input type="text" 
                                                                    class="form-control @error('indikator.' . $no . '.target') is-invalid @enderror" 
                                                                    name="indikator[{{ $no }}][target]" 
                                                                    value="{{ $target_value }}">
                                                                @error('indikator.' . $no . '.target')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            @endif
                                                        </td>

                                                        {{-- <td>
                                                            <input type="text" class="form-control" name="indikator[{{ $no }}][keterangan]" value="{{ $target_keterangan }}">
                                                        </td> --}}
                                                        @php $no++; @endphp
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="form-footer text-right">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <a href="{{ url('targetcapaianprodi') }}" class="btn btn-danger">Kembali</a>
                                    </div>
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
    <script src="{{ asset('library/cleave.js/dist/cleave.min.js') }}"></script>
    <script src="{{ asset('library/cleave.js/dist/addons/cleave-phone.us.js') }}"></script>
    <script src="{{ asset('library/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('library/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}"></script>
    <script src="{{ asset('library/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ asset('library/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('library/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>

    <script src="{{ asset('library/datatables/media/js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('library/datatables/media/js/jquery.dataTables.min.js') }}"></script>

    @include('sweetalert::alert')

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/forms-advanced-forms.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ikSelect = document.getElementById("ik_id");
            const tiTargetInput = document.getElementById("ti_target");
            const tiTargetHint = document.getElementById("ti_target_hint");

            ikSelect.addEventListener("change", function () {
                const selectedOption = this.options[this.selectedIndex];
                const jenis = selectedOption.getAttribute("data-jenis");

                if (jenis === "nilai") {
                    tiTargetInput.placeholder = "Indikator ini menggunakan ketercapaian nilai";
                    tiTargetHint.textContent = "Isi nilai ketercapaian seperti 1.2 atau 1.3.";
                } else if (jenis === "persentase") {
                    tiTargetInput.placeholder = "Indikator ini menggunakan ketercapaian persentase";
                    tiTargetHint.textContent = "Isi angka dalam rentang 0 hingga 100.";
                } else if (jenis === "ketersediaan") {
                    tiTargetInput.placeholder = "Indikator ini menggunakan ketercapaian ketersediaan";
                    tiTargetHint.textContent = "Isi dengan 'Ada' atau 'Draft'.";
                } else {
                    tiTargetInput.placeholder = "Isi Target Capaian";
                    tiTargetHint.textContent = "Isi sesuai dengan jenis ketercapaian.";
                }
            });
        });

        $(document).ready(function () {
            $("#table-indikator").DataTable({
                "columnDefs": [
                    { "sortable": false, "targets": [4, 5, 6] }
                ],
                "paging": false, 
                "searching": true, 
                "order": [[1, 'asc']],
                "info": true,
                "dom": '<"d-flex justify-content-between align-items-center mb-3"f>rt<"bottom"i><"clear">',
                "language": {
                    "search": "Cari :", 
                    // "searchPlaceholder": "..."
                },
                "infoCallback": function(settings, start, end, max, total, pre) {
                    return `
                        <span class="badge bg-primary text-light px-3 py-2 m-3">
                            Total Data : ${total}
                        </span>
                    `;
                }
            });
        });
    </script>

    {{-- <script>
        document.getElementById('ik_id').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var baseline = selectedOption.getAttribute('data-baseline');
            document.getElementById('ik_baseline').value = baseline;
        });
    </script> --}}
    <script>
        // Kosongkan baseline saat halaman dimuat
        //document.getElementById('baseline').value = 'Pilih Indikator Kinerja Terlebih Dahulu';
    
        // Ketika ada perubahan pada pilihan indikator kinerja
        // document.getElementById('ik_id').addEventListener('change', function() {
        //     var selectedOption = this.options[this.selectedIndex];
        //     var baseline = selectedOption.getAttribute('data-baseline');
        //     document.getElementById('baseline').value = baseline;
        // });
    </script>
@endpush
