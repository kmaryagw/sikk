@extends('layouts.app')
@section('title', 'Tambah Periode Monitoring')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Tambah Periode Monitoring</h1>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('periode-monitoring.store') }}">
                        @csrf
                        <div class="form-group">
                            <label for="th_id">Tahun</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fa-solid fa-calendar-alt"></i>
                                    </div>
                                </div>
                                <select name="th_id" id="th_id" class="form-control @error('th_id') is-invalid @enderror">
                                    <option value="">-- Pilih Tahun --</option>
                                    @foreach ($tahuns as $tahun)
                                        <option value="{{ $tahun->th_id }}" {{ old('th_id') == $tahun->th_id ? 'selected' : '' }}>
                                            {{ $tahun->th_tahun }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('th_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        


                        

                        <div class="form-group">
                            <label for="pm_id">Periode Monev</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fa-solid fa-calendar-alt"></i>
                                    </div>
                                </div>
                                <select name="pm_id" id="pm_id" class="form-control @error('pm_id') is-invalid @enderror">
                                    <option value="">-- Pilih Periode --</option>
                                    @foreach ($periodes as $periode)
                                        <option value="{{ $periode->pm_id }}" {{ old('pm_id') == $periode->pm_id ? 'selected' : '' }}>
                                            {{ $periode->pm_nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('pm_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="pmo_tanggal_mulai">Tanggal Mulai</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fa-solid fa-calendar-day"></i>
                                    </div>
                                </div>
                                <input type="date" name="pmo_tanggal_mulai" id="pmo_tanggal_mulai" class="form-control @error('pmo_tanggal_mulai') is-invalid @enderror" value="{{ old('pmo_tanggal_mulai') }}">
                                @error('pmo_tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="pmo_tanggal_selesai">Tanggal Selesai</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fa-solid fa-calendar-check"></i>
                                    </div>
                                </div>
                                <input type="date" name="pmo_tanggal_selesai" id="pmo_tanggal_selesai" class="form-control @error('pmo_tanggal_selesai') is-invalid @enderror" value="{{ old('pmo_tanggal_selesai') }}">
                                @error('pmo_tanggal_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="rk_id">Rencana Kerja</label>
                            <select name="rk_id" id="rk_id" class="form-control @error('rk_id') is-invalid @enderror">
                                <option value="">-- Pilih Rencana Kerja --</option>
                                @foreach ($RencanaKerja as $rk)
                                    <option value="{{ $rk->rk_id }}" {{ old('rk_id') == $rk->rk_id ? 'selected' : '' }}>
                                        {{ $rk->rk_nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('rk_id')
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
