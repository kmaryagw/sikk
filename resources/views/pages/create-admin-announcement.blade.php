@extends('layouts.app')

@section('title', 'Tambah Pengumuman')

@section('main')
<div class="main-content section">
    <div class="section-header">
        <h2 class="mb-4 text-danger">Tambah Pengumuman Baru</h2>
    </div>

    <form action="{{ route('announcement.store') }}" method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm border-0">
        @csrf

        {{-- Judul --}}
        <div class="mb-3">
            <label for="title" class="form-label">Judul</label>
            <input type="text" 
                   id="title" 
                   name="title" 
                   class="form-control @error('title') is-invalid @enderror"
                   value="{{ old('title') }}" 
                   required>
            @error('title') 
                <div class="invalid-feedback">{{ $message }}</div> 
            @enderror
        </div>

        {{-- Tanggal --}}
        <div class="mb-3">
            <label for="date" class="form-label">Tanggal</label>
            <input type="date" 
                   id="date" 
                   name="date" 
                   class="form-control @error('date') is-invalid @enderror"
                   value="{{ old('date') }}" 
                   required>
            @error('date') 
                <div class="invalid-feedback">{{ $message }}</div> 
            @enderror
        </div>

        {{-- Ringkasan --}}
        <div class="mb-3">
            <label for="summary" class="form-label">Ringkasan</label>
            <textarea id="summary" 
                      name="summary" 
                      class="form-control @error('summary') is-invalid @enderror"
                      rows="3">{{ old('summary') }}</textarea>
            @error('summary') 
                <div class="invalid-feedback">{{ $message }}</div> 
            @enderror
        </div>

        {{-- Gambar --}}
        <div class="mb-3">
            <label for="image" class="form-label">Gambar</label>
            <input type="file" 
                   id="image" 
                   name="image" 
                   class="form-control @error('image') is-invalid @enderror"
                   accept="image/*">
            @error('image') 
                <div class="invalid-feedback">{{ $message }}</div> 
            @enderror
        </div>

        {{-- Centang utama --}}
        <div class="form-check mb-3">
            <input type="checkbox" 
                   id="is_main" 
                   name="is_main" 
                   value="1" 
                   class="form-check-input"
                   {{ old('is_main') ? 'checked' : '' }}>
            <label class="form-check-label" for="is_main">Jadikan pengumuman utama</label>
        </div>

        {{-- Tombol --}}
        <div class="d-flex">
            <button type="submit" class="btn btn-primary mr-3">Simpan</button>
            <a href="{{ route('announcement.index') }}" class="btn btn-danger">Batal</a>
        </div>
    </form>
</div>
@endsection
