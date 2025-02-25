@extends('layouts.app')
@section('title', 'Target Capaian')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
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
                                        {{ request('prodi') == $prodi->prodi_id ? 'selected' : '' }} 
                                        {{ Auth::user()->prodi_id == $prodi->prodi_id ? 'selected' : '' }}>
                                        {{ $prodi->nama_prodi }}
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
                            <a class="btn btn-primary" href="{{ route('targetcapaianprodi.create') }}"><i class="fa-solid fa-plus"></i> Tambah/Update</a>
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
                                <th>Indikator Kinerja</th>
                                <th>Jenis</th>
                                <th>Nilai Baseline</th>
                                <th>Target Capaian</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = $target_capaians->firstItem(); @endphp
                            @foreach ($target_capaians as $targetcapaian)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $targetcapaian->th_tahun }}</td>
                                    <td>{{ $targetcapaian->nama_prodi }}</td>
                                    <td>{{ $targetcapaian->ik_kode }} - {{ $targetcapaian->ik_nama }}</td>
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
                                        @if ($targetcapaian->ik_ketercapaian == 'persentase' && is_numeric($targetcapaian->ik_baseline))
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: {{ intval($targetcapaian->ik_baseline) }}%;" 
                                                     aria-valuenow="{{ intval($targetcapaian->ik_baseline) }}" 
                                                     aria-valuemin="0" aria-valuemax="100">
                                                    {{ $targetcapaian->ik_baseline }}%
                                                </div>
                                            </div>
                                        @elseif ($targetcapaian->ik_ketercapaian == 'nilai' && is_numeric($targetcapaian->ik_baseline))
                                            <span class="badge badge-primary">{{ $targetcapaian->ik_baseline }}</span>
                                        @elseif (in_array(strtolower($targetcapaian->ik_baseline), ['ada', 'draft']))
                                            @if (strtolower($targetcapaian->ik_baseline) === 'ada')
                                                <span class="text-success"><i class="fa-solid fa-check-circle"></i> Ada</span>
                                            @else
                                                <span class="text-warning"><i class="fa-solid fa-info-circle"></i> Draft</span>
                                            @endif
                                        @else
                                            {{ $targetcapaian->ik_baseline }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($targetcapaian->ik_ketercapaian == 'persentase' && is_numeric($targetcapaian->ti_target))
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: {{ intval($targetcapaian->ti_target) }}%;" 
                                                     aria-valuenow="{{ intval($targetcapaian->ti_target) }}" 
                                                     aria-valuemin="0" aria-valuemax="100">
                                                    {{ $targetcapaian->ti_target }}%
                                                </div>
                                            </div>
                                        @elseif ($targetcapaian->ik_ketercapaian == 'nilai' && is_numeric($targetcapaian->ti_target))
                                            <span class="badge badge-primary">{{ $targetcapaian->ti_target }}</span>
                                        @elseif (in_array(strtolower($targetcapaian->ti_target), ['ada', 'draft']))
                                            @if (strtolower($targetcapaian->ti_target) === 'ada')
                                                <span class="text-success"><i class="fa-solid fa-check-circle"></i> Ada</span>
                                            @else
                                                <span class="text-warning"><i class="fa-solid fa-info-circle"></i> Draft</span>
                                            @endif
                                        @else
                                            {{ $targetcapaian->ti_target }}
                                        @endif
                                    </td>                                                                       
                                    <td>{{ $targetcapaian->ti_keterangan }}</td>

                                    @if (Auth::user()->role== 'admin' || Auth::user()->role == 'prodi')
                                    <td>
                                        <a class="btn btn-warning btn-sm mb-2 mt-2" href="{{ route('targetcapaianprodi.edit', $targetcapaian->ti_id) }}"><i class="fa-solid fa-pen-to-square"></i> Ubah </a>
                                        <form id="delete-form-{{ $targetcapaian->ti_id }}" method="POST" class="d-inline" action="{{ route('targetcapaianprodi.destroy', $targetcapaian->ti_id) }}">

                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm" onclick="confirmDelete(event, '{{ $targetcapaian->ti_id }}' )"><i class="fa-solid fa-trash"></i> Hapus</button>

                                        </form>
                                    </td>
                                    @endif
                                </tr>
                            @endforeach
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