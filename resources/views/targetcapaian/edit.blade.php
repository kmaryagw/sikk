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

                                <form method="POST" action="{{ route('targetcapaianprodi.update', $targetcapaian->ti_id) }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-group d-flex align-items-center">
                                        <label for="th_id" class="mr-2" style="font-size: 1rem;">Tahun Aktif:</label>
                                        @foreach ($tahuns as $tahun)
                                            @if ($tahun->th_is_aktif === 'y')
                                                <span class="badge badge-primary p-3" style="font-size: 1rem;">
                                                    <i class="fa-solid fa-calendar-alt"></i> {{ $tahun->th_tahun }}
                                                </span>
                                                <input type="hidden" name="th_id" value="{{ $tahun->th_id }}">
                                            @endif
                                        @endforeach
                                    </div> 
                                    <div class="row">
                                        <!-- Kolom Kiri -->
                                        <div class="col-md-6">
                                            <!-- Prodi -->
                                            <div class="form-group">
                                                <label>Prodi</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa-solid fa-building-columns"></i>
                                                        </div>
                                                    </div>
                                                    @if ($userRole === 'prodi' && $userProdi)
                                                        <input type="text" class="form-control" value="{{ $userProdi->nama_prodi }}" readonly>
                                                        <input type="hidden" name="prodi_id" value="{{ $userProdi->prodi_id }}">
                                                    @else
                                                        <select class="form-control" name="prodi_id">
                                                            <option value="" disabled>Pilih Prodi</option>
                                                            @foreach ($prodis as $prodi)
                                                                <option value="{{ $prodi->prodi_id }}" 
                                                                    {{ $prodi->prodi_id == $targetcapaian->prodi_id ? 'selected' : '' }}>
                                                                    {{ $prodi->nama_prodi }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="baseline">Nilai Baseline</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fas fa-sort-amount-down"></i>
                                                        </div>
                                                    </div>
                                                    <input type="text" id="baseline" name="baseline_display" 
                                                        class="form-control" 
                                                        value="{{ $baseline ?? 'Pilih Indikator Kinerja Terlebih Dahulu' }}" readonly>
                                                </div>
                                            </div>

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
                                        </div>

                                        <!-- Kolom Kanan -->
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
                                    </div>

                                    <div class="form-footer text-right">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <a href="{{ url('targetcapaianprodi') }}" class="btn btn-danger">Kembali</a>
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

            ikSelect.addEventListener("change", updateTargetFields);

            updateTargetFields();
        });
    </script>    
    <script>
        document.getElementById('baseline').value = '{{ $baseline ?? 'Pilih Indikator Kinerja Terlebih Dahulu' }}';
    
        document.getElementById('ik_id').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var baseline = selectedOption.getAttribute('data-baseline');
            document.getElementById('baseline').value = ik_baseline;
        });
    </script>
@endpush
