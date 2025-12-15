@extends('layouts.app')

@section('title', 'Pengaturan Akun')

@push('style')
<style>
    /* --- Modern Card Styling --- */
    .card-profile {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.05);
        overflow: hidden;
        transition: transform 0.3s ease;
    }
    
    /* --- Profile Header Gradient --- */
    .profile-header-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        height: 150px;
        position: relative;
    }

    /* --- Avatar Styling --- */
    .user-avatar-wrapper {
        position: relative;
        margin-top: -75px;
        text-align: center;
    }
    .user-avatar {
        width: 150px;
        height: 150px;
        border: 5px solid #fff;
        border-radius: 50%;
        box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        object-fit: cover;
        background-color: #fff;
    }
    .user-status-indicator {
        position: absolute;
        bottom: 15px;
        right: 50%;
        transform: translateX(60px);
        width: 20px;
        height: 20px;
        background-color: #47c363;
        border: 3px solid #fff;
        border-radius: 50%;
    }

    /* --- Custom Tabs --- */
    .nav-pills .nav-link {
        border-radius: 50px;
        padding: 10px 25px;
        font-weight: 600;
        color: #6c757d;
        transition: all 0.3s;
    }
    .nav-pills .nav-link.active {
        background-color: #6777ef;
        color: #fff;
        box-shadow: 0 4px 10px rgba(103, 119, 239, 0.4);
    }
    .nav-pills .nav-link:hover:not(.active) {
        background-color: #f8f9fa;
        color: #6777ef;
    }

    /* --- Form Styling --- */
    .form-control {
        border-radius: 8px;
        height: 45px;
        border: 1px solid #e4e6fc;
        background-color: #fdfdff;
    }
    .form-control:focus {
        border-color: #6777ef;
        box-shadow: 0 0 0 0.2rem rgba(103, 119, 239, 0.15);
    }
    .form-label {
        font-weight: 600;
        color: #34395e;
        margin-bottom: 8px;
    }
    
    /* Readonly input styling to look like text */
    .form-control[readonly] {
        background-color: #f9f9f9;
        border-color: transparent;
        color: #555;
        cursor: default;
    }
    .is-editing .form-control[readonly] {
        background-color: #fff;
        border-color: #e4e6fc;
    }

    /* --- Password Strength Meter --- */
    .password-strength-bar {
        height: 5px;
        border-radius: 5px;
        transition: all 0.3s;
        margin-top: 5px;
        width: 0%;
        background-color: #e4e6fc;
    }
</style>
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header border-0 mb-4">
            <h1>Pengaturan Akun</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ url('dashboard') }}" class="btn btn-danger">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <!-- LEFT COLUMN: Profile Card -->
                <div class="col-12 col-lg-4 mb-4">
                    <div class="card card-profile">
                        <div class="profile-header-bg"></div>
                        <div class="user-avatar-wrapper">
                            <img alt="image" src="https://ui-avatars.com/api/?name={{ urlencode($user->nama) }}&background=6777ef&color=fff&size=512&bold=true" class="user-avatar">
                            <div class="user-status-indicator" title="Online"></div>
                        </div>
                        <div class="card-body text-center pt-3">
                            <h4 class="mb-0 text-dark">{{ $user->nama }}</h4>
                            <p class="text-muted mb-2">
                                {{ '@' . $user->username }}
                            </p>
                            <span class="badge badge-light px-3 py-1 mb-3 shadow-sm">
                                {{ ucfirst($user->role ?? 'User') }}
                            </span>

                            <div class="row mt-4 border-top pt-4">
                                <div class="col-6 border-right">
                                    <h6 class="font-weight-bold mb-0">Terdaftar</h6>
                                    <small class="text-muted">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</small>
                                </div>
                                <div class="col-6">
                                    <h6 class="font-weight-bold mb-0">Status</h6>
                                    <small class="text-success font-weight-bold">Aktif</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Tabs for Edit & Security -->
                <div class="col-12 col-lg-8">
                    <div class="card card-profile">
                        <div class="card-header bg-white border-bottom-0 pb-0 pt-4 px-4">
                            <ul class="nav nav-pills" id="profileTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="bio-tab" data-toggle="tab" href="#biodata" role="tab" aria-controls="biodata" aria-selected="true">
                                        <i class="fas fa-user-edit mr-2"></i> Biodata
                                    </a>
                                </li>
                                <li class="nav-item ml-3">
                                    <a class="nav-link" id="security-tab" data-toggle="tab" href="#security" role="tab" aria-controls="security" aria-selected="false">
                                        <i class="fas fa-shield-alt mr-2"></i> Keamanan
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body p-4">
                            <div class="tab-content" id="profileTabsContent">
                                
                                <!-- TAB 1: EDIT BIODATA -->
                                <div class="tab-pane fade show active" id="biodata" role="tabpanel" aria-labelledby="bio-tab">
                                    <form method="POST" action="{{ route('profile.update') }}" id="profileForm" class="no-loader">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <h6 class="text-muted mb-0">Informasi Pribadi</h6>
                                            <button type="button" class="btn btn-sm btn-icon icon-left btn-outline-primary" id="btnEnableEdit">
                                                <i class="far fa-edit"></i> Ubah Data
                                            </button>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label class="form-label">Username</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text bg-transparent"><i class="fas fa-at text-muted"></i></span>
                                                    </div>
                                                    <input type="text" class="form-control pl-3 pl-0" name="username" value="{{ old('username', $user->username) }}" readonly required>
                                                </div>
                                                @error('username') <small class="text-danger">{{ $message }}</small> @enderror
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label class="form-label">Email</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text bg-transparent"><i class="fas fa-envelope text-muted"></i></span>
                                                    </div>
                                                    <input type="email" class="form-control pl-3 pl-0" name="email" value="{{ old('email', $user->email) }}" readonly required>
                                                </div>
                                                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-12">
                                                <label class="form-label">Nama Lengkap</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text bg-transparent"><i class="fas fa-user text-muted"></i></span>
                                                    </div>
                                                    <input type="text" class="form-control pl-3 pl-0" name="nama" value="{{ old('nama', $user->nama) }}" readonly required>
                                                </div>
                                                @error('nama') <small class="text-danger">{{ $message }}</small> @enderror
                                            </div>
                                        </div>

                                        <div class="text-right mt-4 d-none" id="biodataActions">
                                            <button type="button" class="btn btn-secondary mr-2" id="btnCancelEdit">Batal</button>
                                            <button type="submit" class="btn btn-primary btn-shadow">
                                                <i class="fas fa-save mr-1"></i> Simpan Perubahan
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- TAB 2: KEAMANAN / PASSWORD -->
                                <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                                    <form id="securityForm" class="no-loader">
                                        @csrf
                                        <input type="hidden" name="_method" value="PUT">
                                        
                                        <div class="alert alert-light border-left-primary shadow-sm mb-4">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-info-circle text-primary mr-3" style="font-size: 1.5rem;"></i>
                                                <div>
                                                    <strong>Amankan Akun Anda</strong>
                                                    <div class="text-small text-muted">Gunakan password minimal 8 karakter dengan kombinasi huruf dan angka.</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">Password Saat Ini</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" name="current_password" id="current_password" required placeholder="Verifikasi password lama">
                                                <div class="input-group-append">
                                                    <span class="input-group-text cursor-pointer toggle-password" data-target="#current_password"><i class="far fa-eye"></i></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label class="form-label">Password Baru</label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" name="password" id="new_password" required placeholder="Buat password baru">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text cursor-pointer toggle-password" data-target="#new_password"><i class="far fa-eye"></i></span>
                                                    </div>
                                                </div>
                                                <!-- Indikator Kekuatan -->
                                                <div class="progress mt-2" style="height: 4px;">
                                                    <div class="progress-bar" id="password-strength" role="progressbar" style="width: 0%"></div>
                                                </div>
                                                <small class="text-muted" id="password-feedback"></small>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label class="form-label">Ulangi Password Baru</label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required placeholder="Konfirmasi password baru">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text cursor-pointer toggle-password" data-target="#password_confirmation"><i class="far fa-eye"></i></span>
                                                    </div>
                                                </div>
                                                <small class="text-danger d-none" id="password-match-error">Password tidak cocok!</small>
                                            </div>
                                        </div>

                                        <div class="text-right mt-3">
                                            <button type="submit" class="btn btn-danger btn-shadow" id="btnUpdatePassword">
                                                <i class="fas fa-lock mr-1"></i> Update Password
                                            </button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        
        // --- 1. GLOBAL SWEETALERT ---
        @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Berhasil', text: '{{ session("success") }}', timer: 2000, showConfirmButton: false });
        @endif
        @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Gagal', text: '{{ session("error") }}' });
        @endif

        // --- 2. LOGIC TAB BIODATA (Toggle Readonly) ---
        const $bioInputs = $('#biodata input');
        const $btnEnable = $('#btnEnableEdit');
        const $bioActions = $('#biodataActions');
        const $btnCancel = $('#btnCancelEdit');
        let originalData = {};

        $btnEnable.on('click', function() {
            // Backup data
            $bioInputs.each(function() { originalData[$(this).attr('name')] = $(this).val(); });
            
            // Enable inputs & Style
            $bioInputs.prop('readonly', false).addClass('bg-white');
            $(this).fadeOut();
            $bioActions.removeClass('d-none').hide().fadeIn();
            
            // Focus first input
            $bioInputs.first().focus();
        });

        $btnCancel.on('click', function() {
            // Restore data
            $bioInputs.each(function() { $(this).val(originalData[$(this).attr('name')]); });
            
            // Disable inputs & Style
            $bioInputs.prop('readonly', true).removeClass('bg-white');
            $bioActions.addClass('d-none');
            $btnEnable.fadeIn();
        });

        // --- 3. LOGIC PASSWORD SHOW/HIDE ---
        $('.toggle-password').on('click', function() {
            const inputId = $(this).data('target');
            const $input = $(inputId);
            const $icon = $(this).find('i');

            if ($input.attr('type') === 'password') {
                $input.attr('type', 'text');
                $icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                $input.attr('type', 'password');
                $icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // --- 4. PASSWORD STRENGTH METER ---
        $('#new_password').on('input', function() {
            const val = $(this).val();
            let strength = 0;
            const $bar = $('#password-strength');
            
            if (val.length >= 8) strength += 25;
            if (val.match(/[a-z]/) && val.match(/[A-Z]/)) strength += 25;
            if (val.match(/\d/)) strength += 25;
            if (val.match(/[^a-zA-Z\d]/)) strength += 25;

            $bar.css('width', strength + '%');
            if(strength < 50) $bar.removeClass('bg-success bg-warning').addClass('bg-danger');
            else if(strength < 75) $bar.removeClass('bg-success bg-danger').addClass('bg-warning');
            else $bar.removeClass('bg-danger bg-warning').addClass('bg-success');
        });

        // --- 5. PASSWORD MATCH CHECK ---
        $('#password_confirmation, #new_password').on('input', function() {
            const pass = $('#new_password').val();
            const conf = $('#password_confirmation').val();
            if(pass && conf && pass !== conf) {
                $('#password-match-error').removeClass('d-none');
            } else {
                $('#password-match-error').addClass('d-none');
            }
        });

        // --- 6. AJAX UPDATE PASSWORD ---
        $('#securityForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $('#btnUpdatePassword');
            const originalText = btn.html();

            // Simple Client Validation
            if($('#new_password').val() !== $('#password_confirmation').val()) {
                Swal.fire('Error', 'Konfirmasi password tidak cocok!', 'error');
                return;
            }

            btn.prop('disabled', true).html('<i class="fas fa-circle-notch fa-spin"></i> Memproses...');

            let formData = new FormData(this);
            
            $.ajax({
                type: "POST",
                url: "{{ route('profile.update-password') }}",
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-HTTP-Method-Override': 'PUT' },
                success: function() {
                    Swal.fire('Berhasil!', 'Password telah diperbarui.', 'success').then(() => location.reload());
                    $('#securityForm')[0].reset();
                },
                error: function(xhr) {
                    let msg = 'Terjadi kesalahan.';
                    if(xhr.responseJSON?.errors) msg = Object.values(xhr.responseJSON.errors)[0][0];
                    else if(xhr.responseJSON?.message) msg = xhr.responseJSON.message;
                    
                    Swal.fire('Gagal!', msg, 'error');
                },
                complete: function() {
                    btn.prop('disabled', false).html(originalText);
                }
            });
        });
    });
</script>
@endpush