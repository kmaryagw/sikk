@extends('layouts.app')

@section('title','SPMI')

@section('main')
<div class="main-content section">
    
    <div class="section-header">
        <h1>Detail Pengumuman</h1>
        {{-- <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('announcement.index') }}">Pengumuman</a></div>
            <div class="breadcrumb-item">Detail</div>
        </div> --}}
    </div>

    <div class="section-body">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                
                <div class="card card-primary shadow">
                    
                    @if($announcement->image)
                        <div style="height: 350px; overflow: hidden; border-top-left-radius: 4px; border-top-right-radius: 4px;">
                            <img src="{{ asset('storage/' . $announcement->image) }}" 
                                 alt="{{ $announcement->title }}" 
                                 class="w-100" 
                                 style="width: 100%; height: 100%; object-fit: cover; object-position: center;">
                        </div>
                    @endif

                    <div class="card-body pt-4 pb-4 px-5">
                        
                        <div class="d-flex align-items-center mb-3">
                            @if($announcement->is_main)
                                <span class="badge badge-success px-3 py-2 mr-2">
                                    <i class="fas fa-star mr-1"></i> Utama
                                </span>
                            @else
                                <span class="badge badge-secondary px-3 py-2 mr-2">
                                    <i class="fas fa-info-circle mr-1"></i> Biasa
                                </span>
                            @endif

                            <span class="text-muted small">
                                <i class="far fa-calendar-alt mr-1"></i> 
                                {{ \Carbon\Carbon::parse($announcement->date)->translatedFormat('d F Y') }}
                            </span>
                        </div>

                        <h2 class="font-weight-bold text-dark mb-4" style="line-height: 1.3;">
                            {{ $announcement->title }}
                        </h2>

                        <hr>

                        <div class="content-text text-secondary mt-4">
                            <div style="font-size: 16px; line-height: 1.8;">
                                @if($announcement->summary)
                                    {!! nl2br(e($announcement->summary)) !!}
                                @else
                                    <p class="text-muted font-italic text-center my-5">
                                        Tidak ada deskripsi lengkap.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-whitesmoke text-right">
                        <a href="{{ route('announcement.index') }}" class="btn btn-secondary mr-2">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                        
                        <div class="d-inline-block">
                            <a href="{{ route('announcement.edit', $announcement->id) }}" class="btn btn-warning mr-1">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </a>
                            
                            <form action="{{ route('announcement.destroy', $announcement->id) }}" 
                                  method="POST" 
                                  class="d-inline"
                                  onsubmit="return confirm('Yakin ingin menghapus pengumuman ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash-alt mr-1"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection