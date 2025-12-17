@extends('layouts.app')
@section('title', 'Riwayat Perubahan Indikator')

@push('style')
    <style>
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .circular-progress {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: conic-gradient(#6777ef var(--percent), #e4e6fc 0);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            transition: all 0.3s;
        }
        .circular-progress::before {
            content: '';
            position: absolute;
            width: 84px; /* Lebar dalam (tebal ring = 100-84 / 2 = 8px) */
            height: 84px;
            border-radius: 50%;
            background-color: #f8f9fa; /* Sesuaikan dengan warna bg card */
        }
        .progress-value {
            position: relative;
            font-size: 1.3rem;
            font-weight: 800;
            color: #6777ef;
            z-index: 1;
        }
        
        /* --- Styles untuk Tipe Target Lainnya --- */
        .target-box {
            background-color: #f8f9fa;
            border: 1px solid #e4e6fc;
            border-radius: 8px;
            padding: 20px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            min-height: 140px; /* Tinggi minimum agar seimbang */
        }

        .target-number {
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1;
            color: #34395e;
        }

        .target-badge {
            font-size: 1.1rem;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .icon-availability {
            font-size: 2.5rem;
            margin-bottom: 5px;
        }

        .section-header {
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.03);
            border-radius: 5px;
            border: none;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        /* Container untuk Back Button & Title agar menyatu rapi */
        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .section-header h1 { 
            margin: 0 !important;
            font-size: 1.5rem;
            font-weight: 700;
            color: #34395e;
            line-height: 1.2;
        }

        .btn-back-custom {
            background-color: #f0f2f5;
            color: #6777ef;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            text-decoration: none;
        }
        .btn-back-custom:hover {
            background-color: #6777ef;
            color: #fff;
            transform: translateX(-3px);
        }

        /* --- Hero Card (Info Indikator) --- */
        .hero-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            border: none;
            position: relative;
            overflow: hidden;
            margin-bottom: 40px;
        }
        .hero-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 6px; height: 100%;
            background: linear-gradient(to bottom, #6777ef, #a2acfa);
        }
        .hero-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #98a6ad;
            font-weight: 700;
            margin-bottom: 4px;
        }

        /* --- Timeline Styles --- */
        .timeline-wrapper {
            position: relative;
            padding-left: 40px;
        }
        .timeline-wrapper::before {
            content: '';
            position: absolute;
            left: 14px; top: 10px; bottom: 0;
            width: 2px;
            background: #e4e6fc;
            border-radius: 2px;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 40px;
            animation: fadeUp 0.6s ease-out forwards;
            opacity: 0;
        }
        .timeline-item:nth-child(1) { animation-delay: 0.1s; }
        .timeline-item:nth-child(2) { animation-delay: 0.2s; }
        .timeline-item:nth-child(3) { animation-delay: 0.3s; }
        .timeline-item:nth-child(n+4) { animation-delay: 0.4s; }

        .timeline-marker {
            position: absolute;
            left: -40px;
            top: 0;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #fff;
            border: 3px solid #6777ef;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            box-shadow: 0 2px 6px rgba(103, 119, 239, 0.3);
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .timeline-item:hover .timeline-marker { transform: scale(1.15); }

        .timeline-content {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.03);
            border: 1px solid #f0f2f5;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .timeline-content:hover {
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
            transform: translateY(-3px);
            border-color: #6777ef;
        }

        .timeline-header {
            background: #fafbfe;
            padding: 15px 20px;
            border-bottom: 1px solid #f0f2f5;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap; /* Agar aman di HP */
            gap: 10px;
        }

        /* Markers & Feedback Colors */
        .marker-success { border-color: #47c363; color: #47c363; }
        .marker-primary { border-color: #6777ef; color: #6777ef; }
        .marker-warning { border-color: #ffa426; color: #ffa426; }
        .marker-danger  { border-color: #fc544b; color: #fc544b; }

        .file-attachment {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            background: #fdfdfd;
            border: 1px solid #e4e6fc;
            border-radius: 6px;
            color: #6777ef;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s;
            text-decoration: none !important;
        }
        .file-attachment:hover {
            background: #6777ef;
            color: #fff !important;
            border-color: #6777ef;
        }

        /* Responsiveness for Header */
        @media (max-width: 576px) {
            .section-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .section-header-breadcrumb {
                margin-top: 10px;
                align-self: flex-start;
            }
        }
    </style>
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        
        <div class="section-header">
            <div class="header-left">
                <a href="{{ route('pages.index-list-indicators', $monitoringiku->mti_id) }}" class="btn-back-custom" title="Kembali">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1>Riwayat & Detail Indikator</h1>
            </div>
        </div>

        <div class="section-body">
            {{-- 1. HERO INFO CARD --}}
            <div class="hero-card p-4">
                <div class="row align-items-center">
                    {{-- Kolom Kiri: Info Indikator --}}
                    <div class="col-lg-8 col-md-12 mb-4 mb-lg-0">
                        <div class="hero-label"><i class="fas fa-bullseye mr-1"></i> Indikator Kinerja</div>
                        <h4 class="text-dark mt-2 mb-3" style="line-height: 1.4;">
                            <span class="text-primary mr-2">[{{ $targetIndikator->indikatorKinerja->ik_kode }}]</span>
                            {{ $targetIndikator->indikatorKinerja->ik_nama }}
                        </h4>
                        
                        <div class="d-flex flex-wrap text-muted">
                            <div class="mr-4 mb-2">
                                <i class="fas fa-building mr-1 text-primary"></i> {{ $monitoringiku->prodi->nama_prodi }}
                            </div>
                            <div class="mb-2">
                                <i class="far fa-calendar-alt mr-1 text-primary"></i> Tahun {{ $monitoringiku->tahunKerja->th_tahun }}
                            </div>
                            <div class="ml-lg-4 mb-2">
                                <i class="fas fa-tag mr-1 text-primary"></i> 
                                Ketercapaian : <span class="text-uppercase font-weight-bold text-dark">{{ $targetIndikator->indikatorKinerja->ik_ketercapaian }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="target-box">
                            <span class="hero-label d-block text-muted mb-2">Target Ditetapkan</span>

                            @php
                                $tipe = strtolower($targetIndikator->indikatorKinerja->ik_ketercapaian ?? 'text');
                                $targetRaw = trim($targetIndikator->ti_target);
                                $numericValue = (float) str_replace(['%', ','], ['', '.'], $targetRaw);
                            @endphp

                            @if($tipe === 'persentase')
                                <div class="circular-progress" style="--percent: {{ $numericValue }}%">
                                    <span class="progress-value">{{ $numericValue }}%</span>
                                </div>
                                
                            @elseif($tipe === 'nilai' || $tipe === 'numerik')
                                <div class="target-number text-primary">{{ $targetRaw }}</div>
                                <span class="text-small text-muted mt-1">Poin / Nilai</span>

                            @elseif($tipe === 'rasio')
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fas fa-balance-scale text-warning mr-2" style="font-size: 1.5rem;"></i>
                                    <div class="h2 font-weight-bold text-dark mb-0">{{ $targetRaw }}</div>
                                </div>
                                <span class="text-small text-muted mt-1">Rasio Perbandingan</span>

                            @elseif($tipe === 'ketersediaan' || in_array(strtolower($targetRaw), ['ada', 'tidak']))
                                @if(strtolower($targetRaw) === 'ada')
                                    <div class="text-success">
                                        <i class="fas fa-check-circle icon-availability"></i>
                                        <div class="h4 font-weight-bold">ADA</div>
                                    </div>
                                @else
                                    <div class="text-danger">
                                        <i class="fas fa-times-circle icon-availability"></i>
                                        <div class="h4 font-weight-bold">TIDAK ADA</div>
                                    </div>
                                @endif

                            @else
                                <div class="h3 font-weight-bold text-dark mb-0">
                                    {{ Str::limit($targetRaw, 30) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. TIMELINE HISTORY --}}
            <div class="row">
                <div class="col-12">
                    @if($histories->isEmpty())
                        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                            <div class="card-body py-5 text-center">
                                <div class="empty-state" data-height="400">
                                    
                                    {{-- PERBAIKAN ICON: Menggunakan Flexbox agar bulat sempurna & center --}}
                                    <div class="d-flex justify-content-center mb-4">
                                        <div class="bg-primary shadow-primary rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 90px; height: 90px; box-shadow: 0 10px 20px rgba(103, 119, 239, 0.3);">
                                            <i class="fas fa-history text-white" style="font-size: 36px;"></i>
                                        </div>
                                    </div>
                                    
                                    <h3 class="text-dark font-weight-bold">Belum Ada Aktivitas</h3>
                                    <p class="lead text-muted mx-auto" style="max-width: 500px; font-size: 16px; line-height: 1.6;">
                                        Saat ini belum ada pencatatan capaian, bukti dukung, ataupun evaluasi yang dilakukan untuk indikator kinerja ini.
                                    </p>
                                    
                                    <a href="{{ route('pages.index-list-indicators', $monitoringiku->mti_id) }}" 
                                       class="btn btn-primary btn-lg mt-4 px-5 py-2 shadow-primary"
                                       style="border-radius: 30px; font-weight: 600; letter-spacing: 0.5px;">
                                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="timeline-wrapper">
                            @foreach($histories as $history)
                                @php
                                    $statusLower = strtolower($history->hmi_status);
                                    
                                    // Logic Warna & Ikon
                                    if ($statusLower == 'tercapai') {
                                        $markerClass = 'marker-success';
                                        $iconClass = 'fa-check';
                                        $badgeClass = 'success';
                                    } elseif ($statusLower == 'terlampaui') {
                                        $markerClass = 'marker-primary';
                                        $iconClass = 'fa-arrow-up';
                                        $badgeClass = 'primary';
                                    } elseif (str_contains($statusLower, 'tidak')) {
                                        $markerClass = str_contains($statusLower, 'terlaksana') ? 'marker-danger' : 'marker-warning';
                                        $iconClass = 'fa-times';
                                        $badgeClass = str_contains($statusLower, 'terlaksana') ? 'danger' : 'warning';
                                    } else {
                                        $markerClass = 'marker-warning'; 
                                        $iconClass = 'fa-pen';
                                        $badgeClass = 'light';
                                    }
                                @endphp

                                <div class="timeline-item">
                                    {{-- Marker Bulat --}}
                                    <div class="timeline-marker {{ $markerClass }}">
                                        <i class="fas {{ $iconClass }}"></i>
                                    </div>

                                    <div class="timeline-content">
                                        {{-- Header Timeline --}}
                                        <div class="timeline-header">
                                            <div>
                                                <span class="text-dark font-weight-bold">
                                                    {{ \Carbon\Carbon::parse($history->created_at)->translatedFormat('d F Y') }}
                                                </span>
                                                <span class="text-dark small ml-1">
                                                    &bull; {{ \Carbon\Carbon::parse($history->created_at)->format('H:i') }}
                                                </span>
                                            </div>
                                            <div class="badge badge-light text-dark font-weight-normal">
                                                {{ \Carbon\Carbon::parse($history->created_at)->diffForHumans() }}
                                            </div>
                                        </div>

                                        <div class="p-4">
                                            <div class="row">
                                                <div class="col-md-3 border-right text-center mb-3 mb-md-0">
                                                    <span class="hero-label d-block text-left text-md-center">Capaian</span>
                                                    <div class="h3 font-weight-bold text-dark mt-2 mb-2">{{ $history->hmi_capaian ?? '-' }}</div>
                                                    <span class="badge badge-{{ $badgeClass }} px-3 py-1 rounded-pill">
                                                        {{ ucfirst($history->hmi_status) }}
                                                    </span>
                                                </div>

                                                <div class="col-md-9 pl-md-4">
                                                    <div class="mb-3">
                                                        <span class="hero-label d-block mb-2">Keterangan</span>
                                                        <p class="text-muted mb-0" style="line-height: 1.6;">
                                                            {{ $history->hmi_keterangan ?: 'Tidak ada keterangan tambahan.' }}
                                                        </p>
                                                    </div>

                                                    <div>
                                                        <span class="hero-label d-block mb-2">Bukti Dukung</span>
                                                        @if($history->hmi_url)
                                                            <a href="{{ $history->hmi_url }}" target="_blank" class="file-attachment">
                                                                <i class="fas fa-link mr-2"></i> Buka Dokumen / Tautan
                                                            </a>
                                                        @else
                                                            <span class="text-small text-muted font-italic">
                                                                <i class="fas fa-ban mr-1"></i> Tidak ada bukti dilampirkan
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if($history->hmi_evaluasi || $history->hmi_tindaklanjut || $history->hmi_peningkatan)
                                            <div class="p-3 bg-light border-top">
                                                <div class="mb-2">
                                                    <i class="fas fa-user-shield text-primary mr-2"></i> 
                                                    <span class="font-weight-bold text-primary" style="font-size: 0.9rem;">Feedback & Tindak Lanjut</span>
                                                </div>
                                                
                                                <div class="row">
                                                    @if($history->hmi_evaluasi)
                                                        <div class="col-lg-4 mb-2">
                                                            <div class="p-3 bg-white rounded border h-100 shadow-sm">
                                                                <strong class="text-info d-block mb-1 font-size-12">EVALUASI</strong>
                                                                <p class="mb-0 text-small text-dark">{{ $history->hmi_evaluasi }}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($history->hmi_tindaklanjut)
                                                        <div class="col-lg-4 mb-2">
                                                            <div class="p-3 bg-white rounded border h-100 shadow-sm">
                                                                <strong class="text-warning d-block mb-1 font-size-12">TINDAK LANJUT</strong>
                                                                <p class="mb-0 text-small text-dark">{{ $history->hmi_tindaklanjut }}</p>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if($history->hmi_peningkatan)
                                                        <div class="col-lg-4 mb-2">
                                                            <div class="p-3 bg-white rounded border h-100 shadow-sm">
                                                                <strong class="text-success d-block mb-1 font-size-12">PENINGKATAN</strong>
                                                                <p class="mb-0 text-small text-dark">{{ $history->hmi_peningkatan }}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </section>
</div>
@endsection