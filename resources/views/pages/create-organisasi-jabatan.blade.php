@extends('layouts.app')

@section('title', 'create-organisasi-jabatan')

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
                <h1>Form Organisasi Jabatan</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="col-6 col-lg-6">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $err)
                                                <li>{{ $err }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('organisasijabatan.store') }}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="oj_nama">Jabatan</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-user-tie"></i>
                                                </div>
                                            </div>
                                            <input class="form-control" type="text" name="oj_nama" id="oj_nama" value="{{ old('oj_nama') }}"/>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="oj_kode">Kode</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-code"></i>
                                                </div>
                                            </div>
                                            <input class="form-control" type="text" name="oj_kode" id="oj_kode" value="{{ old('oj_kode') }}"/>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="oj_mengeluarkan_nomor">Mengeluarkan Nomor</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-check-square"></i>
                                                </div>
                                            </div>
                                            <select class="form-control" id="oj_mengeluarkan_nomor" name="oj_mengeluarkan_nomor">
                                                @foreach ($nomors as $nomor)
                                                    <option value="{{ $nomor }}" {{ old('oj_mengeluarkan_nomor', 'y') == $nomor ? 'selected' : '' }}>
                                                        {{ $nomor == 'y' ? 'Ya' : 'Tidak' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="oj_induk">Induk</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-folder-tree"></i>
                                                </div>
                                            </div>
                                            <select class="form-control" name="oj_induk" id="oj_induk">
                                                <option value="">- Tidak Ada -</option>
                                                @foreach ($organisasis as $organisasi)
                                                    <option value="{{ $organisasi->oj_id }}" {{ old('oj_id') == $organisasi->oj_id ? 'selected' : '' }}>
                                                        {{ $organisasi->oj_nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="oj_status">Status Aktif</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-toggle-on"></i>
                                                </div>
                                            </div>
                                            <select class="form-control" id="oj_status" name="oj_status">
                                                @foreach ($statuses as $status)
                                                    <option value="{{ $status }}" {{ old('oj_status', 'y') == $status ? 'selected' : '' }}>
                                                        {{ $status == 'y' ? 'Ya' : 'Tidak' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <a href="{{ url('organisasijabatan') }}" class="btn btn-danger">Kembali</a>
                                    </div>
                                </form>
                                </div>
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