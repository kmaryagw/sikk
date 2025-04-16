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
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Detail Monitoring IKU</h1>
            </div>
                <div class="card">
                    <div class="card-header">
                        <h4>Data Monitoring IKU dari Prodi: 
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
                                            <th width="3%">No</th>
                                            <th width="20%">Indikator</th>
                                            <th width="5%">Baseline</th>
                                            <th width="5%">Target</th>
                                            <th width="15%">Jenis Ketercapaian</th>
                                            <th width="5%">Capaian</th>
                                            <th width="10%">Status</th>
                                            <th width="15%">Keterangan</th>
                                            <th width="10%">URL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $no = 1; @endphp
                                        @foreach ($targetIndikator as $indikator)
                                            @php
                                                $detail = $monitoringikuDetail->where('ti_id', $indikator->ti_id)->first();
                                                $indikatorKinerja = optional($indikator->indikatorKinerja);
                                            @endphp
                                            <tr>
                                                <td class="text-center">{{ $no++ }}</td>
                                                <td>
                                                    {{ $indikatorKinerja->ik_kode ?? 'N/A' }} - {{ $indikatorKinerja->ik_nama ?? 'N/A' }}
                                                </td>
                                                <td class="text-center">{{ $indikatorKinerja->ik_baseline ?? 'N/A' }}</td>
                                                <td class="text-center">{{ $indikator->ti_target ?? 'N/A' }}</td>
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
                                                                        default => 'fa-solid fa-info-circle',
                                                                    };
                                                                @endphp
                                                                <i class="{{ $icon }}"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" class="form-control text-capitalize" value="{{ $indikatorKinerja->ik_ketercapaian ?? '-' }}" readonly>
                                                        <input type="hidden" name="mtid_ketercapaian[]" value="{{ $indikatorKinerja->ik_ketercapaian }}">
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="hidden" name="ti_id[]" value="{{ $indikator->ti_id }}">
                                                    @if(in_array($indikatorKinerja->ik_ketercapaian, ['nilai', 'persentase']))
                                                        <input type="number" class="form-control" style="max-width: 100px;" 
                                                               name="mtid_capaian[]" step="any" 
                                                               value="{{ old('mtid_capaian.' . $loop->index, $detail->mtid_capaian ?? '') }}">
                                                    @else
                                                        <input type="text" class="form-control" style="max-width: 100px;" 
                                                               name="mtid_capaian[]" 
                                                               value="{{ old('mtid_capaian.' . $loop->index, $detail->mtid_capaian ?? '') }}">
                                                    @endif
                                                </td>
                                               
                                                <td>
                                                    <select name="mtid_status[]" class="form-control select2" style="max-width: 150px;">
                                                        <option value="">-- Pilih Status --</option>
                                                        <option value="tercapai" 
                                                            {{ old('mtid_status.' . $loop->index, $detail->mtid_status ?? '') == 'tercapai' ? 'selected' : '' }}>Tercapai
                                                        </option>
                                                        <option value="tidak tercapai" 
                                                            {{ old('mtid_status.' . $loop->index, $detail->mtid_status ?? '') == 'tidak tercapai' ? 'selected' : '' }}>Tidak Tercapai
                                                        </option>
                                                        <option value="tidak terlaksana" 
                                                            {{ old('mtid_status.' . $loop->index, $detail->mtid_status ?? '') == 'tidak terlaksana' ? 'selected' : '' }}>Tidak Terlaksana
                                                        </option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" style="max-width: 200px;" 
                                                           name="mtid_keterangan[]" 
                                                           value="{{ old('mtid_keterangan.' . $loop->index, $detail->mtid_keterangan ?? '') }}">
                                                </td>
                                                <td>
                                                    <input type="url" class="form-control" style="max-width: 200px;" 
                                                           name="mtid_url[]" 
                                                           value="{{ old('mtid_url.' . $loop->index, $detail->mtid_url ?? '') }}">
                                                </td>
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
            });
        });
    </script>
@endpush