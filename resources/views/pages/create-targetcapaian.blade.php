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
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Form Target Capaian</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item">Form Target Capaian</div>
                </div>
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

                                <form method="POST" action="{{ route('targetcapaian.store') }}">
                                    @csrf
                                    <div class="row">
                                        <!-- Kolom Kiri -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="ik_id">Indikator Kinerja</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa-solid fa-bullseye"></i>
                                                        </div>
                                                    </div>
                                                    <select class="form-control" name="ik_id" id="ik_id" required>
                                                        <option value="" disabled selected>Pilih Indikator Kinerja</option>
                                                        @foreach ($indikatorkinerjas as $indikatorkinerja)
                                                            <option value="{{ $indikatorkinerja->ik_id }}" 
                                                                data-jenis="{{ $indikatorkinerja->ik_ketercapaian }}">
                                                                {{ $indikatorkinerja->ik_kode }} - {{ $indikatorkinerja->ik_nama }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="ti_target">Target Capaian</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa-solid fa-award"></i>
                                                        </div>
                                                    </div>
                                                    <input type="text" id="ti_target" name="ti_target" class="form-control" placeholder="Isi Target Capaian" required>
                                                </div>
                                                <small id="ti_target_hint" class="form-text text-muted">Isi sesuai dengan jenis ketercapaian.</small>
                                            </div>

                                            <div class="form-group">
                                                <label for="prodi_id">Prodi</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa-solid fa-building-columns"></i>
                                                        </div>
                                                    </div>
                                                    <select class="form-control" name="prodi_id" id="prodi_id">
                                                        <option value="" disabled selected>Pilih Prodi</option>
                                                        @foreach ($prodis as $prodi)
                                                            <option value="{{ $prodi->prodi_id }}" {{ old('prodi_id') == $prodi->prodi_id ? 'selected' : '' }}>
                                                                {{ $prodi->nama_prodi }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Kolom Kanan -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="ti_keterangan">Keterangan</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa-solid fa-clipboard-list"></i>
                                                        </div>
                                                    </div>
                                                    <textarea class="form-control" name="ti_keterangan" id="ti_keterangan" required>{{ old('ti_keterangan') }}</textarea>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="th_id">Tahun</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa-solid fa-calendar-alt"></i>
                                                        </div>
                                                    </div>
                                                    <select class="form-control" name="th_id" id="th_id" required>
                                                        <option value="" disabled selected>Pilih Tahun</option>
                                                        @foreach ($tahuns as $tahun)
                                                            <option value="{{ $tahun->th_id }}">{{ $tahun->th_tahun }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-footer text-right">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <a href="{{ url('targetcapaian') }}" class="btn btn-danger">Kembali</a>
                                    </div>
                                </form>
                            </div>
                        </div>
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
    </script>
@endpush
