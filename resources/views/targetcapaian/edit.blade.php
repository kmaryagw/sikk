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
                <h1>Form Edit Target</h1>
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

                                            <!-- BASELINE (Dinamis) -->
                                            <div class="form-group">
                                                <label for="baseline">Nilai Baseline</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fas fa-sort-amount-down"></i>
                                                        </div>
                                                    </div>
                                                    
                                                    {{-- Input Teks/Angka (Default) --}}
                                                    <input type="text" id="baseline_input" class="form-control dynamic-input-baseline" 
                                                        value="{{ old('baseline', $baseline) }}" 
                                                        placeholder="Masukkan Nilai Baseline">

                                                    {{-- Input Dropdown (Khusus Ketersediaan) --}}
                                                    <select id="baseline_select" class="form-control dynamic-input-baseline" style="display: none;">
                                                        <option value="draft" {{ strtolower(old('baseline', $baseline)) == 'draft' ? 'selected' : '' }}>Draft</option>
                                                        <option value="ada" {{ strtolower(old('baseline', $baseline)) == 'ada' ? 'selected' : '' }}>Ada</option>
                                                    </select>
                                                </div>
                                                <small class="form-text text-muted" id="baseline_hint">Baseline dapat diedit.</small>
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
                                                    
                                                    {{-- 1. Input Hidden untuk mengirim data ke Controller (Karena Select Disabled tidak terkirim) --}}
                                                    <input type="hidden" name="ik_id" value="{{ $targetcapaian->ik_id }}">

                                                    {{-- 2. Select Disabled (Hanya untuk tampilan & Logic JS) --}}
                                                    {{-- Note: name dihapus atau dibiarkan tidak masalah karena disabled, tapi styling dibuat abu-abu --}}
                                                    <select class="form-control" id="ik_id" disabled style="background-color: #e9ecef; cursor: not-allowed;">
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
                                                <small class="form-text text-muted">Indikator kinerja tidak dapat diubah pada mode edit.</small>
                                            </div>
                                            <!-- Jenis Ketercapaian -->
                                            <div class="form-group">
                                                <label for="jenis_ketercapaian">Jenis Ketercapaian</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa-solid fa-info-circle"></i>
                                                        </div>
                                                    </div>
                                                    <input type="text" id="jenis_ketercapaian" class="form-control" value="" disabled>
                                                </div>
                                            </div>

                                            <!-- TARGET CAPAIAN (Dinamis) -->
                                            <div class="form-group">
                                                <label for="ti_target">Target</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa-solid fa-award"></i>
                                                        </div>
                                                    </div>

                                                    {{-- Input Teks/Angka (Default) --}}
                                                    <input type="text" id="target_input" class="form-control dynamic-input-target" 
                                                        value="{{ old('ti_target', $targetcapaian->ti_target) }}" 
                                                        placeholder="Isi Target Capaian">

                                                    {{-- Input Dropdown (Khusus Ketersediaan) --}}
                                                    <select id="target_select" class="form-control dynamic-input-target" style="display: none;">
                                                        <option value="draft" {{ strtolower(old('ti_target', $targetcapaian->ti_target)) == 'draft' ? 'selected' : '' }}>Draft</option>
                                                        <option value="ada" {{ strtolower(old('ti_target', $targetcapaian->ti_target)) == 'ada' ? 'selected' : '' }}>Ada</option>
                                                    </select>
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
            const selectIK = document.getElementById("ik_id");
            const jenisInput = document.getElementById("jenis_ketercapaian");
            const targetHint = document.getElementById("ti_target_hint");
            
            // Elemen Baseline
            const baselineInput = document.getElementById("baseline_input");
            const baselineSelect = document.getElementById("baseline_select");
            
            // Elemen Target
            const targetInput = document.getElementById("target_input");
            const targetSelect = document.getElementById("target_select");

            function adjustInputType(jenis, inputEl, selectEl, fieldName) {
                // Reset display
                inputEl.style.display = 'none';
                selectEl.style.display = 'none';
                
                // Hapus atribut name dari keduanya dulu (supaya tidak double submit)
                inputEl.removeAttribute('name');
                selectEl.removeAttribute('name');

                if (jenis === 'ketersediaan') {
                    // Tampilkan Dropdown
                    selectEl.style.display = 'block';
                    selectEl.setAttribute('name', fieldName); // Set name ke select
                } else {
                    // Tampilkan Input Biasa
                    inputEl.style.display = 'block';
                    inputEl.setAttribute('name', fieldName); // Set name ke input
                    
                    if (jenis === 'nilai' || jenis === 'persentase') {
                        inputEl.type = 'number';
                        inputEl.step = '0.01'; // Izinkan desimal
                    } else {
                        // Rasio atau lainnya
                        inputEl.type = 'text';
                    }
                }
            }

            function updateInfo() {
                const selectedOption = selectIK.options[selectIK.selectedIndex];
                const jenis = selectedOption.getAttribute("data-jenis");
                
                // Update Text Jenis Ketercapaian
                if (jenis) {
                    jenisInput.value = jenis.charAt(0).toUpperCase() + jenis.slice(1);
                    
                    // Update Hint Text
                    if (jenis === 'nilai') {
                        targetHint.textContent = "Isi dengan angka, contoh: 2.5 atau 80";
                    } else if (jenis === 'persentase') {
                        targetHint.textContent = "Isi angka antara 0 sampai 100.";
                    } else if (jenis === 'ketersediaan') {
                        targetHint.textContent = "Pilih status ketersediaan.";
                    } else if (jenis === 'rasio') {
                        targetHint.textContent = "Isi dengan format x : y, contoh: 2 : 1";
                    }

                    // --- LOGIC DINAMIS BASELINE ---
                    adjustInputType(jenis, baselineInput, baselineSelect, 'baseline');

                    // --- LOGIC DINAMIS TARGET ---
                    adjustInputType(jenis, targetInput, targetSelect, 'ti_target');

                } else {
                    jenisInput.value = "";
                    targetHint.textContent = "Isi sesuai dengan jenis ketercapaian.";
                }
            }

            // Event saat dropdown indikator berubah
            selectIK.addEventListener("change", function() {
                updateInfo();
                
                // Khusus saat GANTI indikator secara manual, isi baseline dengan default master
                const selectedOption = selectIK.options[selectIK.selectedIndex];
                const jenis = selectedOption.getAttribute("data-jenis");
                const defaultBaseline = selectedOption.getAttribute("data-baseline");
                
                if (jenis === 'ketersediaan') {
                    // Set default dropdown ke 'draft' jika master kosong, atau sesuai master
                    baselineSelect.value = (defaultBaseline && (defaultBaseline === 'ada' || defaultBaseline === 'draft')) 
                                            ? defaultBaseline 
                                            : 'draft';
                } else {
                    // Set input text/number
                    baselineInput.value = defaultBaseline ? defaultBaseline : '';
                }
            });

            // Jalankan sekali saat load halaman
            if (selectIK.value) {
                updateInfo();
            }
        });
    </script>
@endpush
