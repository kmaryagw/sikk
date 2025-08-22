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
            {{-- Jika sudah login --}}
            <li class="dropdown">
                <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                    <img alt="image" src="{{ asset('img/avatar/avatar-1.png') }}" class="rounded-circle mr-1">
                    <div class="d-sm-none d-lg-inline-block">
                        Hi, {{ Auth::user()->nama ?? Auth::user()->username }} 
                        <span class="text-info">({{ Auth::user()->role }})</span>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="dropdown-title">
                        @if (Auth::user()->role == 'admin')
                            <div class="has-icon">{{ Auth::user()->role }}</div>
                        @elseif (Auth::user()->role == 'prodi' && Auth::user()->programStudi)
                            <div class="has-icon">{{ Auth::user()->programStudi->nama_prodi }}</div>
                        @elseif (Auth::user()->role == 'unit kerja' && Auth::user()->unitKerja)
                            <div class="has-icon">{{ Auth::user()->unitKerja->unit_nama }}</div>
                        @elseif (Auth::user()->role == 'fakultas' && Auth::user()->fakultas)
                            <div class="has-icon">{{ Auth::user()->fakultas->nama_fakultas }}</div>
                        @endif
                    </div>
                    <a href="{{ route('profile') }}" class="dropdown-item has-icon">
                        <i class="fas fa-user"></i> Profil
                    </a>
                    {{-- <div class="dropdown-divider"></div>
                    <a href="{{ url('/') }}" class="dropdown-item has-icon">
                        <i class="fas fa-bullhorn"></i> Pengumuman
                    </a> --}}
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('logout') }}" class="dropdown-item has-icon text-danger"
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
            <li>
                <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
            </li>
        @endif
    </ul>
</nav>
