@extends('layouts.app')
@section('title', 'Evaluasi')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Daftar Evaluasi</h1>
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
                                <th>Nama Prodi</th>
                                <th>Tahun</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = $evaluasis->firstItem(); @endphp
                            @foreach ($evaluasis as $evaluasi)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $evaluasi->prodi->nama_prodi }}</td>
                                    <td>{{ $evaluasi->tahun_kerja->th_tahun }}</td>
                                    <td>
                                        @if($evaluasi->status == 0)
                                            <a class="btn btn-warning" href="{{ route('evaluasi.fill', $evaluasi->eval_id) }}"><i class="fa-solid fa-pen"></i> Isi/Ubah</a>
                                            @if($evaluasi->isFilled())
                                                <button class="btn btn-info finalBtn" data-id="{{ $evaluasi->eval_id }}"><i class="fa-solid fa-check"></i> Final</button>
                                            @else
                                                <button class="btn btn-secondary" disabled><i class="fa-solid fa-lock"></i> Final</button>
                                            @endif
                                        @else
                                            <a class="btn btn-info" href="{{ route('evaluasi.show', $evaluasi->eval_id) }}"><i class="fa-solid fa-eye"></i> Lihat Data</a>
                                        @endif
                                    </td>                                    
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($evaluasis->hasPages())
                    <div class="card-footer">
                        {{ $evaluasis->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraries -->
    <script src="{{ asset('library/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('library/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('library/summernote/dist/summernote-bs4.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Page Specific JS -->
    <script>
        // Script untuk membuka modal tambah data evaluasi
        document.getElementById("showModalBtn").addEventListener("click", function () {
            Swal.fire({
                title: 'Tambah Data Evaluasi',
                html: `
                    <form id="form-add-evaluasi">
                        @csrf
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
                            <label for="prodi_id" class="form-label">Prodi</label>
                            <select class="form-control" name="prodi_id" required>
                                <option value="">Pilih Prodi</option>
                                @foreach ($prodis as $prodi)
                                    <option value="{{ $prodi->prodi_id }}">{{ $prodi->nama_prodi }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                `,
                focusConfirm: false,
                preConfirm: () => {
                    const prodiId = document.querySelector('[name="prodi_id"]').value;
                    const thId = document.querySelector('[name="th_id"]').value;

                    if (!prodiId || !thId) {
                        Swal.showValidationMessage('Harap pilih Prodi dan Tahun');
                        return false;
                    }

                    return { prodi_id: prodiId, th_id: thId };
                },
                showCancelButton: true,
                cancelButtonText: 'Batal',
                confirmButtonText: 'Tambah Data',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    const formData = new FormData();
                    formData.append('prodi_id', result.value.prodi_id);
                    formData.append('th_id', result.value.th_id);

                    fetch("{{ route('evaluasi.store') }}", {
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: formData
                    }).then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Sukses',
                                    text: data.message,
                                    icon: 'success',
                                    showConfirmButton: true,
                                    confirmButtonText: 'OK',
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Gagal', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Gagal', 'Terjadi kesalahan saat mengirim data.', 'error');
                        });
                }
            });
        });

        // SweetAlert konfirmasi untuk tombol final
        document.querySelectorAll('.finalBtn').forEach(button => {
    button.addEventListener('click', function () {
        const evaluasiId = this.getAttribute('data-id');
        
        Swal.fire({
            title: 'Yakin ingin finalisasi?',
            text: 'Pastikan data sudah terisi dengan benar.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, finalisasi!',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                // Kirim permintaan AJAX ke server untuk melakukan finalisasi
                fetch(`/evaluasi/final/${evaluasiId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    }
                }).then(response => response.json())
                  .then(data => {
                      if (data.success) {
                          Swal.fire('Sukses', data.message, 'success');
                          location.reload(); // Refresh halaman setelah finalisasi
                      } else {
                          Swal.fire('Gagal', data.message, 'error');
                      }
                  })
                  .catch(error => {
                      Swal.fire('Gagal', 'Terjadi kesalahan saat memfinalisasi.', 'error');
                  });
            }
        });
    });
});
    </script>
@endpush