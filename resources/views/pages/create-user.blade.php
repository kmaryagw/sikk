@extends('layouts.app')

@section('title', 'Create User')

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
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item">Form User</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="card-body">
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
                                            <input class="form-control" type="password" name="password" required />
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
                                                    <i class="fa-solid fa-house-chimney"></i>
                                                </div>
                                            </div>
                                            <input class="form-control" type="text" name="status" value="{{ old('status') }}" required />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Role</label>
                                        <select class="form-select" name="role" id="role" required>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>{{ $role }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3" id="prodi_field" style="display: none;">
                                        <label>Prodi</label>
                                        <select class="form-select" name="prodi_id">
                                            <option value="" disabled selected>Pilih Prodi</option>
                                            @foreach ($prodis as $prodi)
                                                <option value="{{ $prodi->prodi_id }}" {{ old('prodi_id') == $prodi->prodi_id ? 'selected' : '' }}>
                                                    {{ $prodi->nama_prodi }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3" id="unit_field" style="display: none;">
                                        <label>Unit Kerja</label>
                                        <select class="form-select" name="id_unit_kerja">
                                            <option value="" disabled selected>Pilih Unit Kerja</option>
                                            @foreach ($units as $unit)
                                                <option value="{{ $unit->id_unit_kerja }}" {{ old('id_unit_kerja') == $unit->id_unit_kerja ? 'selected' : '' }}>
                                                    {{ $unit->unit_nama }}
                                                </option>
                                            @endforeach
                                        </select>
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
            const roleSelect = document.getElementById('role');
            const prodiField = document.getElementById('prodi_field');
            const unitField = document.getElementById('unit_field');

            function toggleFields() {
                const selectedRole = roleSelect.value;

                
                if (selectedRole === 'admin') {
                    prodiField.style.display = 'none';
                    unitField.style.display = 'none';
                    document.querySelector('select[name="prodi_id"]').value = '';
                    document.querySelector('select[name="id_unit_kerja"]').value = '';
                } else if (selectedRole === 'prodi') {
                    prodiField.style.display = 'block';
                    unitField.style.display = 'none';
                    document.querySelector('select[name="id_unit_kerja"]').value = '';
                } else if (selectedRole === 'unit kerja') {
                    prodiField.style.display = 'none';
                    unitField.style.display = 'block';
                    document.querySelector('select[name="prodi_id"]').value = '';
                } else {
                    prodiField.style.display = 'none';
                    unitField.style.display = 'none';
                }
            }

            roleSelect.addEventListener('change', toggleFields);
            toggleFields();
        });
    </script>
@endpush