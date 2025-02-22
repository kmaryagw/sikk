@extends('layouts.app')

@section('title', 'edit-nomor-surat')

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
                <h1>Edit Nomor Surat</h1>
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

                                <form method="POST" action="{{ route('nomorsurat.update', $nomorSurat->sn_id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="oj_id">Organisasi Jabatan</label>
                                                <select class="form-control select2" name="oj_id" id="oj_id" required>
                                                    <option value="">Pilih Organisasi Jabatan</option>
                                                    @foreach ($organisasiJabatans as $oj)
                                                        <option value="{{ $oj->oj_id }}" {{ $nomorSurat->oj_id == $oj->oj_id ? 'selected' : '' }}>
                                                            {{ $oj->oj_nama }} ({{ $oj->parent->oj_nama ?? '-' }}, {{ $oj->parent->parent->oj_nama ?? '-' }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="skl_id">Klasifikasi Lingkup</label>
                                                <select class="form-control select2" name="skl_id" id="skl_id" required>
                                                    <option value="">Pilih Klasifikasi Lingkup</option>
                                                    @foreach ($lingkups as $lingkup)
                                                        <option value="{{ $lingkup->skl_id }}" {{ $nomorSurat->skl_id == $lingkup->skl_id ? 'selected' : '' }}>
                                                            {{ $lingkup->skl_nama }} ({{ $lingkup->perihal->skp_nama ?? '' }}, {{ $lingkup->perihal->fungsi->skf_nama ?? '' }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="sn_tanggal">Tanggal</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa-solid fa-calendar-day"></i>
                                                        </div>
                                                    </div>
                                                <input type="date" name="sn_tanggal" id="sn_tanggal" class="form-control" value="{{ $nomorSurat->sn_tanggal }}">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="sn_perihal">Perihal</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa-solid fa-tag"></i>
                                                        </div>
                                                    </div>
                                                <textarea class="form-control" name="sn_perihal" id="sn_perihal">{{ $nomorSurat->sn_perihal }}</textarea>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="sn_keterangan">Keterangan</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa-solid fa-clipboard-list"></i>
                                                        </div>
                                                    </div>
                                                <textarea class="form-control" name="sn_keterangan" id="sn_keterangan">{{ $nomorSurat->sn_keterangan }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-footer text-left mt-5">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <a href="{{ route('nomorsurat.index') }}" class="btn btn-danger">Kembali</a>
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
    <script src="{{ asset('library/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('library/cleave.js/dist/cleave.min.js') }}"></script>
    <script src="{{ asset('library/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('library/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}"></script>
    <script src="{{ asset('library/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ asset('library/select2/dist/js/select2.full.min.js') }}"></script>

    @include('sweetalert::alert')
    
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endpush
