@extends('layouts.app')

@section('title','SPMI')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/bootstrap-daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Form User</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="col-6 col-lg-6">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $err)
                                                <li>{{ $err }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('user.store') }}">
                                    @csrf
                                    <div class="form-group">
                                        <label>Nama User</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-user"></i>
                                                </div>
                                            </div>
                                            <input class="form-control" type="text" name="username" value="{{ old('username') }}" required />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Password</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-lock"></i>
                                                </div>
                                            </div>
                                            <input class="form-control" type="password" name="password" id="password" required />
                                            
                                            <div class="input-group-append">
                                                <div class="input-group-text" id="togglePassword" style="cursor: pointer;">
                                                    <i class="fa-solid fa-eye" id="eyeIcon"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="pwindicator" class="pwindicator">
                                            <div class="bar"></div>
                                            <div class="label"></div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Status</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-exclamation-circle"></i>
                                                </div>
                                            </div>
                                            <select class="form-control" name="status" required>
                                                <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Role</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-user-tag"></i>
                                                </div>
                                            </div>
                                            <select class="form-control" name="role" id="role" required>
                                                <option value="" disabled selected>Pilih Role</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>{{ $role }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3" id="fakultas_field" style="display: none;">
                                        <label>Fakultas</label>
                                        <div id="fakultas_hint" class="text-muted small mb-2" style="display: none;">
                                            <i class="fa-solid fa-info-circle"></i> Pilih fakultas jika unit kerja ini adalah <b>Dekanat</b> (untuk kendali monitoring prodi).
                                        </div>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-school"></i>
                                                </div>
                                            </div>
                                            <select class="form-control" name="id_fakultas">
                                                <option value="" selected>Pilih Fakultas (Opsional)</option>
                                                @foreach ($fakultasns as $fakultas)
                                                    <option value="{{ $fakultas->id_fakultas }}" {{ old('id_fakultas') == $fakultas->id_fakultas ? 'selected' : '' }}>{{ $fakultas->nama_fakultas }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3" id="prodi_field" style="display: none;">
                                        <label>Prodi</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-building-columns"></i>
                                                </div>
                                            </div>
                                            <select class="form-control" name="prodi_id">
                                                <option value="" disabled selected>Pilih Prodi</option>
                                                @foreach ($prodis as $prodi)
                                                    <option value="{{ $prodi->prodi_id }}" {{ old('prodi_id') == $prodi->prodi_id ? 'selected' : '' }}>{{ $prodi->nama_prodi }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3" id="unit_field" style="display: none;">
                                        <label>Unit Kerja</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-briefcase"></i>
                                                </div>
                                            </div>
                                            <select class="form-control" name="unit_id">
                                                <option value="" disabled selected>Pilih Unit Kerja</option>
                                                @foreach ($units as $unit)
                                                    <option value="{{ $unit->unit_id }}" {{ old('unit_id') == $unit->unit_id ? 'selected' : '' }}>{{ $unit->unit_nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <a href="{{ url('user') }}" class="btn btn-danger">Kembali</a>
                                    </div>
                                </form>
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
    <!-- JS Libraries -->
    <script src="{{ asset('library/cleave.js/dist/cleave.min.js') }}"></script>
    <script src="{{ asset('library/cleave.js/dist/addons/cleave-phone.us.js') }}"></script>
    <script src="{{ asset('library/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('library/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}"></script>
    <script src="{{ asset('library/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ asset('library/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('library/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>

    @include('sweetalert::alert')

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/forms-advanced-forms.js') }}"></script>

    <!-- Custom Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- Script Toggle Role (Sudah Ada) ---
            const roleSelect = document.getElementById('role');
            const prodiField = document.getElementById('prodi_field');
            const unitField = document.getElementById('unit_field');
            const fakultasField = document.getElementById('fakultas_field');
            const fakultasHint = document.getElementById('fakultas_hint');

            function toggleFields() {
                const selectedRole = roleSelect.value;
                prodiField.style.display = 'none';
                unitField.style.display = 'none';
                fakultasField.style.display = 'none';
                fakultasHint.style.display = 'none';

                if (selectedRole === 'prodi') {
                    prodiField.style.display = 'block';
                } 
                else if (selectedRole === 'unit kerja') {
                    unitField.style.display = 'block';
                    fakultasField.style.display = 'block';
                    fakultasHint.style.display = 'block'; 
                } 
                else if (selectedRole === 'fakultas') {
                    fakultasField.style.display = 'block';
                }
            }

            roleSelect.addEventListener('change', toggleFields);
            toggleFields(); 

            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                eyeIcon.classList.toggle('fa-eye');
                eyeIcon.classList.toggle('fa-eye-slash');
            });
        });
    </script>
@endpush