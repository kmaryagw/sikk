@extends('layouts.app')

@section('title', 'Laporan IKU/IKT')

@push('style')
    <!-- Tambahkan CSS Libraries jika diperlukan -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Laporan IKU/IKT</h1>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <form class="row g-2 align-items-center">
                    <div class="col-auto">
                        <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Pencarian..." />
                    </div>
                
                    {{-- @if (Auth::user()->role == 'admin' || Auth::user()->role == 'prodi') --}}
                
                        <div class="col-auto">
                            <select class="form-control" name="tahun">
                                <option value="">Pilih Tahun</option>
                                @foreach ($tahuns as $tahun)
                                    <option value="{{ $tahun->th_id }}" {{ request('tahun') == $tahun->th_id ? 'selected' : '' }}>
                                        {{ $tahun->th_tahun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    {{-- @endif --}}
                
                    <div class="col-auto">
                        <button class="btn btn-info"><i class="fa-solid fa-search"></i> Cari</button>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-success" onclick="exportToExcel()"><i class="fa-solid fa-file-excel"></i> Export Excel</button>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-danger" onclick="exportToPDF()"><i class="fa-solid fa-file-pdf"></i> Export PDF</button>
                    </div>
                </form>                
            </div>

            <div class="table-responsive text-center">
                <table class="table table-hover table-bordered table-striped m-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Indikator Kinerja</th>
                            <th>Target Capaian</th>
                            <th>Keterangan</th>
                            <th>Prodi</th>
                            <th>Tahun</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = $target_capaians->firstItem(); @endphp
                        @foreach ($target_capaians as $targetcapaian)
                            <tr>
                                <td>{{ $no++ }}</td>
                                    <td>{{ $targetcapaian->ik_nama }}</td>
                                    <td>{{ $targetcapaian->ti_target }}</td>
                                    <td>{{ $targetcapaian->ti_keterangan }}</td>
                                    <td>{{ $targetcapaian->nama_prodi }}</td>
                                    <td>{{ $targetcapaian->th_tahun }}</td>
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
    <!-- JS Libraries jika diperlukan -->
    <script src="{{ asset('library/summernote/dist/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('library/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>

    <script>
        function exportToExcel() {
            // Fungsi untuk melakukan export ke Excel
            window.location.href = "#"; // Pastikan route disesuaikan dengan route yang Anda buat
        }

        function exportToPDF() {
            // Fungsi untuk melakukan export ke PDF
            window.location.href = "#}"; // Pastikan route disesuaikan dengan route yang Anda buat
        }
    </script>
@endpush