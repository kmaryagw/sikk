@extends('layouts.app')

@section('title', 'DataTables')

@push('style')
    <!-- CSS Libraries -->
    {{-- <link rel="stylesheet"
        href="assets/modules/datatables/datatables.min.css">
    <link rel="stylesheet"
        href="assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet"
        href="assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css"> --}}

    <link rel="stylesheet"
        href="{{ asset('library/datatables/media/css/jquery.dataTables.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Auditor</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="#">Modules</a></div>
                    <div class="breadcrumb-item">DataTables</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table-striped table"
                                        id="table-2">
                                        <thead>
                                            <tr>
                                                <th class="text-center">
                                                    <div class="custom-checkbox custom-control">
                                                        <input type="checkbox"
                                                            data-checkboxes="mygroup"
                                                            data-checkbox-role="dad"
                                                            class="custom-control-input"
                                                            id="checkbox-all">
                                                        <label for="checkbox-all"
                                                            class="custom-control-label">&nbsp;</label>
                                                    </div>
                                                </th>
                                                <th>Unit Audit</th>
                                                <th>Pelaksana Audit</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="custom-checkbox custom-control text-center">
                                                        <input type="checkbox"
                                                            data-checkboxes="mygroup"
                                                            class="custom-control-input"
                                                            id="checkbox-1">
                                                        <label for="checkbox-1"
                                                            class="custom-control-label">&nbsp;</label>
                                                    </div>
                                                </td>
                                                <td>Create a mobile app</td>
                            
                                                
                                                <td>2018-01-20</td>
                                                
                                                <td><a href="#"
                                                        class="btn btn-danger">Hapus</a></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="custom-checkbox custom-control text-center">
                                                        <input type="checkbox"
                                                            data-checkboxes="mygroup"
                                                            class="custom-control-input"
                                                            id="checkbox-2">
                                                        <label for="checkbox-2"
                                                            class="custom-control-label">&nbsp;</label>
                                                    </div>
                                                </td>
                                                <td>Redesign homepage</td>
                                                
                                                
                                                <td>2018-04-10</td>
                                                
                                                <td><a href="#"
                                                        class="btn btn-danger">Hapus</a></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="custom-checkbox custom-control text-center">
                                                        <input type="checkbox"
                                                            data-checkboxes="mygroup"
                                                            class="custom-control-input"
                                                            id="checkbox-3">
                                                        <label for="checkbox-3"
                                                            class="custom-control-label">&nbsp;</label>
                                                    </div>
                                                </td>
                                                <td>Backup database</td>
                                                
                                                
                                                <td>2018-01-29</td>
                                                
                                                <td><a href="#"
                                                        class="btn btn-danger">Hapus</a></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="custom-checkbox custom-control text-center">
                                                        <input type="checkbox"
                                                            data-checkboxes="mygroup"
                                                            class="custom-control-input"
                                                            id="checkbox-4">
                                                        <label for="checkbox-4"
                                                            class="custom-control-label">&nbsp;</label>
                                                    </div>
                                                </td>
                                                <td>Input data</td>
                                                
                                                
                                                <td>2018-01-16</td>
                                                
                                                <td><a href="#"
                                                        class="btn btn-danger">Hapus</a></td>
                                            </tr>
                                        </tbody>
                                    </table>
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
    {{-- <script src="assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js"></script> --}}
    <script src="{{ asset('library/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    {{-- <script src="{{ asset() }}"></script> --}}
    {{-- <script src="{{ asset() }}"></script> --}}
    <script src="{{ asset('library/jquery-ui-dist/jquery-ui.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/modules-datatables.js') }}"></script>
@endpush
