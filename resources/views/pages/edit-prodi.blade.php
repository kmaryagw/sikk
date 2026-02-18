@extends('layouts.app')

@section('title','SPMI')

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
                <h1>Edit Prodi</h1>
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

                                <form action="{{ route('prodi.update', $prodi->prodi_id) }}" method="POST">
                                    @csrf
                                    @method('put')
                                    <div class="form-group">
                                        <label>Nama Prodi</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-building-columns"></i>
                                                </div>
                                            </div>
                                            <input class="form-control" type="text" name="nama_prodi" value="{{ old('nama_prodi', $prodi->nama_prodi) }}"/>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Singkatan</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-ellipsis-h"></i>
                                                </div>
                                            </div>
                                            <input class="form-control" type="text" name="singkatan_prodi" value="{{ old('singkatan_prodi', $prodi->singkatan_prodi) }}"/>
                                        </div>
                                    </div>

                                    <!-- Dropdown Fakultas -->
                                    <div class="form-group">
                                        <label for="id_fakultas">Fakultas</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-school"></i>
                                                </div>
                                            </div>
                                            <select class="form-control" name="id_fakultas" id="id_fakultas">
                                                <option value="">Pilih Fakultas</option>
                                                @foreach ($fakultas as $fakultasItem)
                                                    <option value="{{ $fakultasItem->id_fakultas }}" 
                                                        {{ old('id_falkutas', $prodi->id_fakultas) == $fakultasItem->id_fakultas ? 'selected' : '' }}>
                                                        {{ $fakultasItem->nama_fakultas }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="unit_id_pengelola">Unit Dekanat</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-briefcase"></i>
                                                </div>
                                            </div>
                                            <select class="form-control" name="unit_id_pengelola" id="unit_id_pengelola">
                                                <option value="">Semua Unit</option>
                                                @foreach ($unitKerjas as $unit)
                                                    <option value="{{ $unit->unit_id }}" 
                                                        {{ old('unit_id_pengelola', $prodi->unit_id_pengelola) == $unit->unit_id ? 'selected' : '' }}>
                                                        {{ $unit->unit_nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <a href="{{ url('prodi') }}" class="btn btn-danger">Kembali</a>
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

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/forms-advanced-forms.js') }}"></script>
@endpush
