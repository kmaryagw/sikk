@extends('layouts.app')

@section('title', 'formaudit')

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
                {{-- <i class="fas fa-phone"></i> --}}
                <h1><i class="fa-solid fa-gear"></i>   FORM AUDIT</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Standar</a></div>
                    <div class="breadcrumb-item">Form Audit</div>
                    
                </div>
            </div>

            <div class="section-body">

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Standar Audit</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fa-solid fa-envelope"></i>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control phone-number">
                                        <button type="button" class="btn btn-primary ">Tambah</button>
                                    </div>
                                    <!-- Tambahkan button di bawah input -->
                                    
                                </div>
                                
                               
                                
                                <!-- tanggal awal -->
                                <div class="form-group">
                                    <label>Tanggal Awal</label>
                                    <input type="date"
                                        class="form-control">
                                </div>

                                <!-- tanggal akhir -->
                                <div class="form-group">
                                    <label>Tanggal Akhir</label>
                                    <input type="date"
                                        class="form-control">
                                </div>

                            </div>
                        </div>

                        {{-- tabel --}}

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table-striped table"
                                                id="table-1">
                                                <thead>
                                                    <tr>
                                                        <th >
                                                            #
                                                        </th>
                                                        <th >ID</th>
                                                        <th >Standar</th>
                                                        <th >Deskripsi Standar</th>
                                                        <th >Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td >
                                                            1
                                                        </td>
                                                        <td>14</td>
                                                        
                                                        
                                                        <td>2018-01-20</td>
                                                        <td>
                                                            
                                                        </td>
                                                        <td><a href="#"
                                                                class="btn btn-danger">Hapus</a></td>
                                                    </tr>
                                                    
                                                </tbody>
                                            </table>
                                            <a href="#"class="btn btn-primary">Submit</a>
                                            <a href="#"class="btn btn-danger">Cancel</a>
                                        </div>
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
