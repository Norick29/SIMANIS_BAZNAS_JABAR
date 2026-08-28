<ul class="navbar-nav bg-gradient-success sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand (SIMASET BAZNAS) -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <img src="{{ asset('image/logo-baznas.png') }}" alt="Logo" style="width: 70px; height:auto;">
        <div class="sidebar-brand-text mx-3">SIMASET</div>
    </a>

    <!-- Divider Awal -->
    <hr class="sidebar-divider my-0">

    <!-- ================================================================== -->
    <!-- 1. MENU DASHBOARD (SEMUA ROLE BISA AKSES) -->
    <!-- ================================================================== -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- ================================================================== -->
    <!-- KHUSUS SUPER ADMIN: MENU MANAJEMEN USER (BERDIRI SENDIRI) -->
    <!-- Sesuai Wireframe Gambar 3.41 Laporan Skripsi -->
    <!-- ================================================================== -->
    @if (auth()->user()->role == 'super admin')
        <div class="sidebar-heading">
            User Management
        </div>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('users.index') }}">
                <i class="fas fa-fw fa-users-cog"></i>
                <span>Manajemen User</span>
            </a>
        </li>
        <!-- Divider Pembatas -->
        <hr class="sidebar-divider">
    @endif

    <!-- ================================================================== -->
    <!-- 2. MENU MONITORING (KHUSUS KA.DEPT & KA.DIV) -->
    <!-- ================================================================== -->
    @if (auth()->user()->role == 'kadept' || auth()->user()->role == 'kadiv')
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMaster"
                aria-expanded="true" aria-controls="collapseMaster">
                <i class="fas fa-fw fa-database"></i>
                <span>Monitoring</span>
            </a>
            <div id="collapseMaster" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{ route('penyusutan.index') }}">Penyusutan Aset</a>
                    <a class="collapse-item" href="{{ route('penghapusan.index') }}">Penghapusan Aset</a>
                    <a class="collapse-item" href="{{ route('riwayat.pindah') }}">Riwayat Pindah</a>
                </div>
            </div>
        </li>
    @endif


    <!-- ================================================================== -->
    <!-- 3. MENU DATA MASTER (HANYA SUPER ADMIN & STAF) -->
    <!-- Kita toleransi pengecekan kata 'staff' dengan dua f maupun satu f -->
    <!-- ================================================================== -->
    @if (auth()->user()->role == 'super admin' || auth()->user()->role == 'staff' || auth()->user()->role == 'staf')
        
        <div class="sidebar-heading">
            Master Data
        </div>

        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMaster"
                aria-expanded="true" aria-controls="collapseMaster">
                <i class="fas fa-fw fa-database"></i>
                <span>Data Master</span>
            </a>
            <div id="collapseMaster" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    
                    <!-- SUB-MENU STRUKTURAL ORGANISASI (KHUSUS SUPER ADMIN) -->
                    @if (auth()->user()->role == 'super admin')
                        <a class="collapse-item" href="{{ route('amil.index') }}">Data Amil</a>
                        <a class="collapse-item" href="{{ route('divisi.index') }}">Data Divisi</a>
                        <a class="collapse-item" href="{{ route('jabatan.index') }}">Data Jabatan</a>
                        <a class="collapse-item" href="{{ route('master-penyusutan.index') }}">Data Penyusutan</a>
                        <hr class="my-1">
                    @endif

                    <!-- SUB-MENU OPERASIONAL LOGISTIK (SUPER ADMIN & STAF) -->
                    <a class="collapse-item" href="{{ route('barang.index') }}">Data Barang</a>
                    <a class="collapse-item" href="{{ route('kategori.index') }}">Data Kategori</a>
                    <a class="collapse-item" href="{{ route('jenis.index') }}">Data Jenis</a>
                    <a class="collapse-item" href="{{ route('lokasi.index') }}">Data Lokasi</a>
                    <a class="collapse-item" href="{{ route('sumber.index') }}">Data Sumber Dana</a>
                </div>
            </div>
        </li>

        <!-- MENU DATA ASET (SUPER ADMIN & STAF) -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#asset"
                aria-expanded="true" aria-controls="asset">
                <i class="fas fa-fw fa-box"></i>
                <span>Data Aset</span>
            </a>
            <div id="asset" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{ route('assets.index') }}">Data Asset</a>
                    <a class="collapse-item" href="{{ route('maintenances.index') }}">Maintenance</a>
                    <a class="collapse-item" href="{{ route('riwayat.pindah') }}">Riwayat Pindah</a>
                    <a class="collapse-item" href="{{ route('hilang.index') }}">Aset Hilang</a>
                    <a class="collapse-item" href="{{ route('penyusutan.index') }}">Penyusutan Aset</a>
                    <a class="collapse-item" href="{{ route('penghapusan.index') }}">Penghapusan Aset</a>
                </div>
            </div>
        </li>

    @endif


    <!-- ================================================================== -->
    <!-- 4. MENU APPROVAL (HANYA SUPER ADMIN, KADEPT, & KADIV) -->
    <!-- ================================================================== -->
    @if (auth()->user()->role == 'super admin' || auth()->user()->role == 'kadept' || auth()->user()->role == 'kadiv')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('approvals.index') }}">
                <i class="fas fa-fw fa-clipboard-check"></i>
                <span>Approval</span>
            </a>
        </li>
    @endif


    <!-- Divider Sektor Laporan -->
    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Report
    </div>

    <!-- ================================================================== -->
    <!-- 5. MENU LAPORAN (SEMUA ROLE BISA AKSES) -->
    <!-- ================================================================== -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('reports.index') }}">
            <i class="fas fa-fw fa-file-alt"></i>
            <span>Laporan</span>
        </a>
    </li>


    <!-- Divider Penutup Komponen Sidebar -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Tombol Bulat Minimize SB Admin 2) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
