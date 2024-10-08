<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="index.html">Instiki</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="index.html">IKU</a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">Menu</li>
            <li class="nav-item dropdown {{ $type_menu === 'dashboard' ? 'active' : '' }}">
                <a href="{{ url('dashboard') }}"
                    class="nav-link"><i class="fas fa-fire"></i><span>Dashboard</span></a>
                
            </li>
            

            {{-- Master Data --}}
            @if (Auth::user()->level == 'admin')
             <li class="nav-item dropdown {{ $type_menu === 'masterdata' ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fa-solid fa-basketball"></i> <span>Master Data</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('user') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('user') }}">User</a>
                    </li>
                    <li class="{{ Request::is('program-studi') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('program-studi') }}">Program Studi</a>
                    </li>
                    <li class="{{ Request::is('unit-kerja') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('unit-kerja') }}">Unit Kerja</a>
                    </li>
                    <li class="{{ Request::is('rencana-strategis') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('rencana-strategis') }}">Rencana Strategis</a>
                    </li>
                    <li class="{{ Request::is('tahun') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('tahun') }}">Tahun</a>
                    </li>
                    <li class="{{ Request::is('periode-monev') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('periode-monev') }}">Periode Monev</a>
                    </li>
                </ul>
            </li>
            @endif

            {{-- Standar --}}
            @if (Auth::user()->level == 'admin')
            <li class="{{ Request::is('survei') ? 'active' : '' }}">
                <a class="nav-link"
                    href="{{ url('credits') }}"><i class="fa fa-paper-plane" aria-hidden="true"></i> <span>Standar</span>
                </a>
            </li>
            @endif

            {{-- Indikator Kinerja Utama/Tambahan --}}
            @if (Auth::user()->level == 'admin'|| Auth::user()->level == 'prodi')
            <li class="{{ Request::is('survei') ? 'active' : '' }}">
                <a class="nav-link"
                    href="{{ url('credits') }}"><i class="fa-solid fa-circle-info"></i> <span>Indikator Kinerja Utama/Tambahan</span>
                </a>
            </li>
            @endif

            {{-- Target Capaian --}}
            @if (Auth::user()->level == 'admin'|| Auth::user()->level == 'prodi')
            <li class="{{ Request::is('survei') ? 'active' : '' }}">
                <a class="nav-link"
                    href="{{ url('credits') }}"><i class="fa-solid fa-award"></i> <span>Target Capaian</span>
                </a>
            </li>
            @endif

            {{-- Program Kerja --}}
            @if (Auth::user()->level == 'admin'|| Auth::user()->level == 'prodi'|| Auth::user()->level == 'unit kerja')
            <li class="{{ Request::is('survei') ? 'active' : '' }}">
                <a class="nav-link"
                    href="{{ url('credits') }}"><i class="fa-solid fa-book"></i> <span>Program Kerja</span>
                </a>
            </li>
            @endif

            {{-- Realisasi Renja --}}
            @if (Auth::user()->level == 'admin'|| Auth::user()->level == 'prodi'|| Auth::user()->level == 'unit kerja')
            <li class="{{ Request::is('survei') ? 'active' : '' }}">
                <a class="nav-link"
                    href="{{ url('credits') }}"><i class="fa-solid fa-circle-info"></i> <span>Realisasi Renja</span>
                </a>
            </li>
            @endif

            {{-- Monitoring --}}
            @if (Auth::user()->level == 'admin')
            <li class="{{ Request::is('survei') ? 'active' : '' }}">
                <a class="nav-link"
                    href="{{ url('credits') }}"><i class="fa fa-eye" aria-hidden="true"></i> <span>Monitoring</span>
                </a>
            </li>
            @endif

            {{-- Evaluasi --}}
            @if (Auth::user()->level == 'admin')
            <li class="{{ Request::is('survei') ? 'active' : '' }}">
                <a class="nav-link"
                    href="{{ url('credits') }}"><i class="fa-solid fa-file-pen"></i> <span>Evaluasi</span>
                </a>
            </li>
            @endif

        
    </aside>
</div>
