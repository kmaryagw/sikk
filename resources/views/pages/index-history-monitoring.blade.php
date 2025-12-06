@extends('layouts.app')
@section('title', 'Log Aktivitas')

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header"><h1>Log Aktivitas Monitoring</h1></div>
        
        <div class="card">
            <div class="card-header">
                <h4>Pilih Data Monitoring</h4>
                <div>
                    <form class="row g-2 align-items-center pl-5">
                        <div class="col-auto">
                            <input class="form-control" name="q" value="{{ $q }}" placeholder="Pencarian..." />
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-info"><i class="fa-solid fa-search"></i> Cari</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped text-center table-hover">
                        <tr>
                            <th>No</th>
                            <th>Prodi</th>
                            <th>Tahun</th>
                            <th>Aksi</th>
                        </tr>
                        @foreach ($monitoringikus as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->prodi->nama_prodi ?? '-' }}</td>
                            <td>{{ $item->tahunKerja->th_tahun }}</td>
                            <td>
                                <a href="{{ route('pages.index-list-indicators', $item->mti_id) }}" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-folder-open"></i> Buka Log
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            <div class="card-footer text-right">
                {{ $monitoringikus->links() }}
            </div>
        </div>
    </section>
</div>
@endsection