@extends('layouts.app')

@section('title', 'Daftar Pengumuman')

@section('main')
<div class="container py-4">
    <h2 class="mb-4 text-white">Daftar Pengumuman</h2>

    {{-- Alert sukses --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Tombol tambah --}}
    <div class="mb-3">
        <a href="{{ route('announcement.create') }}" class="btn btn-primary">
            + Tambah Pengumuman
        </a>
    </div>

    {{-- Tabel daftar --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Judul</th>
                            <th>Tanggal</th>
                            <th>Utama</th>
                            <th>Gambar</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($announcement as $item)
                            <tr>
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
                                <td class="text-end">
                                    <a href="{{ route('announcement.show', $item->id) }}" class="btn btn-sm btn-info">
                                        Detail
                                    </a>
                                    <a href="{{ route('announcement.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                        Edit
                                    </a>
                                    <form action="{{ route('announcement.destroy', $item->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Yakin ingin menghapus pengumuman ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            Hapus
                                        </button>
                                    </form>
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
