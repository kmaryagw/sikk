@extends('layouts.app')
@section('title', 'Edit Monitoring IKU')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Tambah Monitoring IKU</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item">Tambah Monitoring IKU</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card" >
                        <div class="card-body" style="background-color: #f4f4f4;" >
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
                            <form action="{{ route('monitoringiku.update-detail', $monitoringiku->mti_id) }}" method="POST">
                                @csrf
                                @method('PUT')
                    
                                <!-- Card 1: Informasi Utama -->
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h4 class="text-danger mb-3">Data Target Capaian</h4>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="prodi">Program Studi</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fa-solid fa-building-columns"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" id="prodi" class="form-control" value="{{ $monitoringiku->prodi->nama_prodi }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tahun">Tahun Kerja</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fa-solid fa-calendar"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" id="tahun" class="form-control" value="{{ $monitoringiku->tahunKerja->th_tahun }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="ik_nama">Indikator Kinerja</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fa-solid fa-bullseye"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" id="ik_nama" class="form-control" value="{{ $targetIndikator->indikatorKinerja->ik_kode }} - {{ $targetIndikator->indikatorKinerja->ik_nama }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="ik_baseline">Baseline</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fa-solid fa-sort-amount-down"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" id="ik_baseline" class="form-control" 
                                                               value="{{ $targetIndikator->indikatorKinerja->ik_baseline }}" readonly>
                                                    </div>
                                                </div>
                                            </div>                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="ti_target">Target</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fa-solid fa-award"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" id="ti_target" class="form-control" value="{{ $targetIndikator->ti_target }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="ti_keterangan">Keterangan Indikator</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fa-solid fa-info-circle"></i>
                                                            </span>
                                                        </div>
                                                        <input class="form-control" name="ti_keterangan" value="{{ $targetIndikator->ti_keterangan }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                    
                                <!-- Card 2: Form Input -->
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="text-danger mb-3">Data Monitoring IKU</h4>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mtid_capaian">Capaian</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fa-solid fa-award"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" class="form-control" name="mtid_capaian" id="mtid_capaian"
                                                            placeholder="Isi Capaian" value="{{ old('mtid_capaian', $monitoringikuDetail->mtid_capaian) }}" required>
                                                    </div>
                                                    <small id="mtid_capaian_hint" class="form-text text-muted">Isi sesuai dengan jenis ketercapaian.</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mtid_status">Status</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fa-solid fa-check-circle"></i>
                                                            </span>
                                                        </div>
                                                        <select class="form-control" name="mtid_status" required>
                                                            <option value="" disabled {{ is_null($monitoringikuDetail->mtid_status) ? 'selected' : '' }}>Pilih Status</option>
                                                            @foreach ($status as $statuses)
                                                                <option value="{{ $statuses }}" 
                                                                    {{ old('mtid_status', $monitoringikuDetail->mtid_status) == $statuses ? 'selected' : '' }}>
                                                                    {{ $statuses }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mtid_url">URL</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">
                                                                <i class="fa-solid fa-link"></i>
                                                            </div>
                                                        </div>
                                                        <input class="form-control" type="url" name="mtid_url" value="{{ old('mtid_url', $monitoringikuDetail->mtid_url) }}" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mtid_keterangan">Keterangan Tambahan</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fa-solid fa-plus-circle"></i>
                                                            </span>
                                                        </div>
                                                        <textarea class="form-control" name="mtid_keterangan" rows="4">{{ $monitoringikuDetail->mtid_keterangan }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                    
                                        <div class="form-group text-right">
                                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan</button>
                                            <a href="{{ route('monitoringiku.index-detail', $monitoringiku->mti_id) }}" class="btn btn-danger"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>                    
                    
                    {{-- Data program kerja, monitoring, realisasi --}}
                    <div class="card">
                        <div class="card-body">
                            <h4 class="text-danger mt-3 mb-4">Data Program Kerja yang Terkait dengan 
                                <span class="badge badge-success">{{ $targetIndikator->indikatorKinerja->ik_nama }}</span>
                            </h4>
                            <div class="table-responsive">
                                @foreach($programKerja as $index => $program)
                                    <div class="mb-4 p-3" style="background-color: #f4f4f4; border-radius: 8px;">
                                        <!-- Program Kerja Header -->
                                        <h6 class="bg-secondary text-dark p-2 rounded">{{ $index + 1 }}. {{ $program->rk_nama }}</h6>
                                        <p class="mb-1"><strong>Unit Kerja:</strong> {{ $program->unitKerja->unit_nama ?? '-' }}</p>
                                        <p class="mb-1"><strong>Tahun:</strong> {{ $program->tahunKerja->th_tahun }}</p>
                                        <p class="mb-3"><strong>Periode:</strong> 
                                            @if($program->periodes->isNotEmpty())
                                                @foreach ($program->periodes as $periode)
                                                    <span class="badge badge-info">{{ $periode->pm_nama }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">Tidak ada periode</span>
                                            @endif
                                        </p>
                    
                                        <!-- Sub Monitoring -->
                                        <h6 class="mt-3 mb-2 text-danger">Monitoring</h6>
                                        @if($program->monitoring->isNotEmpty())
                                            <div class="table-wrapper" style="background-color: #ffffff; padding: 1em; border-radius: 8px;">
                                                <table class="table table-bordered table-hover table-striped text-center">
                                                    <thead>
                                                        <tr>
                                                            <th>Capaian</th>
                                                            <th>Kondisi</th>
                                                            <th>Kendala</th>
                                                            <th>Tindak Lanjut</th>
                                                            <th>Tanggal Tindak Lanjut</th>
                                                            <th>Status</th>
                                                            <th>Bukti</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($program->monitoring as $monitoring)
                                                            <tr>
                                                                <td>
                                                                    <div class="progress" style="height: 20px;">
                                                                        <div class="progress-bar" role="progressbar" style="width: {{ $monitoring->mtg_capaian }}%;" aria-valuenow="{{ $monitoring->rmtg_capaian }}" aria-valuemin="0" aria-valuemax="100">{{ $monitoring->mtg_capaian }}%</div>
                                                                    </div>
                                                                </td>
                                                                <td>{{ $monitoring->mtg_kondisi }}</td>
                                                                <td>{{ $monitoring->mtg_kendala }}</td>
                                                                <td>{{ $monitoring->mtg_tindak_lanjut }}</td>
                                                                <td>{{ $monitoring->mtg_tindak_lanjut_tanggal }}</td>
                                                                <td>
                                                                    @if (strtolower($monitoring->mtg_status) === 'y')
                                                                        <span class="text-success"><i class="fa-solid fa-check-circle"></i> Tercapai</span>
                                                                    @elseif (strtolower($monitoring->mtg_status) === 'n')
                                                                        <span class="text-warning"><i class="fa-solid fa-info-circle"></i> Belum Tercapai</span>
                                                                    @elseif (strtolower($monitoring->mtg_status) === 'p')
                                                                        <span class="text-primary"><i class="fa-solid fa-arrow-circle-right"></i> Perlu Tindak Lanjut</span>
                                                                    @else
                                                                        <span class="text-danger"><i class="fa-solid fa-times-circle"></i> Tidak Terlaksana</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($monitoring->mtg_bukti)
                                                                        <a href="{{ Storage::url($monitoring->mtg_bukti) }}" target="_blank" class="btn btn-success btn-sm"><i class="fa-solid fa-eye"></i> Lihat Bukti</a>
                                                                    @else
                                                                        <span class="text-danger">Tidak ada bukti</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <p class="text-muted">Belum ada data monitoring.</p>
                                        @endif
                    
                                        <!-- Sub Realisasi -->
                                        <h6 class="mt-3 mb-2 text-danger">Realisasi</h6>
                                        @if($program->realisasi->isNotEmpty())
                                            <div style="background-color: #ffffff; padding: 1em; border-radius: 8px;">
                                                <table class="table table-bordered table-hover table-striped text-center">
                                                    <thead>
                                                        <tr>
                                                            <th>Deskripsi</th>
                                                            <th>Capaian</th>
                                                            <th>Tanggal</th>
                                                            <th>URL</th>
                                                            <th>Bukti</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($program->realisasi as $real)
                                                            <tr>
                                                                <td>{{ $real->rkr_deskripsi }}</td>
                                                                <td>
                                                                    <div class="progress" style="height: 20px;">
                                                                        <div class="progress-bar" role="progressbar" style="width: {{ $real->rkr_capaian }}%;" aria-valuenow="{{ $real->rkr_capaian }}" aria-valuemin="0" aria-valuemax="100">{{ $real->rkr_capaian }}%</div>
                                                                    </div>
                                                                </td>
                                                                <td>{{ $real->rkr_tanggal ? \Carbon\Carbon::parse($real->rkr_tanggal)->format('d-m-Y') : '-' }}</td>
                                                                <td>
                                                                    @if($real->rkr_url)
                                                                        <a href="{{ $real->rkr_url}}" target="_blank" class="btn btn-link">Lihat URL</a>
                                                                    @else
                                                                        Tidak Ada URL
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($real->rkr_file)
                                                                        <a class="btn btn-success" href="{{ asset('storage/' . $real->rkr_file) }}" target="_blank"><i class="fa-solid fa-eye"></i> Lihat Dokumen</a>
                                                                    @else
                                                                        Tidak Ada Dokumen
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <p class="text-muted">Belum ada data realisasi.</p>
                                        @endif
                                    </div>
                                @endforeach
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

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ikNamaInput = document.getElementById("ik_nama");
            const mtidCapaianInput = document.getElementById("mtid_capaian");
            const mtidCapaianHint = document.getElementById("mtid_capaian_hint");
    
            const jenis = "{{ $targetIndikator->indikatorKinerja->ik_ketercapaian }}";
    
            if (jenis === "nilai") {
                mtidCapaianInput.placeholder = "Indikator ini menggunakan ketercapaian nilai";
                mtidCapaianHint.textContent = "Isi nilai ketercapaian seperti 1.2 atau 1.3.";
            } else if (jenis === "persentase") {
                mtidCapaianInput.placeholder = "Indikator ini menggunakan ketercapaian persentase";
                mtidCapaianHint.textContent = "Isi angka dalam rentang 0 hingga 100.";
            } else if (jenis === "ketersediaan") {
                mtidCapaianInput.placeholder = "Indikator ini menggunakan ketercapaian ketersediaan";
                mtidCapaianHint.textContent = "Isi dengan 'Ada' atau 'Draft'.";
            } else {
                mtidCapaianInput.placeholder = "Isi Capaian";
                mtidCapaianHint.textContent = "Isi sesuai dengan jenis ketercapaian.";
            }
        });
    </script>
@endpush