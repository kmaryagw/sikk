@extends('layouts.app')
@section('title','SPMI')

@push('style')
    <!-- CSS DataTables Standar -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    
    <style>
        .table td, .table th {
            vertical-align: middle !important; 
            text-align: center !important;   
            padding: 15px !important;        
        }

        .table td.text-left {
            text-align: left !important;
        }

        table.dataTable thead .sorting:before, 
        table.dataTable thead .sorting_asc:before, 
        table.dataTable thead .sorting_desc:before, 
        table.dataTable thead .sorting:after, 
        table.dataTable thead .sorting_asc:after, 
        table.dataTable thead .sorting_desc:after {
            bottom: 1em;
        }
        
        .dataTables_info {
            padding: 1rem;
            font-weight: 600;
        }
    </style>
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>
                Log untuk <span class="text-primary">Prodi {{ $Monitoringiku->prodi->nama_prodi }}</span> 
                &mdash; 
                <span>Tahun : {{ $Monitoringiku->tahunKerja->th_tahun }}</span>
            </h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('pages.index-history-monitoring') }}" class="btn btn-danger btn-icon icon-left">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Daftar Indikator Kinerja</h4>
                {{-- Form Pencarian & Filter --}}
                <div class="card-header-form">
                    <form method="GET">
                        <div class="d-flex align-items-center">
                            
                            <select name="unit_kerja" class="form-control mr-4" style="width: 200px;" onchange="this.form.submit()">
                                <option value="">Semua Unit Kerja</option>
                                @foreach($unitKerjas as $unit)
                                    <option value="{{ $unit->unit_id }}" 
                                        {{ $unitKerjaFilter == $unit->unit_id ? 'selected' : '' }}>
                                        {{ $unit->unit_nama }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="text" name="q" class="form-control mr-3" placeholder="Cari Indikator..." value="{{ $q }}">
                            <button class="btn btn-info">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card-body p-0"> 
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped m-0" id="table-indikator">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 5%">No</th>
                                <th style="width: 15%">Kode</th>
                                <th>Indikator Kinerja</th>
                                <th style="width: 15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($targetIndikators as $target)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $target->indikatorKinerja->ik_kode }}</td>
                                <td class="text-left">
                                    {{ $target->indikatorKinerja->ik_nama }}
                                    {{-- Opsional: Menampilkan Badge Unit Kerja kecil di bawah nama indikator --}}
                                    {{-- <br>
                                    @foreach($target->indikatorKinerja->unitKerja as $uk)
                                        <small class="badge badge-light text-muted mt-1">{{ $uk->unit_nama }}</small>
                                    @endforeach --}}
                                </td>
                                <td>
                                    <a href="{{ route('monitoringiku.history', ['mti_id' => $Monitoringiku->mti_id, 'ti_id' => $target->ti_id]) }}" 
                                    class="btn btn-primary btn-sm">
                                        <i class="fa-solid fa-clock-rotate-left"></i> Lihat Riwayat
                                    </a>
                                </td>
                            </tr>
                            @empty
                            {{-- Data kosong ditangani DataTable --}}
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#table-indikator').DataTable({
                "paging": false,       
                "lengthChange": false, 
                "searching": false,    
                "ordering": true,      
                "info": true,          
                "autoWidth": false,   
                "responsive": true,
                "order": [[ 1, "asc" ]], 
                "language": {
                    "emptyTable": "Data tidak ditemukan",
                    "info": "Menampilkan _TOTAL_ indikator", 
                    "infoEmpty": "Menampilkan 0 indikator",
                },
                "columnDefs": [
                    { "orderable": false, "targets": [3] } 
                ]
            });
        });
    </script>
@endpush