@extends('layouts.app')

@section('title', 'edit-renstra')

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
                <h1>Edit Renstra</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item">Form Renstra</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-lg-6">
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

                                <form method="POST" action="{{ route('renstra.update', $renstra) }}">
                                    @csrf
                                    @method('put')
                                    <div class="form-group">
                                        <label>Nama Renstra</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-user"></i>
                                                </div>
                                            </div>
                                            <input class="form-control" type="text" name="ren_nama" value="{{ old('ren_nama', $renstra->ren_nama) }}" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Pimpinan Renstra</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-user-tie"></i>
                                                </div>
                                            </div>
                                            <input class="form-control" type="text" name="ren_pimpinan" value="{{ old('ren_pimpinan', $renstra->ren_pimpinan) }}" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Periode Awal</label>
                                        <input class="form-control" type="number" name="ren_periode_awal" value="{{ old('ren_periode_awal', $renstra->ren_periode_awal) }}" min="1900" max="{{ date('Y') }}" />
                                    </div>

                                    <div class="form-group">
                                        <label>Periode Akhir</label>
                                        <input class="form-control" type="number" name="ren_periode_akhir" value="{{ old('ren_periode_akhir', $renstra->ren_periode_akhir) }}" min="1900" max="{{ date('Y') }}" />
                                    </div>

                                    <div class="form-group">
                                        <label>Status Aktif</label>
                                        <select class="form-select" name="ren_is_aktif">
                                            <option value="y" {{ old('ren_is_aktif', $renstra->ren_is_aktif) == 'y' ? 'selected' : '' }}>Aktif</option>
                                            <option value="n" {{ old('ren_is_aktif', $renstra->ren_is_aktif) == 'n' ? 'selected' : '' }}>Non-Aktif</option>
                                        </select>
                                    </div>

                                    <!-- Optional unit_kerja if you need to edit it -->
                                    

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <a href="{{ route('renstra.index') }}" class="btn btn-danger">Kembali</a>
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

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/forms-advanced-forms.js') }}"></script>
@endpush
