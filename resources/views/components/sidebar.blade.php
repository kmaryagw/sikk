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
            <li class="nav-item dropdown {{ $type_menu === 'masterdata' ? 'active' : '' }}">
                <a href="#"
                    class="nav-link has-dropdown"
                    data-toggle="dropdown"><i class="fa-solid fa-basketball"></i> <span>Master Data</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('user') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('user') }}">User</a>
                    </li>
                    <li class="{{ Request::is('transparent-sidebar') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('transparent-sidebar') }}">Program Studi</a>
                    </li>
                    <li class="{{ Request::is('layout-top-navigation') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('layout-top-navigation') }}">Unit Kerja</a>
                    </li>
                    <li class="{{ Request::is('layout-top-navigation') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('layout-top-navigation') }}">Rencana Strategis</a>
                    </li>
                    <li class="{{ Request::is('layout-top-navigation') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('layout-top-navigation') }}">Tahun</a>
                    </li>
                    <li class="{{ Request::is('layout-top-navigation') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('layout-top-navigation') }}">Periode Monev</a>
                    </li>

                </ul>
            </li>

            {{-- Standar --}}
            
            <li class="{{ Request::is('survei') ? 'active' : '' }}">
                <a class="nav-link"
                    href="{{ url('credits') }}"><i class="fa fa-paper-plane" aria-hidden="true"></i> <span>Standar</span>
                </a>
            </li>

            {{-- Indikator Kinerja Utama/Tambahan --}}
            
            <li class="{{ Request::is('survei') ? 'active' : '' }}">
                <a class="nav-link"
                    href="{{ url('credits') }}"><i class="fa-solid fa-circle-info"></i> <span>Indikator Kinerja Utama/Tambahan</span>
                </a>
            </li>

            {{-- Target Capaian --}}
            
            <li class="{{ Request::is('survei') ? 'active' : '' }}">
                <a class="nav-link"
                    href="{{ url('credits') }}"><i class="fa-solid fa-award"></i> <span>Target Capaian</span>
                </a>
            </li>

            {{-- Program Kerja --}}
            
            <li class="{{ Request::is('survei') ? 'active' : '' }}">
                <a class="nav-link"
                    href="{{ url('credits') }}"><i class="fa-solid fa-book"></i> <span>Program Kerja</span>
                </a>
            </li>

            {{-- Realisasi Renja --}}
            
            <li class="{{ Request::is('survei') ? 'active' : '' }}">
                <a class="nav-link"
                    href="{{ url('credits') }}"><i class="fa-solid fa-circle-info"></i> <span>Realisasi Renja</span>
                </a>
            </li>

            {{-- Monitoring --}}
            
            <li class="{{ Request::is('survei') ? 'active' : '' }}">
                <a class="nav-link"
                    href="{{ url('credits') }}"><i class="fa fa-eye" aria-hidden="true"></i> <span>Monitoring</span>
                </a>
            </li>

            {{-- Evaluasi --}}
            
            <li class="{{ Request::is('survei') ? 'active' : '' }}">
                <a class="nav-link"
                    href="{{ url('credits') }}"><i class="fa-solid fa-file-pen"></i> <span>Evaluasi</span>
                </a>
            </li>


        
    </aside>
</div>
