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

    {{-- Tombol tambah --}}
    <div class="mb-3 text-end">
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
                                <td class="p-4">{{ $loop->iteration }}</td>
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
                                                <a class="dropdown-item" href="{{ route('announcement.show', $item->id) }}">
                                                    <i class="fa-solid fa-eye me-2"> </i> Detail
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('announcement.edit', $item->id) }}">
                                                    <i class="fa-solid fa-pen-to-square me-2"></i> Edit
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('announcement.destroy', $item->id) }}" method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus pengumuman ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fa-solid fa-trash me-2"> </i> Hapus
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
            {{ $announcement->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endpush
