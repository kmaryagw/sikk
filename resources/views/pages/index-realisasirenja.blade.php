@extends('layouts.app')
@section('title', 'Realisasi Renja')

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Daftar Rencana Kerja</h1>
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
                    @if (Auth::user()->role== 'admin')
                    <div class="col-auto">
                        <a class="btn btn-primary" href="{{ route('realisasirenja.create') }}"><i class="fa-solid fa-plus"></i> Tambah</a>
                    </div>
                    @endif
                </form>
            </div>

            <div class="table-responsive text-center">
                <table class="table table-hover table-bordered table-striped m-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Program Kerja</th>
                            <th>Unit Kerja</th>
                            <th>Tahun</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = $rencanaKerjas->firstItem(); @endphp
                        @foreach ($rencanaKerjas as $rencanaKerja)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $rencanaKerja->rk_nama }}</td>
                                <td>{{ $rencanaKerja->UnitKerja->unit_nama ?? '-' }}</td>
                                <td>{{ $rencanaKerja->tahun_kerja->th_tahun ?? '-' }}</td>
                                <td>
                                    <a class="btn btn-warning" href="{{ route('realisasirenja.showRealisasi', $rencanaKerja->rk_id) }}">
                                        <i class="fa-solid fa-pen-to-square"></i> Realisasi
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($rencanaKerjas->hasPages())
                <div class="card-footer">
                    {{ $rencanaKerjas->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </section>
</div>
@endsection