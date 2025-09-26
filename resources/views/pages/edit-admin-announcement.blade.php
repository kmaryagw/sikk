@extends('layouts.app')

@section('title', 'Edit Pengumuman')

@section('main')
<div class="container py-4">
    <h2 class="mb-4">Edit Pengumuman</h2>

    <form action="{{ route('announcement.update', $announcement->id) }}" 
          method="POST" 
          enctype="multipart/form-data" 
          class="card p-4 shadow-sm border-0">
        @csrf
        @method('PUT')

        {{-- Judul --}}
        <div class="mb-3">
            <label for="title" class="form-label">Judul</label>
            <input type="text" 
                   id="title" 
                   name="title" 
                   class="form-control @error('title') is-invalid @enderror"
                   value="{{ old('title', $announcement->title) }}" 
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
                   value="{{ old('date', \Carbon\Carbon::parse($announcement->date)->format('Y-m-d')) }}" 
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
                      rows="3" 
                      class="form-control @error('summary') is-invalid @enderror">{{ old('summary', $announcement->summary) }}</textarea>
            @error('summary') 
                <div class="invalid-feedback">{{ $message }}</div> 
            @enderror
        </div>

        {{-- Gambar --}}
        <div class="mb-3">
            <label for="image" class="form-label">Gambar</label>
            @if($announcement->image)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $announcement->image) }}" 
                         alt="Gambar saat ini" 
                         class="img-fluid rounded" 
                         style="max-height: 150px;">
                </div>
            @endif
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
                   {{ old('is_main', $announcement->is_main) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_main">Jadikan pengumuman utama</label>
        </div>

        {{-- Tombol --}}
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('announcement.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
