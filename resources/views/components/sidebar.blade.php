<style>
    .sidebar-mini .sidebar-menu li a {
        justify-content: center;
        padding: 12px 0;
    }

    .sidebar-mini .sidebar-menu li a i {
        margin-right: 0;
        font-size: 20px;
        text-align: center;
        width: 100%;
    }

    .main-siderbar{
        font-size: 1rem
    }
</style>
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
                <a href="{{ url('dashboard') }}" class="nav-link">
                    <i class="fas fa-fire"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            {{-- Master Surat --}}
            @if (Auth::user()->role == 'admin')
             <li class="nav-item dropdown {{ $type_menu === 'mastersurat' ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fa-solid fa-file"></i> <span>Master Surat</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('organisasijabatan') || (isset($sub_menu) && $sub_menu === 'organisasijabatan') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('organisasijabatan') }}"><i class="fas fa-sitemap"></i>Organisasi Jabatan</a>
                    </li>
                    <li class="{{ Request::is('suratfungsi') || (isset($sub_menu) && $sub_menu === 'suratfungsi') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('suratfungsi') }}"><i class="fas fa-tasks"></i>Surat Fungsi</a>
                    </li>
                    <li class="{{ Request::is('suratperihal') || (isset($sub_menu) && $sub_menu === 'suratperihal') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('suratperihal') }}"><i class="fas fa-tag"></i>Surat Perihal</a>
                    </li>
                    <li class="{{ Request::is('suratlingkup') || (isset($sub_menu) && $sub_menu === 'suratlingkup') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('suratlingkup') }}"><i class="fas fa-folder-open"></i>Surat Lingkup</a>
                    </li>
                </ul>
            </li>
            @endif

            {{-- Surat --}}
            @if (Auth::user()->role == 'admin')
             <li class="nav-item dropdown {{ $type_menu === 'surat' ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fa-solid fa-envelope"></i> <span>Surat</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('datanomorsurat') || (isset($sub_menu) && $sub_menu === 'datanomorsurat') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('datanomorsurat') }}"><i class="fas fa-list-ol"></i>Data Nomor Surat</a>
                    </li>
                    <li class="{{ Request::is('menungguvalidasi') || (isset($sub_menu) && $sub_menu === 'menungguvalidasi') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('menungguvalidasi') }}"><i class="fas fa-hourglass-half"></i>Perlu Validasi</a>
                    </li>
                </ul>
            </li>
            @endif

            {{-- Nomor Surat --}}
            @if (Auth::user()->role == 'unit kerja')
            <li class="{{ $type_menu === 'nomorsurat' ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('nomorsurat') }}">
                    <i class="fas fa-file-invoice" aria-hidden="true"></i>
                    <span>Nomor Surat</span>
                </a>
            </li>
            @endif

            {{-- Master Data --}}
            @if (Auth::user()->role == 'admin')
            <li class="nav-item dropdown {{ $type_menu === 'masterdata' ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fa-solid fa-basketball"></i> <span>Master Data</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('user') || (isset($sub_menu) && $sub_menu === 'user') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('user') }}"><i class="fas fa-user"></i>User</a>
                    </li>
                    <li class="{{ Request::is('fakultas') || (isset($sub_menu) && $sub_menu === 'fakultas') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('fakultasn') }}"><i class="fas fa-school"></i>Fakultas</a>
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
                    {{-- <li class="{{ Request::is('periodemonev') || (isset($sub_menu) && $sub_menu === 'periodemonev') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('periodemonev') }}"><i class="fas fa-clock"></i>Periode Monev</a>
                    </li> --}}
                    <li class="{{ Request::is('standar') || (isset($sub_menu) && $sub_menu === 'standar') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('standar') }}"><i class="fa fa-paper-plane"></i>Standar</a>
                    </li>
                    <li class="{{ Request::is('indikatorkinerja') || (isset($sub_menu) && $sub_menu === 'indikatorkinerja') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('indikatorkinerja') }}"><i class="fas fa-bullseye"></i>IKU/IKT</a>
                    </li>
                    <li class="{{ Request::is('announcement') || (isset($sub_menu) && $sub_menu === 'announcement') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('announcement.index') }}">
                            <i class="fas fa-bullhorn"></i> Pengumuman
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            {{-- @if (Auth::user()->role == 'admin')
            <li class="{{ $type_menu === 'periode-monitoring' ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('periode-monitoring') }}">
                    <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                    <span>Periode Monitoring</span>
                </a>
            </li>
            @endif --}}


            {{-- Setting IKU --}}
            
            {{-- @if (Auth::user()->role== 'admin')
            <li class="{{ $type_menu === 'SettingIKU' ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('settingiku') }}">
                    <i class="fa fa-gears" aria-hidden="true"></i>
                    <span>Set IKU/T per-Tahun</span>
                </a>
            </li>
            @endif --}}

            {{-- Target Capaian Prodi --}}
            @if (Auth::user()->role == 'prodi')
            <li class="{{ $type_menu === 'targetcapaian' ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('targetcapaianprodi') }}">
                    <i class="fa-solid fa-award"></i> 
                    <span>Target</span>
                </a>
            </li>
            @endif
            @if (Auth::user()->role == 'admin')
            <li class="{{ $type_menu === 'targetcapaian' ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('targetcapaian') }}">
                    <i class="fa-solid fa-award"></i> 
                    <span>Target</span>
                </a>
            </li>
            @endif

            {{-- Program Kerja --}}
            {{-- @if (Auth::user()->role == 'prodi'|| Auth::user()->role == 'unit kerja' || Auth::user()->role == 'admin')
            <li class="{{ $type_menu === 'programkerja' ? 'active' : '' }}">
                <a class="nav-link"
                    href="{{ url('programkerja') }}"><i class="fa-solid fa-book"></i> <span>Program Kerja</span>
                </a>
            </li>
            @endif --}}

            {{-- Realisasi Renja --}}
            {{-- @if (Auth::user()->role == 'admin'|| Auth::user()->role == 'prodi'|| Auth::user()->role == 'unit kerja')
            <li class="{{ $type_menu === 'realisasirenja' ? 'active' : '' }}">
                <a class="nav-link"
                    href="{{ url('realisasirenja') }}"><i class="fa-solid fa-tasks"></i> <span>Realisasi Renja</span>
                </a>
            </li>
            @endif --}}

            {{-- Monitoring Renja--}}
            {{-- @if (Auth::user()->role == 'admin' || Auth::user()->role == 'unit kerja' || Auth::user()->role == 'prodi')
            <li class="{{ $type_menu === 'monitoring' ? 'active' : '' }}">
                <a class="nav-link"
                    href="{{ url('monitoring') }}"><i class="fa fa-eye" aria-hidden="true"></i> <span>Monitoring Renja</span>
                </a>
            </li>
            @endif --}}

            {{-- Monitoring IKU/T --}}
            @if (Auth::user()->role == 'admin'|| Auth::user()->role == 'fakultas' || Auth::user()->role == 'unit kerja')
            <li class="{{ $type_menu === 'monitoringiku' ? 'active' : '' }}">
                <a class="nav-link"
                    href="{{ url('monitoringiku') }}"><i class="fa-solid fa-eye"></i> <span>Monitoring IKU/T</span>
                </a>
            </li>
            @endif

            @if (Auth::user()->role == 'admin'|| Auth::user()->role == 'prodi' || Auth::user()->role == 'unit kerja' || Auth::user()->role == 'fakultas')
             <li class="nav-item dropdown {{ $type_menu === 'laporan' ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fa-solid fa-file-alt"></i> <span>Laporan</span></a>
                <ul class="dropdown-menu">
                    @if (Auth::user()->role == 'admin'|| Auth::user()->role == 'prodi' || Auth::user()->role == 'unit kerja' || Auth::user()->role == 'fakultas')
                    <li class="{{ Request::is('laporan-iku') || (isset($sub_menu) && $sub_menu === 'laporan-iku') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('laporan-iku') }}"><i class="fas fa-bullseye"></i>IKU/IKT</a>
                    </li>
                    @endif
                    {{-- @if (Auth::user()->role == 'admin'|| Auth::user()->role == 'prodi' || Auth::user()->role == 'unit kerja' || Auth::user()->role == 'fakultas')
                    <li class="{{ $type_menu === 'laporan-renja' ? 'active' : '' }}">
                        <a class="nav-link" 
                            href="{{ url('laporan-renja') }}"><i class="fas fa-file-alt"></i><span>Renja</span>
                        </a>
                    </li>
                    @endif --}}
                    {{-- @if (Auth::user()->role == 'admin'|| Auth::user()->role == 'prodi' || Auth::user()->role == 'unit kerja' || Auth::user()->role == 'fakultas')
                    <li class="{{ Request::is('laporan-monitoring') || (isset($sub_menu) && $sub_menu === 'laporan-monitoring') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('laporan-monitoring') }}"><i class="fas fa-eye"></i>Hasil Monitoring</a>
                    </li>
                    <li class="{{ Request::is('laporan-evaluasi') || (isset($sub_menu) && $sub_menu === 'laporan-evaluasi') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('laporan-evaluasi') }}"><i class="fas fa-file-pen"></i>Evaluasi</a>
                    </li>
                    @endif --}}
                </ul>
            </li>
            @endif
    </aside>
</div>
