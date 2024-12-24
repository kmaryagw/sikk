@extends('layouts.app')

@section('title', 'Edit Renstra')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/bootstrap-daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit Renstra</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('renstra.index') }}">Daftar Renstra</a></div>
                    <div class="breadcrumb-item active">Edit Renstra</div>
                </div>
            </div>

            <div class="section-body">
                <div class="card">
                    <div class="card-header">
                        <h4>Form Edit Renstra</h4>
                    </div>
                    <div class="card-body">
                        <!-- Menampilkan Error -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Form -->
                        <form method="POST" action="{{ route('renstra.update', $renstra) }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <!-- Kolom Kiri -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ren_nama">Nama Renstra</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-file-alt"></i>
                                                </div>
                                            </div>
                                            <input type="text" id="ren_nama" name="ren_nama" 
                                                class="form-control" 
                                                value="{{ old('ren_nama', $renstra->ren_nama) }}" 
                                                placeholder="Masukkan nama Renstra" 
                                                required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="ren_pimpinan">Pimpinan Renstra</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-user-tie"></i>
                                                </div>
                                            </div>
                                            <input type="text" id="ren_pimpinan" name="ren_pimpinan" 
                                                class="form-control" 
                                                value="{{ old('ren_pimpinan', $renstra->ren_pimpinan) }}" 
                                                placeholder="Masukkan nama pimpinan" 
                                                required>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kolom Kanan -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ren_periode_awal">Periode Awal</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </div>
                                            </div>
                                            <input type="number" id="ren_periode_awal" name="ren_periode_awal" 
                                                class="form-control" 
                                                value="{{ old('ren_periode_awal', $renstra->ren_periode_awal) }}" 
                                                placeholder="Masukkan tahun awal" 
                                                required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="ren_periode_akhir">Periode Akhir</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-calendar-check"></i>
                                                </div>
                                            </div>
                                            <input type="number" id="ren_periode_akhir" name="ren_periode_akhir" 
                                                class="form-control" 
                                                value="{{ old('ren_periode_akhir', $renstra->ren_periode_akhir) }}" 
                                                placeholder="Masukkan tahun akhir" 
                                                required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status dan Tombol -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ren_is_aktif">Status Aktif</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-toggle-on"></i>
                                                </div>
                                            </div>
                                            <select id="ren_is_aktif" name="ren_is_aktif" class="form-control">
                                                @foreach ($ren_is_aktifs as $status)
                                                    <option value="{{ $status }}" 
                                                        {{ old('ren_is_aktif', $renstra->ren_is_aktif) == $status ? 'selected' : '' }}>
                                                        {{ $status }}
                                                    </option>
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
                                <a href="{{ route('renstra.index') }}" class="btn btn-danger">
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

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/forms-advanced-forms.js') }}"></script>
@endpush
