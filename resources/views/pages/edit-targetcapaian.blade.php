@extends('layouts.app')

@section('title', 'Edit Target Capaian')

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
                <h1>Form Edit Target Capaian</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item">Form Edit Target Capaian</div>
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

                                <form method="POST" action="{{ route('targetcapaian.update', $targetcapaian) }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="row">
                                        <!-- Kolom Kiri -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Indikator Kinerja</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa-solid fa-bullseye"></i>
                                                        </div>
                                                    </div>
                                                    <select class="form-control" name="ik_id" required>
                                                        <option value="" disabled>Pilih Indikator Kinerja</option>
                                                        @foreach ($indikatorkinerjautamas as $indikatorkinerjautama)
                                                            <option value="{{ $indikatorkinerjautama->ik_id }}" 
                                                                {{ $indikatorkinerjautama->ik_id == $targetcapaian->ik_id ? 'selected' : '' }}>
                                                                {{ $indikatorkinerjautama->ik_nama }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label>Target Capaian</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa-solid fa-award"></i>
                                                        </div>
                                                    </div>
                                                    <input type="text" name="ti_target" class="form-control" value="{{ old('ti_target', $targetcapaian->ti_target) }}" required>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label>Prodi</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa-solid fa-building-columns"></i>
                                                        </div>
                                                    </div>
                                                    <select class="form-control" name="prodi_id">
                                                        <option value="" disabled>Pilih Prodi</option>
                                                        @foreach ($prodis as $prodi)
                                                            <option value="{{ $prodi->prodi_id }}" 
                                                                {{ $prodi->prodi_id == $targetcapaian->prodi_id ? 'selected' : '' }}>
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
                                                <label>Keterangan</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa-solid fa-clipboard-list"></i>
                                                        </div>
                                                    </div>
                                                    <textarea class="form-control" name="ti_keterangan" required>{{ old('ti_keterangan', $targetcapaian->ti_keterangan) }}</textarea>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label>Tahun</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa-solid fa-calendar-alt"></i>
                                                        </div>
                                                    </div>
                                                    <select class="form-control" name="th_id" required>
                                                        <option value="" disabled>Pilih Tahun</option>
                                                        @foreach ($tahuns as $tahun)
                                                            <option value="{{ $tahun->th_id }}" 
                                                                {{ $tahun->th_id == $targetcapaian->th_id ? 'selected' : '' }}>
                                                                {{ $tahun->th_tahun }}
                                                            </option>
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
@endpush