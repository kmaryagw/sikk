@extends('layouts.app')

@section('title', 'Create Indikator Kinerja Utama')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/bootstrap-daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Form Indikator Kinerja Utama</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item active">Create Indikator Kinerja Utama</div>
                </div>
            </div>

            <div class="section-body">
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

                        <form method="POST" action="{{ route('indikatorkinerja.store') }}">
                            @csrf
                            <div class="row">
                                <!-- Kolom Kiri -->
                                <div class="col-md-6">
                                    <!-- Kode Indikator Kinerja -->
                                    <div class="form-group">
                                        <label for="ik_kode">Kode Indikator Kinerja</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-code"></i>
                                                </div>
                                            </div>
                                            <input type="text" id="ik_kode" name="ik_kode" 
                                                class="form-control @error('ik_kode') is-invalid @enderror" 
                                                value="{{ old('ik_kode') }}" placeholder="Masukkan kode indikator" required>
                                            @error('ik_kode')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Nama Indikator Kinerja -->
                                    <div class="form-group">
                                        <label for="ik_nama">Nama Indikator Kinerja</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            </div>
                                            <input type="text" id="ik_nama" name="ik_nama" 
                                                class="form-control @error('ik_nama') is-invalid @enderror" 
                                                value="{{ old('ik_nama') }}" placeholder="Masukkan nama indikator" required>
                                            @error('ik_nama')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Standar -->
                                    <div class="form-group">
                                        <label for="std_id">Standar</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-check-circle"></i>
                                                </div>
                                            </div>
                                            <select id="std_id" name="std_id" 
                                                class="form-control @error('std_id') is-invalid @enderror" required>
                                                <option value="" disabled selected>Pilih Standar</option>
                                                @foreach ($standar as $s)
                                                    <option value="{{ $s->std_id }}" {{ old('std_id') == $s->std_id ? 'selected' : '' }}>{{ $s->std_nama }}</option>
                                                @endforeach
                                            </select>
                                            @error('std_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Kolom Kanan -->
                                <div class="col-md-6">
                                    <!-- Tahun -->
                                    <div class="form-group">
                                        <label for="th_id">Tahun</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </div>
                                            </div>
                                            <select id="th_id" name="th_id" class="form-control" required>
                                                <option value="" disabled selected>Pilih Tahun</option>
                                                @foreach ($tahunKerja as $tahun)
                                                    <option value="{{ $tahun->th_id }}" {{ old('th_id') == $tahun->th_id ? 'selected' : '' }}>{{ $tahun->th_tahun }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="ik_jenis">Jenis Indikator Kinerja</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-bullseye"></i>
                                                </div>
                                            </div>
                                            <select id="ik_jenis" name="ik_jenis" 
                                                class="form-control" required>
                                                <option value="" disabled selected>Pilih Jenis</option>
                                                @foreach ($jeniss as $jenis)
                                                    <option value="{{ $jenis }}" {{ old('ik_jenis') == $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Pengukur Ketercapaian -->
                                    <div class="form-group">
                                        <label for="ik_ketercapaian">Pengukur Ketercapaian</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-chart-line"></i>
                                                </div>
                                            </div>
                                            <select id="ik_ketercapaian" name="ik_ketercapaian" 
                                                class="form-control" required>
                                                <option value="" disabled selected>Pilih Ketercapaian</option>
                                                @foreach ($ketercapaians as $ketercapaian)
                                                    <option value="{{ $ketercapaian }}" {{ old('ik_ketercapaian') == $ketercapaian ? 'selected' : '' }}>{{ $ketercapaian }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Aksi -->
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                                <a href="{{ url('indikatorkinerja') }}" class="btn btn-danger">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
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

    @include('sweetalert::alert')

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/forms-advanced-forms.js') }}"></script>
@endpush
