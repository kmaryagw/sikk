@extends('layouts.app')

@section('title', 'user')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet"
        href="{{ asset('library/bootstrap-daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet"
        href="{{ asset('library/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('library/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('library/selectric/public/selectric.css') }}">
    <link rel="stylesheet"
        href="{{ asset('library/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('library/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">
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
                    <div class="col-12  col-lg-6">
                        <div class="card">
                            
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Nama User</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fa-solid fa-user"></i>
                                            </div>
                                        </div>
                                        <input class="form-control" type="text" name="nama_user" value="{{ old('nama_user') }}"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fa-solid fa-envelope"></i>
                                            </div>
                                        </div>
                                        <input class="form-control" type="email" name="email" value="{{ old('email') }}"/>
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
                                        <input class="form-control" type="password" name="password" value="{{ old('password') }}"/>
                                    </div>
                                    <div id="pwindicator"
                                        class="pwindicator">
                                        <div class="bar"></div>
                                        <div class="label"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fa-solid fa-house-chimney"></i>
                                            </div>
                                        </div>
                                        <input class="form-control" type="text" name="alamat" value="{{ old('alamat') }}"/>
                                    </div>
                                </div>
                                

                                
                                <div class="form-group">
                                    <label>Level</label>
                                    <select class="form-select" name="level">
                                        @foreach ($levels as $level)
                                            @if (old('level')==$level)
                                                <option value="{{ $level }}"  selected>{{ $level }}</option>
                                           @else 
                                            <option value="{{ $level }}">{{ $level }}</option>
                                                @endif
                                        @endforeach
                                    </select>
                                </div>
                                <a href="#"class="btn btn-primary">Submit</a>
                                <a href="#"class="btn btn-danger">Cancel</a>
                            </div>
                        </div>
                        
                        
                        
                    </div>
                    
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraies -->
    <script src="{{ asset('library/cleave.js/dist/cleave.min.js') }}"></script>
    <script src="{{ asset('library/cleave.js/dist/addons/cleave-phone.us.js') }}"></script>
    <script src="{{ asset('library/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('library/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}"></script>
    <script src="{{ asset('library/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ asset('library/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('library/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/forms-advanced-forms.js') }}"></script>
@endpush
