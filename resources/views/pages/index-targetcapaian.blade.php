@extends('layouts.app')
@section('title', 'Target Capaian')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/circular-progress-bar.css') }}">
@endpush


@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Daftar Target Capaian</h1>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <form class="row g-2 align-items-center">
                        @if (Auth::user()->role== 'admin' || Auth::user()->role == 'prodi')
                        <div class="col-auto">
                            <select class="form-control" name="prodi">
                                <option value="">Semua Prodi</option>
                                @foreach ($prodis as $prodi)
                                    <option value="{{ $prodi->prodi_id }}" 
                                        {{ request('prodi') == $prodi->prodi_id ? 'selected' : '' }}>
                                        {{ $prodi->nama_prodi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif   
                        @if (Auth::user()->role== 'admin' || Auth::user()->role == 'prodi')
                        <div class="col-auto">
                            <select class="form-control" name="tahun">
                                <option value="">Semua Tahun</option>
                                @foreach ($tahun as $th)
                                    <option value="{{ $th->th_id }}" {{ request('tahun') == $th->th_id ? 'selected' : '' }}>
                                        {{ $th->th_tahun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif                                          
                        <div class="col-auto">
                            <input class="form-control" name="q" value="{{ $q }}" placeholder="Pencarian..." />
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-info"><i class="fa-solid fa-search"></i> Cari</button>
                        </div>
                        @if (Auth::user()->role== 'admin' || Auth::user()->role == 'prodi')
                        <div class="col-auto">
                            <a class="btn btn-primary" href="{{ route('targetcapaian.create') }}"><i class="fa-solid fa-plus"></i> Tambah Target Capaian</a>
                        </div>
                        @endif
                    </form>
                </div>  

                <div class="table-responsive text-center">
                    <table class="table table-hover table-bordered table-striped m-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tahun</th>
                                <th>Prodi</th>
                                <th style="width : 39%">Indikator Kinerja</th>
                                <th>Jenis</th>
                                <th>Nilai Baseline</th>
                                <th>Target Capaian</th>
                                <th>Keterangan</th>
                                {{-- <th>Aksi</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = $target_capaians->firstItem(); @endphp
                            @foreach ($target_capaians as $targetcapaian)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $targetcapaian->th_tahun }}</td>
                                    <td>{{ $targetcapaian->nama_prodi }}</td>
                                    <td style="padding: 1rem;">{{ $targetcapaian->ik_kode }} - {{ $targetcapaian->ik_nama }}</td>
                                    <td>
                                        @if (strtolower($targetcapaian->ik_jenis == 'IKU'))
                                            <span class="badge badge-success">IKU</span>
                                        @elseif (strtolower($targetcapaian->ik_jenis == 'IKT'))
                                            <span class="badge badge-primary">IKT</span>
                                        @else
                                            <span class="badge badge-secondary">Tidak Diketahui</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $ketercapaian = strtolower($targetcapaian->ik_ketercapaian);
                                            $baselineRaw = trim($targetcapaian->ik_baseline);
                                            $baselineValue = (float) str_replace('%', '', $baselineRaw);
                                            $progressColor = $baselineValue == 0 ? '#dc3545' : '#28a745';
                                        @endphp
                                    
                                        @if ($ketercapaian === 'persentase' && is_numeric($baselineValue))
                                            <div class="ring-progress-wrapper">
                                                <div class="ring-progress" style="--value: {{ $baselineValue }}; --progress-color: {{ $progressColor }};">
                                                    <div class="ring-inner">
                                                        <span class="ring-text">{{ $baselineValue }}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif ($ketercapaian === 'nilai' && is_numeric($baselineRaw))
                                            <span class="badge badge-primary">{{ $baselineRaw }}</span>
                                        @elseif (in_array(strtolower($baselineRaw), ['ada', 'draft']))
                                            @if (strtolower($baselineRaw) === 'ada')
                                                <span class="text-success"><i class="fa-solid fa-check-circle"></i> Ada</span>
                                            @else
                                                <span class="text-warning"><i class="fa-solid fa-info-circle"></i> Draft</span>
                                            @endif
                                        @elseif ($ketercapaian === 'rasio')
                                            <span class="badge badge-info"><i class="fa-solid fa-balance-scale"></i> {{ $baselineRaw }}</span>
                                        @else
                                            {{ $baselineRaw }}
                                        @endif
                                    </td>                                    
                                    <td>
                                        @php
                                            $ketercapaian = strtolower($targetcapaian->ik_ketercapaian);
                                            $targetRaw = trim($targetcapaian->ti_target);
                                            $numericValue = (float) str_replace('%', '', $targetRaw);
                                            $progressColor = $numericValue == 0 ? '#dc3545' : '#28a745'; // Merah jika 0, hijau jika > 0
                                        @endphp
                                    
                                        @if ($ketercapaian === 'persentase' && is_numeric($numericValue))
                                            <div class="ring-progress-wrapper">
                                                <div class="ring-progress" style="--value: {{ $numericValue }}; --progress-color: {{ $progressColor }};">
                                                    <div class="ring-inner">
                                                        <span class="ring-text">{{ $numericValue }}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif ($ketercapaian === 'nilai' && is_numeric($targetRaw))
                                            <span class="badge badge-primary">{{ $targetRaw }}</span>
                                        @elseif (in_array(strtolower($targetRaw), ['ada', 'draft']))
                                            @if (strtolower($targetRaw) === 'ada')
                                                <span class="text-success"><i class="fa-solid fa-check-circle"></i> Ada</span>
                                            @else
                                                <span class="text-warning"><i class="fa-solid fa-info-circle"></i> Draft</span>
                                            @endif
                                        @elseif ($ketercapaian === 'rasio')
                                            @php
                                                $formattedRasio = 'Format salah';
                                                $cleaned = preg_replace('/\s*/', '', $targetRaw);
                                                if (preg_match('/^\d+:\d+$/', $cleaned)) {
                                                    [$left, $right] = explode(':', $cleaned);
                                                    $formattedRasio = $left . ' : ' . $right;
                                                }
                                            @endphp
                                            <span class="badge badge-info"><i class="fa-solid fa-balance-scale"></i> {{ $formattedRasio }} </span>
                                        @else
                                            {{ $targetRaw }}
                                        @endif
                                    </td>                                                                                                                                                                                                                      
                                    <td>{{ $targetcapaian->ti_keterangan }}</td>

                                    {{-- @if (Auth::user()->role== 'admin' || Auth::user()->role == 'prodi')
                                    <td>
                                        <a class="btn btn-warning btn-sm mb-2 mt-2" href="{{ route('targetcapaian.edit', $targetcapaian->ti_id) }}"><i class="fa-solid fa-pen-to-square"></i> Ubah </a>
                                        <form id="delete-form-{{ $targetcapaian->ti_id }}" method="POST" class="d-inline" action="{{ route('targetcapaian.destroy', $targetcapaian->ti_id) }}">

                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm" onclick="confirmDelete(event, '{{ $targetcapaian->ti_id }}' )"><i class="fa-solid fa-trash"></i> Hapus</button>

                                        </form>
                                    </td>
                                    @endif --}}
                                </tr>
                            @endforeach

                            @if ($target_capaians->isEmpty())
                                @php
                                    $tahunText = $tahun->firstWhere('th_id', $tahunId)?->th_tahun ?? 'ini';
                                    $prodiText = $prodis->firstWhere('prodi_id', $prodiId)?->nama_prodi ?? null;
                                @endphp
                                <tr>
                                    <td colspan="10" class="text-center alert alert-danger m-0">
                                        Prodi {{ $prodiText }}
                                        @if ($tahunText)
                                            di Tahun {{ $tahunText }}
                                        @endif
                                        tidak memiliki Target Capaian.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @if ($target_capaians->hasPages())
                    <div class="card-footer">
                        {{ $target_capaians->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraries -->
    <script src="{{ asset('library/simpleweather/jquery.simpleWeather.min.js') }}"></script>
    <script src="{{ asset('library/chart.js/dist/Chart.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
    <script src="{{ asset('library/summernote/dist/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('library/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/index-0.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function confirmDelete(event, formid) {
        event.preventDefault();
        Swal.fire({
            title: 'Apakah Anda yakin?',
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
        })
    }
</script>
@endpush