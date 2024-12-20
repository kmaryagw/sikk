@extends('layouts.app')
@section('title', 'Edit Detail Evaluasi')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Edit Detail Evaluasi</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item">Edit Detail Evaluasi</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
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

                            <!-- Form -->
                            <form action="{{ route('evaluasi.update-detail', $evaluasiDetail->evald_id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="ti_id">Target Indikator</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fa-solid fa-award"></i>
                                                    </div>
                                                </div>
                                                <select class="form-control" name="ti_id" disabled>
                                                    @foreach ($targetIndikators as $targetIndikator)
                                                        <option value="{{ $targetIndikator->ti_id }}" 
                                                            @if ($targetIndikator->ti_id == $evaluasiDetail->ti_id) selected @endif>
                                                            {{ $targetIndikator->indikatorKinerja->ik_nama }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="ti_id" value="{{ $evaluasiDetail->ti_id }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="evald_target">Target</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fa-solid fa-bullseye"></i>
                                                    </span>
                                                </div>
                                                <input type="text" id="evald_target" class="form-control" name="evald_target" value="{{ $evaluasiDetail->evald_target }}" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="evald_capaian">Capaian</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fa-solid fa-percent"></i>
                                                    </span>
                                                </div>
                                                <input type="text" class="form-control" name="evald_capaian" value="{{ $evaluasiDetail->evald_capaian }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="evald_keterangan">Keterangan</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fa-solid fa-info-circle"></i>
                                                    </span>
                                                </div>
                                                <textarea class="form-control" name="evald_keterangan" rows="4">{{ old('evald_keterangan', $evaluasiDetail->evald_keterangan) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group text-right">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <a href="{{ route('evaluasi.index-detail', $evaluasi->eval_id) }}" class="btn btn-danger">Kembali</a>
                                </div>
                            </form>                          

                            <!-- Tabel Data Monitoring -->
                            <h5 class="mt-4">Data Monitoring Terkait</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr class="text-center">
                                            <th>No</th>
                                            <th>Nama Rencana Kerja</th>
                                            <th>Unit Kerja</th>
                                            <th>Capaian</th>
                                            <th>Kondisi</th>
                                            <th>Kendala</th>
                                            <th>Tindak Lanjut</th>
                                            <th>Bukti</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rencanaKerja as $index => $rencana)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $rencana->rk_nama }}</td>
                                                <td>{{ $rencana->unitKerja->unit_nama ?? 'Tidak Ada' }}</td>

                                                @forelse($rencana->monitoring as $monitoring)
                                                    <td>{{ $monitoring->mtg_capaian }}</td>
                                                    <td>{{ $monitoring->mtg_kondisi }}</td>
                                                    <td>{{ $monitoring->mtg_kendala }}</td>
                                                    <td>{{ $monitoring->mtg_tindak_lanjut }}</td>
                                                    <td>
                                                        @if ($monitoring->mtg_bukti)
                                                            <a href="{{ Storage::url($monitoring->mtg_bukti) }}" target="_blank" class="btn btn-info btn-sm">Lihat Bukti</a>
                                                        @else
                                                            <span class="text-danger">Tidak ada bukti</span>
                                                        @endif
                                                    </td>
                                                @empty
                                                    <td colspan="6" class="text-center text-warning">Belum melakukan monitoring</td>
                                                @endforelse
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
@endpush