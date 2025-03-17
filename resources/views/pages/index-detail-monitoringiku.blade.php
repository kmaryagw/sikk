@extends('layouts.app')
@section('title', 'Detail Monitoring IKU')

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="mb-0">Detail Program Studi</h1>
                    <a class="btn btn-danger" href="{{ route('monitoringiku.index') }}">
                        <i class="fa-solid fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Data Monitoring IKU dari Prodi: <span class="badge badge-info">{{ $Monitoringiku->targetIndikator->prodi->nama_prodi }}</span> Tahun: <span class="badge badge-primary">{{ $Monitoringiku->targetIndikator->tahunKerja->th_tahun }}</span></h4>
                @if (Auth::user()->role == 'admin' || Auth::user()->role == 'fakultas')
                    <a class="btn btn-primary" href="{{ route('monitoringiku.create-detail', ['mti_id' => $Monitoringiku->mti_id]) }}">
                        <i class="fa-solid fa-plus"></i> Isi/Ubah Semua
                    </a>
                @endif           
            </div>
        
            @if($targetIndikators->isEmpty())
                <div class="card-body text-center">
                    <p>Tidak ada target untuk prodi ini.</p>
                </div>
            @else
                <div class="table-responsive text-center">
                    <table class="table table-hover table-bordered table-striped m-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Indikator Kinerja</th>
                                <th>Baseline</th>
                                <th>Target</th>
                                <th>Keterangan Indikator</th>
                                <th>Capaian</th>
                                <th>Status</th>
                                <th>URL</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($targetIndikators as $target)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $target->indikatorKinerja->ik_kode }} - {{ $target->indikatorKinerja->ik_nama }}</td>
                                    <td>
                                        @if ($target->indikatorKinerja->ik_ketercapaian == 'persentase' && is_numeric($target->indikatorKinerja->ik_baseline))
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: {{ intval($target->indikatorKinerja->ik_baseline) }}%;" 
                                                     aria-valuenow="{{ intval($target->indikatorKinerja->ik_baseline) }}" 
                                                     aria-valuemin="0" aria-valuemax="100">
                                                    {{ $target->indikatorKinerja->ik_baseline }}%
                                                </div>
                                            </div>
                                        @elseif ($target->indikatorKinerja->ik_ketercapaian == 'nilai' && is_numeric($target->indikatorKinerja->ik_baseline))
                                            <span class="badge badge-primary">{{ $target->indikatorKinerja->ik_baseline }}</span>
                                        @elseif (in_array(strtolower($target->indikatorKinerja->ik_baseline), ['ada', 'draft']))
                                            @if (strtolower($target->indikatorKinerja->ik_baseline) === 'ada')
                                                <span class="text-success"><i class="fa-solid fa-check-circle"></i> Ada</span>
                                            @else
                                                <span class="text-warning"><i class="fa-solid fa-info-circle"></i> Draft</span>
                                            @endif
                                        @elseif ($target->indikatorKinerja->ik_ketercapaian == 'rasio')
                                            <span class="badge badge-info">{{ $target->indikatorKinerja->ik_baseline }}</span>
                                        @else
                                            {{ $target->indikatorKinerja->ik_baseline }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($target->indikatorKinerja->ik_ketercapaian == 'persentase' && is_numeric($target->ti_target))
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: {{ intval($target->ti_target) }}%;" 
                                                     aria-valuenow="{{ intval($target->ti_target) }}" 
                                                     aria-valuemin="0" aria-valuemax="100">
                                                    {{ $target->ti_target }}%
                                                </div>
                                            </div>
                                        @elseif ($target->indikatorKinerja->ik_ketercapaian == 'nilai' && is_numeric($target->ti_target))
                                            <span class="badge badge-primary">{{ $target->ti_target }}</span>
                                        @elseif (in_array(strtolower($target->ti_target), ['ada', 'draft']))
                                            @if (strtolower($target->ti_target) === 'ada')
                                                <span class="text-success"><i class="fa-solid fa-check-circle"></i> Ada</span>
                                            @else
                                                <span class="text-warning"><i class="fa-solid fa-info-circle"></i> Draft</span>
                                            @endif
                                        @elseif ($target->indikatorKinerja->ik_ketercapaian == 'rasio')
                                            <span class="badge badge-info">{{ $target->ti_target }}</span>
                                        @else
                                            {{ $target->ti_target }}
                                        @endif
                                    </td> 
                                    <td>{{ $target->ti_keterangan }}</td>  
                                    <td>
                                        @if(isset($target->indikatorKinerja->ik_ketercapaian) && $target->indikatorKinerja->ik_ketercapaian == 'persentase')
                                            @if(isset($target->monitoringDetail->mtid_capaian) && $target->monitoringDetail->mtid_capaian > 0)
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" 
                                                        style="width: {{ intval($target->monitoringDetail->mtid_capaian) }}%;" 
                                                        aria-valuenow="{{ intval($target->monitoringDetail->mtid_capaian) }}" 
                                                        aria-valuemin="0" aria-valuemax="100">
                                                        {{ intval($target->monitoringDetail->mtid_capaian) }}%
                                                    </div>
                                                </div>
                                            @else
                                                <span>Belum ada Capaian</span>
                                            @endif
                                        @elseif(isset($target->indikatorKinerja->ik_ketercapaian) && $target->indikatorKinerja->ik_ketercapaian == 'nilai')
                                            <span class="badge badge-primary">{{ $target->monitoringDetail->mtid_capaian ?? 'Belum ada Capaian' }}</span>
                                        @elseif(isset($target->monitoringDetail->mtid_capaian) && in_array(strtolower($target->monitoringDetail->mtid_capaian), ['ada', 'draft']))
                                            @if(strtolower($target->monitoringDetail->mtid_capaian) === 'ada')
                                                <span class="text-success"><i class="fa-solid fa-check-circle"></i> Ada</span>
                                            @else
                                                <span class="text-warning"><i class="fa-solid fa-info-circle"></i> Draft</span>
                                            @endif
                                        @elseif(isset($target->indikatorKinerja->ik_ketercapaian) && $target->indikatorKinerja->ik_ketercapaian == 'rasio')
                                            <span class="badge badge-info">{{ $target->monitoringDetail->mtid_capaian ?? 'Belum ada Capaian' }}</span>
                                        @else
                                            Belum ada Capaian
                                        @endif
                                    </td>                                                                
                                    <td>
                                        @php
                                            $status = isset($target->monitoringDetail->mtid_status) ? strtolower($target->monitoringDetail->mtid_status) : null;
                                        @endphp
                                    
                                        @if($status === 'tercapai')
                                            <span class="text-success"><i class="fa-solid fa-check-circle"></i> Tercapai</span>
                                        @elseif($status === 'tidak tercapai')
                                            <span class="text-warning"><i class="fa-solid fa-info-circle"></i> Tidak Tercapai</span>
                                        @elseif($status === 'tidak terlaksana')
                                            <span class="text-danger"><i class="fa-solid fa-times-circle"></i> Tidak Terlaksana</span>
                                        @else
                                            Belum ada Status
                                        @endif
                                    </td>
                                    
                                    {{-- <td>
                                        @if($target->monitoringDetail->mtid_url ?? 'Belum ada Capaian')
                                            <a href="{{ $target->monitoringDetail->mtid_url ?? 'Belum ada Capaian'}}" target="_blank" class="btn btn-sm btn-success">Lihat URL</a>
                                        @else
                                            Belum Ada URL
                                        @endif
                                    </td>   --}}
                                    <td>
                                        @if(isset($target->monitoringDetail->mtid_url) && $target->monitoringDetail->mtid_url)
                                            <a href="{{ $target->monitoringDetail->mtid_url }}" target="_blank" class="btn btn-sm btn-success">Lihat URL</a>
                                        @else
                                            Belum Ada URL
                                        @endif
                                    </td>
                                    
                                                                     
                                    <td class="text-center">
                                        <a href="{{ route('monitoringiku.edit-detail', ['mti_id' => $Monitoringiku->mti_id, 'ti_id' => $target->ti_id]) }}" class="btn btn-sm btn-warning"><i class="fa-solid fa-pen-to-square"></i> Isi/Ubah</a>                                      
                                    </td>
                                    
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>        
    </section>
</div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(event, formid) {
            event.preventDefault();
            Swal.fire({
                title: 'Yakin menghapus Data ini?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus data!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + formid).submit();
                }
            });
        }
    </script>
@endpush