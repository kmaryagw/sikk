@extends('layouts.app')

@section('title', 'Daftar Pengumuman')

@section('main')
<div class="main-content section">
    <div class="section-header">
        <h2 class="mb-4 text-danger">Daftar Pengumuman</h2>
    </div>

    {{-- Alert sukses --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <style>
        /* Dropdown item styling */
        .dropdown-menu .dropdown-item {
            font-size: 0.95rem;
            padding: 1rem 1rem;
            line-height: 1.25rem;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        /* Ikon sejajar rapi */
        .dropdown-menu .dropdown-item i {
            width: 1.25rem;
            text-align: center;
            font-size: 1rem;
        }

        /* Hover efek halus */
        .dropdown-menu .dropdown-item:hover {
            background-color: #f1f1f1;
        }

        /* Warna spesifik tiap aksi */
        .dropdown-menu .dropdown-item.text-info:hover {
            color: #0dcaf0 !important; /* Biru muda */
        }

        .dropdown-menu .dropdown-item.text-warning:hover {
            color: #ffc107 !important; /* Kuning */
        }

        .dropdown-menu .dropdown-item.text-danger:hover {
            color: #dc3545 !important; /* Merah */
}
    </style>


    {{-- Tombol tambah --}}
    <div class="mb-4 d-flex justify-content-end">
        <a href="{{ route('announcement.create') }}" class="btn btn-primary">
            + Tambah Pengumuman
        </a>
    </div>

    {{-- Tabel daftar --}}
    <div class="card shadow-sm border-0 text-center">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0 align-middle">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>No</th>
                            <th>Judul</th>
                            <th>Tanggal</th>
                            <th>Utama</th>
                            <th>Gambar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($announcement as $item)
                            <tr>
                                <td class="p-4">
                                    {{ ($announcement->currentPage() - 1) * $announcement->perPage() + $loop->iteration }}
                                </td>
                                <td>{{ $item->title }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->date)->translatedFormat('d F Y') }}</td>
                                <td>
                                    @if($item->is_main)
                                        <span class="badge bg-success">Ya</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" 
                                             alt="Gambar" class="img-thumbnail" style="max-height: 60px;">
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="aksiDropdown{{ $item->id }}"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                            Aksi
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="aksiDropdown{{ $item->id }}">
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center text-info fw-semibold" 
                                                href="{{ route('announcement.show', $item->id) }}">
                                                    <i class="fa-solid fa-eye me-2 pr-4"></i> Detail
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center text-warning fw-semibold" 
                                                href="{{ route('announcement.edit', $item->id) }}">
                                                    <i class="fa-solid fa-pen-to-square me-2 pr-4"></i> Edit
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('announcement.destroy', $item->id) }}" method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus pengumuman ini?')" class="m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item d-flex align-items-center text-danger fw-semibold">
                                                        <i class="fa-solid fa-trash me-2 pr-4"></i> Hapus
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Belum ada pengumuman
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="card-footer">
            {{ $announcement->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endpush
