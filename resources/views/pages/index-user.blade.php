@extends('layouts.app')
@section('title', 'user')
    {{-- @if (session()->has('message'))
        <p class="alert alert-info">{{ session('message') }}</p>
    @endif --}}
    <div class="card mb-3">
        <div class="card-header">
            <form class="row row-cols-auto g-1">
                <div class="col">
                    <input class="form-control" name="q" value="{{ $q }}" placeholder="Pencarian..." />
                </div>
                <div class="col">
                    <button class="btn btn-info"><i class="fa-solid fa-arrows-rotate"></i> Refresh</button>
                </div>
                <div class="col">
                    {{-- <a class="btn btn-primary" href="{{ route('user.create') }}"><i class="fa-solid fa-plus"></i> Tambah</a> --}}
                </div>
                
                <!-- Button trigger modal -->

  
  <!-- Modal -->
  
            </form>
        </div>

        <div class="table-responsive text-center">
            <table class="table table-hover table-bordered table-striped m-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama User</th>
                        <th>Email</th>
                        <th>Alamat</th>
                        <th>Level</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <?php //$no = 1;
                ?>
                <tbody>
                    <?php $no = 1; ?>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $user->nama_user }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->alamat }}</td>
                            
                            
                            
                            <td>{{ $user->level }}</td> 

                            <td>
                                <a class="btn btn-warning" href="#"><i class="fa-solid fa-pen-to-square"></i> Ubah</a>
                                <form method="POST" class="d-inline" action="#">
                                @csrf
                                @method('DELETE')
                                {{-- <button class="btn btn-danger" onclick="return confirm('Hapus data?')"><i class="fa-solid fa-trash"></i> Hapus</button> --}}
                                </form>
                                
                                </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
        <div class="card-footer">
            {{ $users->links() }}
        </div>
        @endif
    </div>
@endsection
