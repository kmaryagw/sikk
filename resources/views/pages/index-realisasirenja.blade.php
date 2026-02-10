@extends('layouts.app')
@section('title','SPMI')

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
                        <select class="form-control" name="tahun">
                            <option value="" disabled selected>Pilih Tahun</option>
                            @foreach ($tahuns as $tahun)
                                <option value="{{ $tahun->th_id }}" {{ request('tahun') == $tahun->th_id ? 'selected' : '' }}>
                                    {{ $tahun->th_tahun }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <select class="form-control" name="unit_id">
                            <option value="">Semua Unit Kerja</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->unit_id }}" {{ request('unit_id') == $unit->unit_id ? 'selected' : '' }}>
                                    {{ $unit->unit_nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <select class="form-control" name="prodi">
                            <option value="">Semua Program Studi</option>
                            @foreach ($prodis as $prodi)
                                <option value="{{ $prodi->prodi_id }}" {{ request('prodi') == $prodi->prodi_id ? 'selected' : '' }}>
                                    {{ $prodi->nama_prodi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-info"><i class="fa-solid fa-search"></i> Cari</button>
                    </div>
                </form>
            </div>

            <div class="table-responsive text-center">
                <table class="table table-hover table-bordered table-striped m-0">
                    <thead>
                        <tr>
                            <th style="width : 1%">No</th>
                            <th style="width : 1%">Tahun</th>
                            <th style="width : 10%">Program Studi</th>
                            <th style="width : 35%">Indikator Kinerja</th>
                            <th style="width : 15%">Program Kerja</th>
                            <th style="width : 10%">Unit Kerja</th>
                            <th style="width : 15%">Periode Monev</th>
                            <th style="width : 3%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = $rencanaKerjas->firstItem(); @endphp
                        @foreach ($rencanaKerjas as $rencanaKerja)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $rencanaKerja->tahunKerja->th_tahun ?? '-' }}</td>
                                <td>
                                    @if($rencanaKerja->programStudis->isNotEmpty())
                                        <ul class="list-unstyled">
                                            @foreach ($rencanaKerja->programStudis as $prodi)
                                                <li class="my-2">{{ $prodi->nama_prodi }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted">Tidak ada Program Studi</span>
                                    @endif
                                </td>
                                <td>
                                    @if($rencanaKerja->targetindikators->isNotEmpty())
                                        <ul class="list-unstyled">
                                            @foreach ($rencanaKerja->targetindikators as $iku)
                                                <li class="my-2" style="padding: 1.5rem;">{{ $iku->indikatorKinerja->ik_kode }} - {{ $iku->indikatorKinerja->ik_nama }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted">Tidak ada Indikator Kinerja</span>
                                    @endif
                                </td>
                                <td>{{ $rencanaKerja->rk_nama }}</td>
                                <td>{{ $rencanaKerja->UnitKerja->unit_nama ?? '-' }}</td>
                            
                                <td>
                                    @if($rencanaKerja->periodes->isNotEmpty())
                                        @foreach ($rencanaKerja->periodes as $periode)
                                            <span class="badge badge-info">{{ $periode->pm_nama }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">Tidak ada periode</span>
                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-warning" href="{{ route('realisasirenja.showRealisasi', $rencanaKerja->rk_id) }}">
                                        <i class="fa-solid fa-pen-to-square"></i> Realisasi
                                    </a>
                                </td>
                            </tr>
                        @endforeach

                        @if ($rencanaKerjas->isEmpty())
                            @php
                                $tahunText = $tahuns->firstWhere('th_id', request('tahun'))?->th_tahun ?? null;
                                $unitText = $units->firstWhere('unit_id', request('unit_id'))?->unit_nama ?? null;
                                $prodiText = $prodis->firstWhere('prodi_id', request('prodi'))?->nama_prodi ?? null;
                            @endphp
                            <tr>
                                <td colspan="12">
                                    Tidak ada data
                                    @if ($unitText)
                                        untuk <strong>Unit Kerja {{ $unitText }}</strong>
                                    @endif
                                    @if ($prodiText)
                                        @if ($unitText), @endif
                                        <strong>Program Studi {{ $prodiText }}</strong>
                                    @endif
                                    @if ($tahunText)
                                        di Tahun <strong>{{ $tahunText }}</strong>
                                    @endif
                                </td>
                            </tr>
                        @endif
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