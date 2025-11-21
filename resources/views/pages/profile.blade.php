@extends('layouts.app')

@section('title', 'Profil Pengguna')
@push('style')
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
    
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Profil Pengguna</h1>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Informasi Profil</h4>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}" id="editProfileForm">
                    @csrf
                    @method('PUT')

                    <table class="table">
                        <tr>
                            <th>Username</th>
                            <td>
                                <span id="usernameText">{{ $user->username }}</span>
                                <input type="text" class="form-control d-none @error('username') is-invalid @enderror"
                                    name="username" value="{{ old('username', $user->username) }}" id="usernameInput">
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </td>
                        </tr>
                        <tr>
                            <th>Nama</th>
                            <td>
                                <span id="namaText">{{ $user->nama ?? '-' }}</span>
                                <input type="text" class="form-control d-none @error('nama') is-invalid @enderror"
                                    name="nama" value="{{ old('nama', $user->nama) }}" id="namaInput">
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>
                                <span id="emailText">{{ $user->email ?? '-' }}</span>
                                <input type="email" class="form-control d-none @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email', $user->email) }}" id="emailInput">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </td>
                        </tr>
                        
                    </table>


                    <div class="d-none" id="editButtons">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        

                        <button type="button" class="btn btn-danger" id="cancelEdit">Batal</button>
                    </div>

                    
                </form>
            </div>

            <div class="card-footer">
                <button class="btn btn-info" id="editProfile">
                    <i class="fa-solid fa-edit"></i> Edit Profil
                </button>
                <button class="btn btn-warning" id="openResetPasswordModal">
                    <i class="fa-solid fa-key"></i> Reset Password
                </button>
            </div>
        </div>
    </section>
</div>

<!-- 🔹 Modal Reset Password -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password</h5>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <form id="resetPasswordForm">
                @csrf
                <input type="hidden" name="_method" value="PUT">

                <div class="modal-body">
                    <div class="form-group">
                        <label>Password Lama</label>
                        <input type="password" class="form-control" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label>Password Baru</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Password</label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 🔹 Script -->
@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: '{{ session("success") }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session("error") }}',
            });
        @endif

        $('#editProfile').on('click', function() {
            toggleEditProfile(true);
        });

        $('#cancelEdit').on('click', function() {
            toggleEditProfile(false);
            resetForm();
        });

        function toggleEditProfile(editMode) {
            $('#usernameText, #namaText, #emailText').toggleClass('d-none', editMode);
            $('#usernameInput, #namaInput, #emailInput').toggleClass('d-none', !editMode);
            $('#editProfile').toggleClass('d-none', editMode);
            $('#editButtons').toggleClass('d-none', !editMode);
        }

        function resetForm() {
            $('#usernameInput').val("{{ $user->username }}");
            $('#namaInput').val("{{ $user->nama }}");
            $('#emailInput').val("{{ $user->email }}");
        }

        $('#openResetPasswordModal').on('click', function() {
            var modal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
            modal.show();
        });

        $('#resetPasswordForm').on('submit', function(event) {
            event.preventDefault();
            let form = $(this);
            let formData = new FormData(form[0]);
            
            $.ajax({
                type: "POST",
                url: "{{ route('profile.update-password') }}",
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-HTTP-Method-Override': 'PUT' },
                success: function() {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('resetPasswordModal'));
                    modal.hide();
                    Swal.fire('Berhasil!', 'Password berhasil diperbarui', 'success').then(() => location.reload());
                },
                error: function() {
                    Swal.fire('Gagal!', 'Terjadi kesalahan, coba lagi.', 'error');
                }
            });
        });
    });
</script>
@endpush
@endsection
