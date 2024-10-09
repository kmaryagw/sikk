@extends('layouts.app')

@section('title', 'Form Audit Internal')

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
                <h1>Standar</h1>
            </div>

            <div class="section-body">

                <div class="row">
                    <div class="col-12 col-md-5">
                        <div class="card">
                            <div class="card-header">
                                <h4>Daftar Standar</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Default Input Text</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Phone Number (US Format)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-phone"></i>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control phone-number">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Password Strength</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-lock"></i>
                                            </div>
                                        </div>
                                        <input type="password" class="form-control pwstrength" data-indicator="pwindicator">
                                    </div>
                                    <div id="pwindicator" class="pwindicator">
                                        <div class="bar"></div>
                                        <div class="label"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Currency</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                $
                                            </div>
                                        </div>
                                        <input type="text" class="form-control currency">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Purchase Code</label>
                                    <input type="text" class="form-control purchase-code" placeholder="ASDF-GHIJ-KLMN-OPQR">
                                </div>
                                <div class="form-group">
                                    <label>Invoice</label>
                                    <input type="text" class="form-control invoice-input">
                                </div>
                                <div class="form-group">
                                    <label>Date</label>
                                    <input type="text" class="form-control datemask" placeholder="YYYY/MM/DD">
                                </div>
                                <div class="form-group">
                                    <label>Credit Card</label>
                                    <input type="text" class="form-control creditcard">
                                </div>
                                <div class="form-group">
                                    <label>Tags</label>
                                    <input type="text" class="form-control inputtags">
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div class="col-12 col-md-7">
                        <div class="card">
                            <div class="card-header">
                                <h4>Form Audit Internal</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Default Input Text</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Phone Number (US Format)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-phone"></i>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control phone-number">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Password Strength</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-lock"></i>
                                            </div>
                                        </div>
                                        <input type="password" class="form-control pwstrength" data-indicator="pwindicator">
                                    </div>
                                    <div id="pwindicator" class="pwindicator">
                                        <div class="bar"></div>
                                        <div class="label"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Currency</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                $
                                            </div>
                                        </div>
                                        <input type="text" class="form-control currency">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Purchase Code</label>
                                    <input type="text" class="form-control purchase-code" placeholder="ASDF-GHIJ-KLMN-OPQR">
                                </div>
                                <div class="form-group">
                                    <label>Invoice</label>
                                    <input type="text" class="form-control invoice-input">
                                </div>
                                <div class="form-group">
                                    <label>Date</label>
                                    <input type="text" class="form-control datemask" placeholder="YYYY/MM/DD">
                                </div>
                                <div class="form-group">
                                    <label>Credit Card</label>
                                    <input type="text" class="form-control creditcard">
                                </div>
                                <div class="form-group">
                                    <label>Tags</label>
                                    <input type="text" class="form-control inputtags">
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
