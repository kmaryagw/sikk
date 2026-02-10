<!-- File: resources/views/pages/edit-periode-monitoring.blade.php -->
@extends('layouts.app')
@section('title','SPMI')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit Periode Monitoring</h1>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h4>Ubah Tanggal Periode Monitoring</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('periode-monitoring.update', $periode->pmo_id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="pmo_tanggal_mulai">Tanggal Mulai</label>
                            <input type="date" id="pmo_tanggal_mulai" name="pmo_tanggal_mulai" class="form-control" value="{{ \Carbon\Carbon::parse($periode->pmo_tanggal_mulai)->format('Y-m-d') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="pmo_tanggal_selesai">Tanggal Selesai</label>
                            <input type="date" id="pmo_tanggal_selesai" name="pmo_tanggal_selesai" class="form-control" value="{{ \Carbon\Carbon::parse($periode->pmo_tanggal_selesai)->format('Y-m-d') }}" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
