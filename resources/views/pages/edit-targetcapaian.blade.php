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
                                            <!-- Indikator Kinerja -->
                                            <div class="form-group">
                                                <label for="ik_id">Indikator Kinerja</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa-solid fa-bullseye"></i>
                                                        </div>
                                                    </div>
                                                    <select class="form-control" name="ik_id" id="ik_id" required>
                                                        <option value="" disabled>Pilih Indikator Kinerja</option>
                                                        @foreach ($indikatorkinerjautamas as $indikatorkinerja)
                                                            <option value="{{ $indikatorkinerja->ik_id }}" 
                                                                data-jenis="{{ $indikatorkinerja->ik_ketercapaian }}"
                                                                data-baseline="{{ $indikatorkinerja->ik_baseline }}"
                                                                {{ old('ik_id', $targetcapaian->ik_id) == $indikatorkinerja->ik_id ? 'selected' : '' }}>
                                                                {{ $indikatorkinerja->ik_kode }} - {{ $indikatorkinerja->ik_nama }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="ik_baseline">Nilai Baseline</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fas fa-sort-amount-down"></i>
                                                        </div>
                                                    </div>
                                                    <input type="text" id="ik_baseline" name="ik_baseline_display" 
                                                        class="form-control" 
                                                        value="{{ $baseline ?? 'Pilih Indikator Kinerja Terlebih Dahulu' }}" readonly>
                                                </div>
                                            </div>

                                            <!-- Target Capaian -->
                                            <div class="form-group">
                                                <label for="ti_target">Target Capaian</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa-solid fa-award"></i>
                                                        </div>
                                                    </div>
                                                    <input type="text" id="ti_target" name="ti_target" class="form-control" 
                                                        value="{{ old('ti_target', $targetcapaian->ti_target) }}" placeholder="Isi Target Capaian" required>
                                                </div>
                                                <small id="ti_target_hint" class="form-text text-muted">Isi sesuai dengan jenis ketercapaian.</small>
                                            </div>
                                        </div>

                                        <!-- Kolom Kanan -->
                                        <div class="col-md-6">
                                            <!-- Keterangan -->
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

                                            <!-- Prodi -->
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

                                            <!-- Tahun -->
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
    @include('sweetalert::alert')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ikSelect = document.getElementById("ik_id");
            const tiTargetInput = document.getElementById("ti_target");
            const tiTargetHint = document.getElementById("ti_target_hint");

            // Fungsi untuk memperbarui placeholder dan hint
            function updateTargetFields() {
                const selectedOption = ikSelect.options[ikSelect.selectedIndex];
                const jenis = selectedOption.getAttribute("data-jenis");

                if (jenis === "nilai") {
                    tiTargetInput.placeholder = "Indikator ini menggunakan ketercapaian nilai";
                    tiTargetHint.textContent = "Isi nilai ketercapaian seperti 1.2 atau 1.3.";
                } else if (jenis === "persentase") {
                    tiTargetInput.placeholder = "Indikator ini menggunakan ketercapaian persentase";
                    tiTargetHint.textContent = "Isi angka dalam rentang 0 hingga 100.";
                } else if (jenis === "ketersediaan") {
                    tiTargetInput.placeholder = "Indikator ini menggunakan ketercapaian ketersediaan";
                    tiTargetHint.textContent = "Isi dengan 'Ada' atau 'Draft'.";
                } else {
                    tiTargetInput.placeholder = "Isi Target Capaian";
                    tiTargetHint.textContent = "Isi sesuai dengan jenis ketercapaian.";
                }
            }

            // Jalankan saat dropdown berubah
            ikSelect.addEventListener("change", updateTargetFields);

            // Jalankan saat halaman selesai dimuat
            updateTargetFields();
        });
    </script>    
    <script>
        // Kosongkan baseline saat halaman dimuat
        document.getElementById('ik_baseline').value = '{{ $baseline ?? 'Pilih Indikator Kinerja Terlebih Dahulu' }}';
    
        // Ketika ada perubahan pada pilihan indikator kinerja
        document.getElementById('ik_id').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var baseline = selectedOption.getAttribute('data-baseline');
            document.getElementById('ik_baseline').value = baseline;
        });
    </script>
@endpush
