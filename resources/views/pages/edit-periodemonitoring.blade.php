@extends('layouts.app')
@section('title', 'Edit Periode Monitoring')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit Periode Monitoring</h1>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('periode-monitoring.update', $periodeMonitoring->pmo_id) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="th_id">Tahun</label>
                            <select name="th_id" id="th_id" class="form-control @error('th_id') is-invalid @enderror">
                                <option value="">-- Pilih Tahun --</option>
                                @foreach ($tahuns as $tahun)
                                    <option value="{{ $tahun->th_id }}" {{ old('th_id', $periodeMonitoring->th_id) == $tahun->th_id ? 'selected' : '' }}>
                                        {{ $tahun->th_tahun }}
                                    </option>
                                @endforeach
                            </select>
                            @error('th_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="pm_id">Periode Monev</label>
                            <select name="pm_id" id="pm_id" class="form-control @error('pm_id') is-invalid @enderror">
                                <option value="">-- Pilih Periode --</option>
                                @foreach ($periodes as $periode)
                                    <option value="{{ $periode->pm_id }}" {{ old('pm_id', $periodeMonitoring->pm_id) == $periode->pm_id ? 'selected' : '' }}>
                                        {{ $periode->pm_nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pm_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="pmo_tanggal_mulai">Tanggal Mulai</label>
                            <input type="datetime-local" name="pmo_tanggal_mulai" id="pmo_tanggal_mulai" class="form-control @error('pmo_tanggal_mulai') is-invalid @enderror" value="{{ old('pmo_tanggal_mulai', \Carbon\Carbon::parse($periodeMonitoring->pmo_tanggal_mulai)->format('Y-m-d\TH:i')) }}">
                            @error('pmo_tanggal_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="pmo_tanggal_selesai">Tanggal Selesai</label>
                            <input type="datetime-local" name="pmo_tanggal_selesai" id="pmo_tanggal_selesai" class="form-control @error('pmo_tanggal_selesai') is-invalid @enderror" value="{{ old('pmo_tanggal_selesai', \Carbon\Carbon::parse($periodeMonitoring->pmo_tanggal_selesai)->format('Y-m-d\TH:i')) }}">
                            @error('pmo_tanggal_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ url('periode-monitoring') }}" class="btn btn-danger">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
