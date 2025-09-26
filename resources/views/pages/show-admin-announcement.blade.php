@extends('layouts.app')

@section('title', 'Detail Pengumuman')

@section('main')
<div class="container py-4">
    <h2 class="mb-4 text-white">Detail Pengumuman</h2>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            {{-- Judul --}}
            <h4 class="fw-bold">{{ $announcement->title }}</h4>

            {{-- Tanggal --}}
            <p class="text-muted mb-1">
                {{ \Carbon\Carbon::parse($announcement->date)->translatedFormat('d F Y') }}
            </p>

            {{-- Status utama --}}
            @if($announcement->is_main)
                <span class="badge bg-success">Pengumuman Utama</span>
            @else
                <span class="badge bg-secondary">Biasa</span>
            @endif

            {{-- Gambar --}}
            @if($announcement->image)
                <div class="my-3">
                    <img src="{{ asset('storage/' . $announcement->image) }}" 
                         alt="Gambar Pengumuman" 
                         class="img-fluid rounded shadow-sm" 
                         style="max-height: 300px;">
                </div>
            @endif

            {{-- Ringkasan --}}
            <div class="mt-3">
                @if($announcement->summary)
                    <p>{{ $announcement->summary }}</p>
                @else
                    <p class="text-muted"><em>Tidak ada ringkasan</em></p>
                @endif
            </div>
        </div>

        {{-- Footer tombol --}}
        <div class="card-footer d-flex justify-content-between">
            <div>
                <a href="{{ route('announcement.edit', $announcement->id) }}" class="btn btn-warning">Edit</a>
                <form action="{{ route('announcement.destroy', $announcement->id) }}" 
                      method="POST" 
                      class="d-inline"
                      onsubmit="return confirm('Yakin ingin menghapus pengumuman ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
            <a href="{{ route('announcement.index') }}" class="btn btn-primary">Kembali</a>
        </div>
    </div>
</div>
@endsection
