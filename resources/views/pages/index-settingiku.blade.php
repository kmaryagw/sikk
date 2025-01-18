@extends('layouts.app')
@section('title', 'Setting IKU')

@push('style')
<link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
<link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Setting IKU</h1>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <form class="row g-2 align-items-center">
                        <div class="col-auto">
                            <input class="form-control" name="q" value="{{ $q }}" placeholder="Pencarian..." />
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-info"><i class="fa-solid fa-search"></i> Cari</button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-primary" id="showModalBtn">
                                <i class="fa-solid fa-plus"></i> Tambah
                            </button>
                        </div>
                    </form>
                </div>

                <div class="table-responsive text-center">
                    <table class="table table-hover table-bordered table-striped m-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tahun</th>
                                <th>Indikator Kinerja</th>
                                <th>Baseline</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = $settings->firstItem(); @endphp
                            @foreach ($settings as $setting)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $setting->tahunKerja->th_tahun }}</td>
                                    <td>{{ $setting->indikatorKinerja->ik_nama }}</td>
                                    <td>
                                        @if ($setting->indikatorKinerja->ik_ketercapaian == 'persentase' && is_numeric($setting->baseline))
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: {{ intval($setting->baseline) }}%;" 
                                                     aria-valuenow="{{ intval($setting->baseline) }}" 
                                                     aria-valuemin="0" aria-valuemax="100">
                                                    {{ $setting->baseline }}%
                                                </div>
                                            </div>
                                        @elseif ($setting->indikatorKinerja->ik_ketercapaian == 'nilai' && is_numeric($setting->baseline))
                                            <span class="badge badge-primary">{{ $setting->baseline }}</span>
                                        @elseif (in_array(strtolower($setting->baseline), ['ada', 'draft']))
                                            @if (strtolower($setting->baseline) === 'ada')
                                                <span class="text-success"><i class="fa-solid fa-check-circle"></i> Ada</span>
                                            @else
                                                <span class="text-warning"><i class="fa-solid fa-info-circle"></i> Draft</span>
                                            @endif
                                        @else
                                            {{ $setting->baseline }}
                                        @endif
                                    </td> 
                                    <td>
                                        <button class="btn btn-warning btn-edit" data-id="{{ $setting->id_setting }}" data-th="{{ $setting->th_id }}" data-ik="{{ $setting->ik_id }}" data-baseline="{{ $setting->baseline }}">
                                            <i class="fa-solid fa-pencil"></i> Edit
                                        </button>
                                        <form id="delete-form-{{ $setting->id_setting }}" action="{{ route('settingiku.destroy', $setting->id_setting) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger" onclick="confirmDelete(event, '{{ $setting->id_setting }}')">
                                                <i class="fa-solid fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($settings->hasPages())
                    <div class="card-footer">
                        {{ $settings->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('library/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('library/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Modal Tambah Data
        // Modal Tambah Data
            // Modal Tambah Data
            document.getElementById("showModalBtn").addEventListener("click", function () {
    Swal.fire({
        title: 'Tambah Setting IKU',
        html: `
            <form id="form-add-settingiku">
                <div class="mb-3">
                    <label for="th_id" class="form-label">Tahun</label>
                    <select class="form-control" name="th_id" required>
                        <option value="">Pilih Tahun</option>
                        @foreach ($tahuns as $tahun)
                            <option value="{{ $tahun->th_id }}">{{ $tahun->th_tahun }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="ik_id" class="form-label">Indikator Kinerja</label>
                    <select class="form-control" name="ik_id" id="ik_id" required>
                        <option value="">Pilih Indikator Kinerja</option>
                        @foreach ($indikatorKinerjas as $indikator)
                            <option value="{{ $indikator->ik_id }}" data-type="{{ $indikator->ik_ketercapaian }}">{{ $indikator->ik_nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="baseline" class="form-label">Baseline</label>
                    <input type="number" class="form-control" name="baseline" id="baseline" required>
                    <small id="baseline_hint" class="form-text text-muted">Isi sesuai dengan jenis ketercapaian.</small>
                </div>
            </form>
        `,
        focusConfirm: false,
        preConfirm: () => {
            const indikatorKinerjaId = document.querySelector('[name="ik_id"]').value;
            const thId = document.querySelector('[name="th_id"]').value;
            const baseline = document.querySelector('[name="baseline"]').value;
            const selectedIndicator = document.querySelector('[name="ik_id"]');
            
            // Pastikan pilihan indikator kinerja memiliki atribut data-type
            const indicatorType = selectedIndicator.options[selectedIndicator.selectedIndex]?.dataset?.type;

            // Validasi jika indikator kinerja menggunakan tipe 'persentase'
            if (indicatorType === 'persentase') {
                // Cek apakah baseline adalah angka bulat dan dalam rentang 0-100
                if (isNaN(baseline) || baseline < 0 || baseline > 100 || !Number.isInteger(parseFloat(baseline))) {
                    Swal.showValidationMessage('Nilai baseline harus berupa angka bulat antara 0 dan 100');
                    return false;
                }
            }

            if (!indikatorKinerjaId || !thId || !baseline) {
                Swal.showValidationMessage('Harap isi semua bidang');
                return false;
            }

            return { ik_id: indikatorKinerjaId, th_id: thId, baseline: baseline };
        },
        showCancelButton: true,
        confirmButtonText: 'Tambah Data',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
    }).then((result) => {
        if (result.isConfirmed) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            fetch("{{ route('settingiku.store') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(result.value)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Sukses', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            })
            .catch(() => Swal.fire('Gagal', 'Terjadi kesalahan.', 'error'));
        }
    });

    // Menambahkan logika untuk update placeholder dan hint sesuai jenis indikator
    document.querySelector('[name="ik_id"]').addEventListener("change", function () {
        const selectedOption = this.options[this.selectedIndex];
        const jenis = selectedOption.getAttribute("data-type");
        const baselineInput = document.getElementById("baseline");
        const baselineHint = document.getElementById("baseline_hint");

        if (jenis === "nilai") {
            baselineInput.placeholder = "Indikator ini menggunakan ketercapaian nilai";
            baselineHint.textContent = "Isi nilai ketercapaian seperti 1.2 atau 1.3.";
        } else if (jenis === "persentase") {
            baselineInput.placeholder = "Indikator ini menggunakan ketercapaian persentase";
            baselineHint.textContent = "Isi angka dalam rentang 0 hingga 100.";
        } else if (jenis === "ketersediaan") {
            baselineInput.placeholder = "Indikator ini menggunakan ketercapaian ketersediaan";
            baselineHint.textContent = "Isi dengan 'Ada' atau 'Draft'.";
        } else {
            baselineInput.placeholder = "Isi Target Capaian";
            baselineHint.textContent = "Isi sesuai dengan jenis ketercapaian.";
        }
    });
});

// Modal Edit Data
document.querySelectorAll('.btn-edit').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.getAttribute('data-id');
        const thId = this.getAttribute('data-th');
        const ikId = this.getAttribute('data-ik');
        const baseline = this.getAttribute('data-baseline');
        
        // Fetch data tipe indikator kinerja
        const indicatorType = this.getAttribute('data-type');

        Swal.fire({
            title: 'Edit Setting IKU',
            html: `
                <form id="form-edit-settingiku">
                    <div class="mb-3">
                        <label for="th_id" class="form-label">Tahun</label>
                        <select class="form-control" name="th_id" required>
                            <option value="">Pilih Tahun</option>
                            @foreach ($tahuns as $tahun)
                                <option value="{{ $tahun->th_id }}" ${thId == '{{ $tahun->th_id }}' ? 'selected' : ''}>{{ $tahun->th_tahun }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="ik_id" class="form-label">Indikator Kinerja</label>
                        <select class="form-control" name="ik_id" required>
                            <option value="">Pilih Indikator Kinerja</option>
                            @foreach ($indikatorKinerjas as $indikator)
                                <option value="{{ $indikator->ik_id }}" ${ikId == '{{ $indikator->ik_id }}' ? 'selected' : ''} data-type="{{ $indikator->ik_ketercapaian }}">{{ $indikator->ik_nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="baseline" class="form-label">Baseline</label>
                        <input type="number" class="form-control" name="baseline" value="${baseline}" id="baseline" required>
                        <small id="baseline_hint" class="form-text text-muted">Isi sesuai dengan jenis ketercapaian.</small>
                    </div>
                </form>
            `,
            focusConfirm: false,
            preConfirm: () => {
                const indikatorKinerjaId = document.querySelector('[name="ik_id"]').value;
                const thId = document.querySelector('[name="th_id"]').value;
                const baseline = document.querySelector('[name="baseline"]').value;
                const selectedIndicator = document.querySelector('[name="ik_id"]');
                
                // Validasi jika indikator kinerja menggunakan tipe 'persentase'
                const indicatorType = selectedIndicator.options[selectedIndicator.selectedIndex].dataset.type;
                if (indicatorType === 'persentase') {
                    // Cek apakah baseline adalah angka bulat dan dalam rentang 0-100
                    if (isNaN(baseline) || baseline < 0 || baseline > 100 || !Number.isInteger(parseFloat(baseline))) {
                        Swal.showValidationMessage('Nilai baseline harus berupa angka bulat antara 0 dan 100');
                        return false;
                    }
                }

                if (!indikatorKinerjaId || !thId || !baseline) {
                    Swal.showValidationMessage('Harap isi semua bidang');
                    return false;
                }

                return { id: id, ik_id: indikatorKinerjaId, th_id: thId, baseline: baseline };
            },
            showCancelButton: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                fetch(`/settingiku/${result.value.id}`, {
                    method: "PUT",
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(result.value)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Sukses', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                    }
                })
                .catch(() => Swal.fire('Gagal', 'Terjadi kesalahan.', 'error'));
            }
        });

        // Update hint saat change pada input tipe indikator
        document.querySelector('[name="ik_id"]').addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const jenis = selectedOption.getAttribute("data-type");
            const baselineInput = document.getElementById("baseline");
            const baselineHint = document.getElementById("baseline_hint");

            if (jenis === "nilai") {
                baselineInput.placeholder = "Indikator ini menggunakan ketercapaian nilai";
                baselineHint.textContent = "Isi nilai ketercapaian seperti 1.2 atau 1.3.";
            } else if (jenis === "persentase") {
                baselineInput.placeholder = "Indikator ini menggunakan ketercapaian persentase";
                baselineHint.textContent = "Isi angka dalam rentang 0 hingga 100.";
            } else if (jenis === "ketersediaan") {
                baselineInput.placeholder = "Indikator ini menggunakan ketercapaian ketersediaan";
                baselineHint.textContent = "Isi dengan 'Ada' atau 'Draft'.";
            } else {
                baselineInput.placeholder = "Isi Target Capaian";
                baselineHint.textContent = "Isi sesuai dengan jenis ketercapaian.";
            }
        });
    });
});


    
        // Konfirmasi Hapus
        function confirmDelete(event, settingId) {
            event.preventDefault();
    
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data ini akan dihapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${settingId}`).submit();
                }
            });
        }
    </script>
    
    
@endpush
