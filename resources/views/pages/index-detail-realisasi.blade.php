@extends('layouts.app')
@section('title', 'Detail Realisasi Renja')

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Detail Realisasi untuk {{ $rencanaKerja->rk_nama }}</h1>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <form class="row g-2 align-items-center" method="GET" action="{{ route('realisasirenja.showRealisasi', $rencanaKerja->rk_id) }}">
                    <div class="col-auto">
                        <input class="form-control" name="q" value="{{ request()->get('q') }}" placeholder="Pencarian..." />
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-info"><i class="fa-solid fa-search"></i> Cari</button>
                    </div>
                    @if (Auth::user()->role == 'admin')
                    <div class="col-auto">
                        <a class="btn btn-primary" href="{{ route('realisasirenja.create') }}"><i class="fa-solid fa-plus"></i> Tambah</a>
                    </div>
                    @endif
                </form>
            </div>

            {{-- <div class="card-body"> --}}
                @if($realisasi->isEmpty())
                    <p>Tidak ada data realisasi untuk rencana kerja ini.</p>
                @else
                    <div class="table-responsive text-center">
                        <table class="table table-hover table-bordered table-striped m-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Deskripsi</th>
                                    <th>Capaian</th>
                                    <th>Tanggal</th>
                                    <th>File</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach ($realisasi as $item)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $item->rkr_deskripsi }}</td>
                                        <td>{{ $item->rkr_capaian }}</td>
                                        <td>
                                            @if($item->rkr_tanggal instanceof \Carbon\Carbon)
                                                {{ $item->rkr_tanggal->format('d-m-Y') }}
                                            @else
                                                {{ $item->rkr_tanggal }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->rkr_file)
                                                <a href="{{ Storage::url($item->rkr_file) }}" target="_blank">Lihat File</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            {{-- </div> --}}

            {{-- @if ($realisasi->hasPages())
                <div class="card-footer">
                    {{ $realisasi->links('pagination::bootstrap-5') }}
                </div>
            @endif --}}
        </div>
    </section>
</div>
@endsection