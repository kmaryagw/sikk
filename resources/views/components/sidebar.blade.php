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
            @if (Auth::user()->role == 'admin')
             <li class="nav-item dropdown {{ $type_menu === 'masterdata' ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fa-solid fa-basketball"></i> <span>Master Data</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('user') || (isset($sub_menu) && $sub_menu === 'user') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('user') }}"><i class="fas fa-user"></i>User</a>
                    </li>
                    <li class="{{ Request::is('prodi') || (isset($sub_menu) && $sub_menu === 'prodi') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('prodi') }}"><i class="fas fa-building-columns"></i>Program Studi</a>
                    </li>
                    <li class="{{ Request::is('unit') || (isset($sub_menu) && $sub_menu === 'unit') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('unit') }}"><i class="fas fa-briefcase"></i>Unit Kerja</a>
                    </li>
                    <li class="{{ Request::is('renstra') || (isset($sub_menu) && $sub_menu === 'renstra') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('renstra') }}"><i class="fas fa-file-alt"></i>Rencana Strategis</a>
                    </li>
                    <li class="{{ Request::is('tahun') || (isset($sub_menu) && $sub_menu === 'tahun') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('tahun') }}"><i class="fas fa-calendar-alt"></i>Tahun</a>
                    </li>
                    <li class="{{ Request::is('periodemonev') || (isset($sub_menu) && $sub_menu === 'periodemonev') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('periodemonev') }}"><i class="fas fa-clock"></i>Periode Monev</a>
                    </li>
                </ul>
            </li>
            @endif

            @if (Auth::user()->role == 'admin')
            <li class="{{ $type_menu === 'periode-monitoring' ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('periode-monitoring') }}">
                    <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                    <span>Periode Monitoring</span>
                </a>
            </li>
            @endif

            {{-- Standar --}}
            @if (Auth::user()->role == 'admin')
            <li class="{{ $type_menu === 'standar' ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('standar') }}">
                    <i class="fa fa-paper-plane" aria-hidden="true"></i>
                    <span>Standar</span>
                </a>
            </li>
            @endif

            {{-- Indikator Kinerja Utama/Tambahan --}}
            
            @if (Auth::user()->role== 'admin'|| Auth::user()->role == 'prodi')
            <li class="{{ $type_menu === 'indikatorkinerjautama' ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('indikatorkinerjautama') }}">
                    <i class="fa fa-bullseye" aria-hidden="true"></i>
                    <span>Indikator Kinerja Utama/Tambahan</span>
                </a>
            </li>
            @endif

            {{-- Target Capaian --}}
            @if (Auth::user()->role == 'admin'|| Auth::user()->role == 'prodi')
            <li class="{{ $type_menu === 'targetcapaian' ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('targetcapaian') }}">
                    <i class="fa-solid fa-award"></i> 
                    <span>Target Capaian</span>
                </a>
            </li>
            @endif

            {{-- Program Kerja --}}
            @if (Auth::user()->role == 'admin'|| Auth::user()->role == 'prodi'|| Auth::user()->role == 'unit kerja')
            <li class="{{ $type_menu === 'programkerja' ? 'active' : '' }}">
                <a class="nav-link"
                    href="{{ url('programkerja') }}"><i class="fa-solid fa-book"></i> <span>Program Kerja</span>
                </a>
            </li>
            @endif

            {{-- Realisasi Renja --}}
            @if (Auth::user()->role == 'admin'|| Auth::user()->role == 'prodi'|| Auth::user()->role == 'unit kerja')
            <li class="{{ $type_menu === 'realisasirenja' ? 'active' : '' }}">
                <a class="nav-link"
                    href="{{ url('realisasirenja') }}"><i class="fa-solid fa-tasks"></i> <span>Realisasi Renja</span>
                </a>
            </li>
            @endif

            {{-- Monitoring --}}
            @if (Auth::user()->role == 'admin')
            <li class="{{ $type_menu === 'monitoring' ? 'active' : '' }}">
                <a class="nav-link"
                    href="{{ url('monitoring') }}"><i class="fa fa-eye" aria-hidden="true"></i> <span>Monitoring</span>
                </a>
            </li>
            @endif

            {{-- Evaluasi --}}
            @if (Auth::user()->role == 'admin')
            <li class="{{ $type_menu === 'evaluasi' ? 'active' : '' }}">
                <a class="nav-link"
                    href="{{ url('evaluasi') }}"><i class="fa-solid fa-file-pen"></i> <span>Evaluasi</span>
                </a>
            </li>
            @endif

            {{-- Laporan --}}
            @if (Auth::user()->role == 'admin')
            <li class="{{ $type_menu === 'laporan' ? 'active' : '' }}">
                <a class="nav-link"
                    href="{{ url('laporan') }}"><i class="fa-solid fa-file-alt"></i> <span>Laporan</span>
                </a>
            </li>
            @endif

        
    </aside>
</div>
