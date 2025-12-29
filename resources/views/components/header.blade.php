<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar">
    <form class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
            <li>
                <a href="#" data-toggle="sidebar" class="nav-link nav-link-lg">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
        </ul>  
    </form>

    <ul class="navbar-nav navbar-right">
        @if(Auth::check())
            <li class="dropdown dropdown-list-toggle">
                <a href="#" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg {{ auth()->user()->unreadNotifications->count() > 0 ? 'beep' : '' }}">
                    <i class="far fa-bell"></i>
                    
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="badge badge-danger badge-counter">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </a>
                
                <div class="dropdown-menu dropdown-list dropdown-menu-right">
                    <div class="dropdown-header">Pusat Notifikasi
                        <div class="float-right">
                            <a href="#">Tandai semua dibaca</a>
                        </div>
                    </div>
                    
                    <div class="dropdown-list-content dropdown-list-icons">
                        @forelse(auth()->user()->notifications->take(10) as $notification)
                            <div id="notif-item-{{ $notification->id }}" class="dropdown-item d-flex justify-content-between align-items-center {{ $notification->read_at ? '' : 'bg-light' }}">
                                <a href="{{ route('notifikasi.read', $notification->id) }}" class="d-flex align-items-center w-100 text-decoration-none" style="overflow: hidden;">
                                    <div class="dropdown-item-icon {{ $notification->data['color'] ?? 'bg-primary' }} text-white mr-3">
                                        <i class="{{ $notification->data['icon'] ?? 'fas fa-bell' }}"></i>
                                    </div>
                                    <div class="dropdown-item-desc">
                                        {{ $notification->data['title'] }}
                                        <div class="time text-primary">
                                            {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                        </div>
                                    </div>
                                </a>
                                <a href="javascript:void(0)" 
                                class="text-danger ml-2 btn-delete-notif" 
                                data-id="{{ $notification->id }}" 
                                style="z-index: 99; padding: 5px;">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        @empty
                            <div class="dropdown-item text-center small text-gray-500 py-3">
                                Tidak ada notifikasi
                            </div>
                        @endforelse
                    </div>
                    <div class="dropdown-footer text-center">
                        <a href="#">Lihat Semua <i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            </li>
            <li class="dropdown">
                <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                    <img alt="image" src="{{ asset('img/avatar/avatar-1.png') }}" class="rounded-circle mr-1">
                    <div class="d-sm-none d-lg-inline-block">
                        Hi, {{ Auth::user()->nama ?? Auth::user()->username }} 
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="dropdown-title">
                        @if (Auth::user()->role == 'admin')
                            Administrator
                        @elseif (Auth::user()->role == 'prodi' && Auth::user()->programStudi)
                            {{ Auth::user()->programStudi->nama_prodi }}
                        @elseif (Auth::user()->role == 'unit kerja' && Auth::user()->unitKerja)
                            {{ Auth::user()->unitKerja->unit_nama }}
                        @elseif (Auth::user()->role == 'fakultas' && Auth::user()->fakultas)
                            {{ Auth::user()->fakultas->nama_fakultas }}
                        @endif
                    </div>
                    
                    <a href="{{ route('profile') }}" class="dropdown-item has-icon">
                        <i class="far fa-user"></i> Profil
                    </a>
                    
                    <div class="dropdown-divider"></div>
                    
                    <a href="#" class="dropdown-item has-icon text-danger" 
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </li>

        @else
            {{-- Jika belum login --}}
            <li class="nav-item">
                <a href="{{ route('login') }}" class="btn btn-primary ml-3 mt-1">Login</a>
            </li>
        @endif
    </ul>

@push('scripts')
    <script>
        document.getElementById('logout-link').addEventListener('click', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Yakin ingin keluar?',
                text: "Sesi Anda akan berakhir!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ED3500',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Keluar!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        });
    </script>
@endpush

</nav>
