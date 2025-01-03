<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar">
    <form class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
            <li><a href="#"
                    data-toggle="sidebar"
                    class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
            <li>
        </ul>  
    </form>
    <ul class="navbar-nav navbar-right">
            <li class="dropdown"><a href="#"
                    data-toggle="dropdown"
                    class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                    <img alt="image"
                        src="{{ asset('img/avatar/avatar-1.png') }}"
                        class="rounded-circle mr-1">
                    <!-- Tampilkan nama pengguna yang sedang login -->
                    <div class="d-sm-none d-lg-inline-block">
                        Hi, {{ Auth::user()->username }} 
                        <span class="text-info">({{ Auth::user()->role }})</span>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="dropdown-title">
                        @if (Auth::user()->role == 'admin')
                            <div class="has-icon">
                                <i class="fas fa-user"></i> {{ Auth::user()->role }}
                            </div>
                        @elseif (Auth::user()->role == 'prodi' && Auth::user()->programStudi)
                            <div class="has-icon">
                                <i class="fas fa-building-columns"></i> {{ Auth::user()->programStudi->nama_prodi }}
                            </div>
                        @elseif (Auth::user()->role == 'unit kerja' && Auth::user()->unitKerja)
                            <div class="has-icon">
                                <i class="fas fa-briefcase"></i> {{ Auth::user()->unitKerja->unit_nama }}
                            </div>
                        @endif
                    </div>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('logout') }}"
                        class="dropdown-item has-icon text-danger"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </li>
    </ul>
</nav>
