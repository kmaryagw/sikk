@extends('layouts.app')

@section('title', 'Detail Monitoring IKU')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/bootstrap-daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">
    <link rel="stylesheet" href="{{ asset('library/datatables/media/css/jquery.dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('library/datatables/media/css/jquery.dataTables.min.css') }}">

     <style>
        .table-responsive {
            max-height: 50rem;   /* tinggi maksimum tabel */
            overflow-y: auto;    /* aktifkan scroll vertikal */
        }

        .table thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #f8f9fa !important; /* warna solid agar tidak transparan */
        }
    </style>
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Detail Monitoring Indikator Kinerja</h1>
            </div>
                <div class="card">
                    <div class="card-header">
                        <h4>Data Monitoring Indikator Kinerja dari Prodi: 
                            <span class="badge badge-info">{{ optional($monitoringiku->prodi)->nama_prodi ?? 'N/A' }}</span> 
                            Tahun: <span class="badge badge-primary">{{ optional($monitoringiku->tahunKerja)->th_tahun ?? 'N/A' }}</span>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('monitoringiku.store-detail', ['mti_id' => $monitoringiku->mti_id]) }}" method="POST">
                            @csrf
                
                            <div class="table-responsive">
                                <table id="table-indikator" class="table table-hover table-bordered table-striped table-sm m-0">
                                    <thead>
                                        <tr class="text-center">
                                            @if(Auth::user()->role == 'admin')
                                                <th width="3%">No</th>
                                                <th width="20%">Indikator</th>
                                                <th width="5%">Baseline</th>
                                                <th width="5%">Target</th>
                                                <th width="12%">Jenis Ketercapaian</th>
                                                <th width="5%">URL</th>
                                                <th width="6.6%">Capaian</th>
                                                {{-- <th width="10%">Status</th> --}}
                                                <th width="10%">Keterangan</th>
                                                <th>Evaluasi</th>
                                                <th>Tindak Lanjut</th>
                                                <th>Peningkatan</th>
                                            @else
                                                <th width="3%">No</th>
                                                <th width="25%">Indikator</th>
                                                <th width="5%">Baseline</th>
                                                <th width="5%">Target</th>
                                                <th width="15%">Jenis Ketercapaian</th>
                                                <th width="10%">Capaian</th>
                                                {{-- <th width="10%">Status</th> --}}
                                                <th width="15%">Keterangan</th>
                                                <th width="10%">URL</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $no = 1;
                                            $isAdmin = Auth::user()->role === 'admin';
                                        @endphp
                                        
                                        @foreach ($targetIndikator as $indikator)
                                            @php
                                                $detail = $monitoringikuDetail->where('ti_id', $indikator->ti_id)->first();
                                                $indikatorKinerja = optional($indikator->indikatorKinerja);
                                                $idx = $loop->index;
                                                // Ambil nilai baseline dan target
                                                $baselineValue = optional($indikator->baselineTahun)->baseline;
                                                $targetValue   = $indikator->ti_target;                                                
                                                $isTargetMissing = ($targetValue === null || trim((string)$targetValue) === '');
                                                $isBaselineMissing = ($baselineValue === null || trim((string)$baselineValue) === '');

                                                // Kunci form jika Baseline ATAU Target kosong.
                                                $isLocked = $isTargetMissing || $isBaselineMissing;

                                                // Ambil data lama (old input) atau data dari database
                                                $capaianValue = old("mtid_capaian.$idx", $detail->mtid_capaian ?? '');
                                            @endphp
                                            
                                            <tr>
                                                <td class="text-center">{{ $no++ }}</td>

                                                {{-- Kolom Indikator --}}
                                                @if($isAdmin)
                                                    <td class="text-justify">
                                                        {{ $indikatorKinerja->ik_kode ?? 'N/A' }} - {{ $indikatorKinerja->ik_nama ?? 'N/A' }}
                                                    </td>
                                                @else
                                                    <td>
                                                        {{ $indikatorKinerja->ik_kode ?? 'N/A' }} - {{ $indikatorKinerja->ik_nama ?? 'N/A' }}
                                                    </td>
                                                @endif

                                                {{-- Kolom Baseline & Target --}}
                                                <td class="text-center">{{ $baselineValue !== null && $baselineValue !== '' ? $baselineValue : '-' }}</td>
                                                <td class="text-center">{{ $targetValue !== null && $targetValue !== '' ? $targetValue : '-' }}</td>

                                                {{-- Kolom Jenis Ketercapaian --}}
                                                <td>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                @php
                                                                    $jenis = strtolower($indikatorKinerja->ik_ketercapaian ?? '');
                                                                    $icon = match ($jenis) {
                                                                        'nilai' => 'fa-solid fa-chart-line',
                                                                        'persentase' => 'fa-solid fa-percent',
                                                                        'rasio' => 'fa-solid fa-divide',
                                                                        'status' => 'fa-solid fa-flag-checkered',
                                                                        'ada/tidak' => 'fa-solid fa-toggle-on',
                                                                        'ketersediaan' => 'fa-solid fa-box-open',
                                                                        default => 'fa-solid fa-info-circle',
                                                                    };
                                                                @endphp
                                                                <i class="{{ $icon }}"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" class="form-control text-capitalize"
                                                            value="{{ $indikatorKinerja->ik_ketercapaian ?? '-' }}" readonly>
                                                        {{-- Hidden input untuk jenis ketercapaian --}}
                                                        <input type="hidden" name="mtid_ketercapaian[{{ $idx }}]"
                                                            value="{{ $indikatorKinerja->ik_ketercapaian }}">
                                                    </div>
                                                </td>

                                                {{-- AREA ADMIN --}}
                                                @if($isAdmin)
                                                    <td>
                                                        <input type="url" class="form-control" style="max-width:200px"
                                                            name="mtid_url[{{ $idx }}]"
                                                            value="{{ old("mtid_url.$idx", $detail->mtid_url ?? '') }}" readonly>
                                                    </td>
                                                    <td>
                                                        {{-- ID Indikator wajib dikirim --}}
                                                        <input type="hidden" name="ti_id[{{ $idx }}]" value="{{ $indikator->ti_id }}">

                                                        {{-- Tampilan Capaian (Read Only untuk Admin) --}}
                                                        @if(in_array(strtolower($indikatorKinerja->ik_ketercapaian), ['nilai', 'persentase']))
                                                            <input type="number" class="form-control" style="max-width:150px"
                                                                name="mtid_capaian[{{ $idx }}]" step="any"
                                                                value="{{ $capaianValue }}" readonly>
                                                        @elseif(strtolower($indikatorKinerja->ik_ketercapaian) === 'ketersediaan')
                                                            <select name="mtid_capaian[{{ $idx }}]" class="form-control" style="max-width:150px" disabled>
                                                                <option value="" {{ $capaianValue ? '' : 'selected' }}>Pilih</option>
                                                                <option value="ada"   {{ $capaianValue == 'ada' ? 'selected' : '' }}>Ada</option>
                                                                <option value="draft" {{ $capaianValue == 'draft' ? 'selected' : '' }}>Draft</option>
                                                            </select>
                                                            <input type="hidden" name="mtid_capaian[{{ $idx }}]" value="{{ $capaianValue }}">
                                                        @else
                                                            <input type="text" class="form-control" style="max-width:100px"
                                                                name="mtid_capaian[{{ $idx }}]" value="{{ $capaianValue }}" readonly>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        <textarea class="form-control" rows="3" style="max-width:200px"
                                                            name="mtid_keterangan[{{ $idx }}]" readonly>{{ old("mtid_keterangan.$idx", $detail->mtid_keterangan ?? '') }}</textarea>
                                                    </td>
                                                    
                                                    {{-- Form Evaluasi Admin (Aktif hanya jika capaian sudah diisi user) --}}
                                                    <td>
                                                        <textarea class="form-control" rows="3" style="max-width:200px"
                                                            name="mtid_evaluasi[{{ $idx }}]"
                                                            @if(empty($detail->mtid_capaian)) disabled @endif>{{ old("mtid_evaluasi.$idx", $detail->mtid_evaluasi ?? '') }}</textarea>
                                                    </td>

                                                    <td>
                                                        <textarea class="form-control" rows="3" style="max-width:200px"
                                                            name="mtid_tindaklanjut[{{ $idx }}]"
                                                            @if(empty($detail->mtid_capaian)) disabled @endif>{{ old("mtid_tindaklanjut.$idx", $detail->mtid_tindaklanjut ?? '') }}</textarea>
                                                    </td>

                                                    <td>
                                                        <textarea class="form-control" rows="3" style="max-width:200px"
                                                            name="mtid_peningkatan[{{ $idx }}]"
                                                            @if(empty($detail->mtid_capaian)) disabled @endif>{{ old("mtid_peningkatan.$idx", $detail->mtid_peningkatan ?? '') }}</textarea>
                                                    </td>

                                                @else
                                                    {{-- AREA USER (PRODI/UNIT) --}}
                                                    <td>
                                                        <input type="hidden" name="ti_id[{{ $idx }}]" value="{{ $indikator->ti_id }}">

                                                        {{-- INPUT CAPAIAN --}}
                                                        @if(in_array(strtolower($indikatorKinerja->ik_ketercapaian), ['nilai', 'persentase']))
                                                            <input type="number" class="form-control" style="max-width:150px"
                                                                name="mtid_capaian[{{ $idx }}]" step="any"
                                                                value="{{ $capaianValue }}" @if($isLocked) readonly @endif>

                                                        @elseif(strtolower($indikatorKinerja->ik_ketercapaian) === 'ketersediaan')
                                                            <select name="mtid_capaian[{{ $idx }}]" class="form-control" style="max-width:150px"
                                                                    @if($isLocked) disabled @endif>
                                                                <option value="" {{ $capaianValue ? '' : 'selected' }}>Pilih</option>
                                                                <option value="ada"   {{ $capaianValue == 'ada' ? 'selected' : '' }}>Ada</option>
                                                                <option value="draft" {{ $capaianValue == 'draft' ? 'selected' : '' }}>Draft</option>
                                                            </select>
                                                            
                                                            {{-- FIX UTAMA: Jika disabled, kirim value ASLI (bukan kosong) agar data tidak hilang --}}
                                                            @if($isLocked)
                                                                <input type="hidden" name="mtid_capaian[{{ $idx }}]" value="{{ $capaianValue }}">
                                                            @endif

                                                        @else
                                                            {{-- Rasio / Lainnya --}}
                                                            <input type="text" class="form-control" style="max-width:100px"
                                                                name="mtid_capaian[{{ $idx }}]" value="{{ $capaianValue }}"
                                                                @if($isLocked) readonly @endif>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        <textarea class="form-control" rows="3" style="max-width:200px"
                                                            name="mtid_keterangan[{{ $idx }}]"
                                                            @if($isLocked) readonly @endif>{{ old("mtid_keterangan.$idx", $detail->mtid_keterangan ?? '') }}</textarea>
                                                    </td>

                                                    <td>
                                                        <input type="url" class="form-control" style="max-width:200px"
                                                            name="mtid_url[{{ $idx }}]"
                                                            value="{{ old("mtid_url.$idx", $detail->mtid_url ?? '') }}"
                                                            placeholder="https://..."
                                                            @if($isLocked) readonly @endif>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3 text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-save"></i> Simpan
                                </button>
                                <a href="{{ route('monitoringiku.index-detail', $monitoringiku->mti_id) }}" class="btn btn-danger">
                                    <i class="fa-solid fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('library/cleave.js/dist/cleave.min.js') }}"></script>
    <script src="{{ asset('library/cleave.js/dist/addons/cleave-phone.us.js') }}"></script>
    <script src="{{ asset('library/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('library/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}"></script>
    <script src="{{ asset('library/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ asset('library/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('library/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>
    
    <script src="{{ asset('library/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('library/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('library/datatables/media/js/jquery.dataTables.js') }}"></script>
    @include('sweetalert::alert')

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/forms-advanced-forms.js') }}"></script>
    <script>
        $(document).ready(function () {
            $("#table-indikator").DataTable({
                "columnDefs": [
                    { "sortable": false, "targets": [4, 5, 6] }
                ],
                "paging": false, 
                "order": [[1, 'asc']],
                info: true,
                infoCallback: function(settings, start, end, max, total, pre) {
                    return `
                        <span class="badge bg-primary text-light px-3 py-2 m-3">
                            Total Data : ${total}
                        </span>
                    `;
                }
            });
        });
    </script>
@endpush