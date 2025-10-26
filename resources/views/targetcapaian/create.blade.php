@extends('layouts.app')

@section('title', 'Create Target Capaian')

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
            max-height: 50rem;   /* tinggi maksimum tabel */
            overflow-y: auto;    /* aktifkan scroll vertikal */
        }

        .table thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #f8f9fa !important; /* biar solid, tidak transparan */
        }
    </style>

@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Form Target Capaian</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $err)
                                                <li>{{ $err }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="form-group d-flex align-items-center">
                                    <label for="th_id" class="mr-2" style="font-size: 1rem;">Tahun Aktif:</label>
                                        <span class="badge badge-primary p-3" style="font-size: 1rem;">
                                            <i class="fa-solid fa-calendar-alt"></i> {{ $tahuns->th_tahun }}
                                        </span>
                                </div>
                                <div class="form-group d-flex align-items-center">
                                    <label for="th_id" class="mr-2" style="font-size: 1rem;">Prodi:</label>
                                    @if ($userRole === 'prodi' && $userProdi)
                                        <!-- Jika user adalah prodi, tampilkan readonly input -->
                                        <span class="badge badge-primary p-3" style="font-size: 1rem;">
                                            <i class="fa-solid fa-calendar-alt"></i> {{ $userProdi->nama_prodi }}
                                        </span>
                                    @endif
                                </div>

                                </form>
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
                                                    <th>Keterangan</th>
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

                                                        $target_value = $found ? $found['ti_target'] : '';
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
                                                            @php
                                                                // Ambil baseline dari data tahun ini kalau ada
                                                                if ($found && $found['baseline'] !== null && $found['baseline'] !== '') {
                                                                    $baseline_value = old("indikator.$no.baseline", $found['baseline']);
                                                                }
                                                                // Kalau baseline kosong, coba isi dari tahun sebelumnya
                                                                elseif (isset($baseline_from_prev) && isset($baseline_from_prev[$ik->ik_id])) {
                                                                    $baseline_value = old("indikator.$no.baseline", $baseline_from_prev[$ik->ik_id]);
                                                                }
                                                                // Kalau dua-duanya tidak ada, biarkan kosong
                                                                else {
                                                                    $baseline_value = old("indikator.$no.baseline", '');
                                                                }
                                                            @endphp

                                                            @if($ik->ik_ketercapaian == 'nilai' || $ik->ik_ketercapaian == 'persentase')
                                                                <input type="number" 
                                                                    class="form-control" 
                                                                    name="indikator[{{ $no }}][baseline]" 
                                                                    step="any" 
                                                                    value="{{ $baseline_value }}">
                                                            @elseif($ik->ik_ketercapaian == 'ketersediaan')
                                                                <select class="form-control" name="indikator[{{ $no }}][baseline]">
                                                                    <option value="" disabled {{ $baseline_value == '' ? 'selected' : '' }}>-- Pilih --</option>
                                                                    <option value="ada" {{ $baseline_value == 'ada' ? 'selected' : '' }}>Ada</option>
                                                                    <option value="draft" {{ $baseline_value == 'draft' ? 'selected' : '' }}>Draft</option>
                                                                </select>
                                                            @elseif($ik->ik_ketercapaian == 'rasio')
                                                                <input type="text" 
                                                                    class="form-control" 
                                                                    name="indikator[{{ $no }}][baseline]" 
                                                                    pattern="^\d+:\d+$" 
                                                                    placeholder="Contoh: 1:20" 
                                                                    value="{{ $baseline_value }}">
                                                            @else
                                                                <input type="text" 
                                                                    class="form-control" 
                                                                    name="indikator[{{ $no }}][baseline]" 
                                                                    value="{{ $baseline_value }}">
                                                            @endif

                                                            <input type="hidden" 
                                                                name="indikator[{{ $no }}][ik_id]" 
                                                                value="{{ $ik->ik_id }}">
                                                        </td>     
                                                        <td>
                                                            @if($ik->ik_ketercapaian == 'nilai' || $ik->ik_ketercapaian == 'persentase')
                                                                <input type="number" class="form-control" name="indikator[{{ $no }}][target]" step="any" value="{{ $target_value }}">
                                                            @elseif($ik->ik_ketercapaian == 'ketersediaan')
                                                                <select class="form-control" name="indikator[{{ $no }}][target]">
                                                                    <option value="" disabled selected {{ $target_value == '' ? 'selected' : '' }}>-- Pilih --</option>
                                                                    <option value="ada" {{ $target_value == 'ada' ? 'selected' : '' }}>Ada</option>
                                                                    <option value="draft" {{ $target_value == 'draft' ? 'selected' : '' }}>Draft</option>
                                                                </select>
                                                            @else
                                                                <input type="text" class="form-control" name="indikator[{{ $no }}][target]" value="{{ $target_value }}">
                                                            @endif
                                                            <input type="hidden" class="form-control" name="indikator[{{ $no }}][ik_id]" value="{{ $ik->ik_id }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name="indikator[{{ $no }}][keterangan]" value="{{ $target_keterangan }}">
                                                        </td>
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

        $("#table-indikator").dataTable({
            "columnDefs": [
                { "sortable": false, "targets": [3,4,5,6] }
            ],
            paging: false
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
