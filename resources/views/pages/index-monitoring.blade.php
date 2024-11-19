@extends('layouts.app')
@section('title', 'Monitoring Periode')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Daftar Monitoring Periode</h1>
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
                        
                    </form>
                </div>

                <div class="table-responsive text-center">
                    <table class="table table-hover table-bordered table-striped m-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tahun</th>
                                <th>Periode Monev (Kuartal)</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal selesai </th> 
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($periodemonitorings as $item )
                                <tr>


                                    
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $item->tahunKerja->th_tahun }}</td>
                                    <td>
                                        @if($item->periodes->isNotEmpty())
                                            @foreach ($item->periodes as $periode)
                                                <span class="badge badge-info">{{ $periode->pm_nama }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">Tidak ada periode</span>
                                        @endif
                                    </td>
                                    
                                    

                                    
                                    
                                    <td>{{ \Carbon\Carbon::parse($item->pmo_tanggal_mulai)->format('Y-m-d H:i:s') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->pmo_tanggal_selesai)->format('Y-m-d H:i:s') }}</td>

                                    
                                    <td class="text-center">
                                        @if ($item->is_within_three_months)
                                            <!-- Tombol Isi Monitoring jika selisih <= 3 bulan -->
                                            <a class="btn btn-warning btn-sm" href="{{ route('monitoring.fill', $item->pmo_id) }}">
                                                <i class="fa-solid fa-pen-to-square"></i> Isi Monitoring
                                            </a>
                                        @else
                                            <!-- Tombol Lihat Monitoring jika selisih > 3 bulan -->
                                            <a class="btn btn-success btn-sm" href="{{ route('monitoring.show', $item->pmo_id) }}">
                                                <i class="fa-solid fa-eye"></i> Lihat Monitoring
                                            </a>
                                        @endif
                                    </td>
                                    
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if (count($periodemonitorings) > 0)
                    <div class="card-footer">
                        {{ $periodemonitorings->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('library/simpleweather/jquery.simpleWeather.min.js') }}"></script>
    <script src="{{ asset('library/chart.js/dist/Chart.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
    <script src="{{ asset('library/summernote/dist/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('library/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>
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
