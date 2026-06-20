<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    @if(!request()->routeIs(['dashboard', 'reports.*', 'users.*', 'profile.*','divisi.*','jabatan.*','sumber.*',]))
                    <form method="GET"
                        action="{{ url()->current() }}"
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">

                        <div class="input-group">

                            <input type="text"
                                id="globalSearch"
                                name="q"
                                value="{{ request('q') }}"
                                class="form-control bg-light border-0 small"
                                placeholder="Search..."
                                autocomplete="off">

                            {{-- Tombol clear --}}
                            @if(request('q'))
                            <div class="input-group-append">
                                <button type="button"
                                        id="clearSearch"
                                        class="btn btn-light">
                                    ✕
                                </button>
                            </div>
                            @endif

                            <div class="input-group-append">
                                <button class="btn btn-success" type="submit">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>

                        </div>
                    </form>
                    @endif


                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <span class="badge badge-danger badge-counter">
                                        {{ auth()->user()->unreadNotifications->count() }}
                                    </span>
                                @endif
                            </a>
                            
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header bg-success border-success"> {{-- Menggunakan tema hijau BAZNAS --}}
                                    Pemberitahuan Sistem
                                </h6>
                                
                                @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('notifications.read', $notification->id) }}">
                                        <div class="mr-3">
                                            <div class="icon-circle {{ $notification->data['bg_color'] ?? 'bg-success' }}">
                                                <i class="{{ $notification->data['icon'] ?? 'fas fa-bell' }} text-white"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="small text-gray-500">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </div>
                                            <span class="font-weight-bold d-block text-dark">
                                                {{ $notification->data['title'] }}
                                            </span>
                                            <span class="small text-gray-600">
                                                {{ $notification->data['message'] }}
                                            </span>
                                        </div>
                                    </a>
                                @empty
                                    <div class="dropdown-item text-center small text-gray-500 py-3">
                                        <i class="fas fa-check-circle text-success mr-1"></i> Tidak ada notifikasi baru
                                    </div>
                                @endforelse
                                
                                <a class="dropdown-item text-center small text-gray-500" href="#">Lihat Semua Pemberitahuan</a>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                
                                @auth
                                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ auth()->user()->name }}</span>
                                @else
                                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">Guest</span>
                                @endauth
                        
                                {{-- <span class="mr-2 d-none d-lg-inline text-gray-600 small">Douglas McGee</span> --}}
                                <img class="img-profile rounded-circle"
                                    src="{{ asset('/') }}sbadmin2/img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <script>
                document.getElementById('clearSearch')?.addEventListener('click', function () {
                    window.location.href = "{{ url()->current() }}";
                });
                </script>
