@extends('layouts.app')
@section('title', 'Edit Detail Evaluasi')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Tambah Evaluasi</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item">Tambah Evaluasi</div>
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
                            <form action="{{ route('evaluasi.update-detail', $evaluasi->eval_id) }}" method="POST">
                                @csrf
                                @method('PUT')
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
                                                <input type="text" id="prodi" class="form-control" value="{{ $evaluasi->prodi->nama_prodi }}" readonly>
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
                                                <input type="text" id="tahun" class="form-control" value="{{ $evaluasi->tahunKerja->th_tahun }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="ik_nama">Indikator Kinerja</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fa-solid fa-bullseye"></i>
                                                    </span>
                                                </div>
                                                <input type="text" id="ik_nama" class="form-control" value="{{ $targetIndikator->indikatorKinerja->ik_nama }}" readonly>
                                            </div>
                                        </div>
                                    </div> --}}

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="ik_nama">Indikator Kinerja</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fa-solid fa-bullseye"></i>
                                                    </span>
                                                </div>
                                                <input type="text" id="ik_nama" class="form-control" 
                                                    value="{{ $targetIndikator->indikatorKinerja->ik_nama }}" readonly>
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

                                    {{-- <div class="col-md-6">
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
                                    </div> --}}

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="evald_capaian">Capaian</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fa-solid fa-award"></i>
                                                    </span>
                                                </div>
                                                <input type="text" class="form-control" name="evald_capaian" id="evald_capaian"
                                                    placeholder="Isi Capaian" value="{{ old('evald_capaian', $evaluasiDetail->evald_capaian) }}" required>
                                            </div>
                                            <small id="evald_capaian_hint" class="form-text text-muted">Isi sesuai dengan jenis ketercapaian.</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="evald_keterangan">Keterangan Tambahan</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fa-solid fa-plus-circle"></i>
                                                    </span>
                                                </div>
                                                <textarea class="form-control" name="evald_keterangan" rows="4">{{ $evaluasiDetail->evald_keterangan }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="evald_keterangan">Status</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fa-solid fa-check-circle"></i>
                                                    </span>
                                                </div>
                                                <select class="form-control" name="evald_status" required>
                                                    <option value="" disabled {{ is_null($evaluasiDetail->evald_status) ? 'selected' : '' }}>Pilih Status</option>
                                                    @foreach ($status as $statuses)
                                                        <option value="{{ $statuses }}" 
                                                            {{ old('evald_status', $evaluasiDetail->evald_status) == $statuses ? 'selected' : '' }}>
                                                            {{ $statuses }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>                                    
                                </div>

                                <div class="form-group text-right">
                                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan</button>
                                    <a href="{{ route('evaluasi.index-detail', $evaluasi->eval_id) }}" class="btn btn-danger"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
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
                                                            <th>Bukti</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($program->monitoring as $monitoring)
                                                            <tr>
                                                                <td>{{ $monitoring->mtg_capaian }}</td>
                                                                <td>{{ $monitoring->mtg_kondisi }}</td>
                                                                <td>{{ $monitoring->mtg_kendala }}</td>
                                                                <td>{{ $monitoring->mtg_tindak_lanjut }}</td>
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
                                                                        <a href="{{ $real->rkr_url }}" target="_blank">{{ $real->rkr_url }}</a>
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
            const evaldCapaianInput = document.getElementById("evald_capaian");
            const evaldCapaianHint = document.getElementById("evald_capaian_hint");
    
            // Menambahkan event listener untuk perubahan pada input Indikator Kinerja
            // Karena input hanya dibaca saja (readonly), jenis ketercapaian ditentukan berdasarkan data yang sudah ada
            const jenis = "{{ $targetIndikator->indikatorKinerja->ik_ketercapaian }}";
    
            if (jenis === "nilai") {
                evaldCapaianInput.placeholder = "Indikator ini menggunakan ketercapaian nilai";
                evaldCapaianHint.textContent = "Isi nilai ketercapaian seperti 1.2 atau 1.3.";
            } else if (jenis === "persentase") {
                evaldCapaianInput.placeholder = "Indikator ini menggunakan ketercapaian persentase";
                evaldCapaianHint.textContent = "Isi angka dalam rentang 0 hingga 100.";
            } else if (jenis === "ketersediaan") {
                evaldCapaianInput.placeholder = "Indikator ini menggunakan ketercapaian ketersediaan";
                evaldCapaianHint.textContent = "Isi dengan 'Ada' atau 'Tidak'.";
            } else {
                evaldCapaianInput.placeholder = "Isi Capaian";
                evaldCapaianHint.textContent = "Isi sesuai dengan jenis ketercapaian.";
            }
        });
    </script>
@endpush