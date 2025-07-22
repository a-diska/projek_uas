<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Dashboard</title>

    <!-- CSS & Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .nav-link {
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .nav-link:hover {
            background-color: #f0f4ff !important;
            color: #3f83f8 !important;
        }

        .nav-link.active {
            background-color: #e3ecfe !important;
            color: #3f83f8 !important;
        }

        .nav-link:hover .sb-nav-link-icon i,
        .nav-link.active .sb-nav-link-icon i {
            color: #3f83f8 !important;
        }
    </style>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-light bg-white shadow-sm border-bottom">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3 text-dark fw-bold" href="#">WORKSHOP</a>

        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm me-4 me-lg-0 text-dark" id="sidebarToggle" href="#">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Search -->
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." />
                <button class="btn btn-outline-dark" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>

        <!-- Navbar Right -->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-dark" id="navbarDropdown" href="#" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle fa-lg"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="navbarDropdown">
                    <li>
                        <a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#"><i class="fas fa-list-alt me-2"></i>Activity Log</a>
                    </li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li>
                        <a class="dropdown-item text-danger" href="#" onclick="handleLogout()">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>

    <!-- Sidebar & Content -->
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-light" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Core</div>

                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                            href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.role.index') ? 'active' : '' }}"
                            href="{{ route('admin.role.index') }}">
                            <i class="fas fa-user-shield"></i> Role
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.user.index') ? 'active' : '' }}"
                            href="{{ route('admin.user.index') }}">
                            <i class="fas fa-users"></i> User
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.workshop.index') ? 'active' : '' }}"
                            href="{{ route('admin.workshop.index') }}">
                            <i class="fas fa-chalkboard-teacher"></i> Workshop
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.pelayanan.index') ? 'active' : '' }}"
                            href="{{ route('admin.pelayanan.index') }}">
                            <i class="fas fa-file-contract"></i> Pelayanan
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.pengajuan.index') ? 'active' : '' }}"
                            href="{{ route('admin.pengajuan.index') }}">
                            <i class="fas fa-file-signature"></i> Pengajuan
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.verifikator.index') ? 'active' : '' }}"
                            href="{{ route('admin.verifikator.index') }}">
                            <i class="fas fa-user-check"></i> Verifikator
                        </a>
                        <hr>
                        <!-- Menu Log Dropdown -->
                        <a class="nav-link {{ request()->is('admin/logs/*') ? '' : 'collapsed' }}" href="#"
                            data-bs-toggle="collapse" data-bs-target="#collapseLogs"
                            aria-expanded="{{ request()->is('admin/logs/*') ? 'true' : 'false' }}"
                            aria-controls="collapseLogs">
                            <div class="sb-nav-link-icon">
                                <i class="fas fa-clipboard-list text-dark"></i> {{-- icon warna default/hitam --}}
                            </div>
                            Log
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>

                        <div class="collapse {{ request()->is('admin/logs/*') ? 'show' : '' }}" id="collapseLogs"
                            data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link {{ request()->routeIs('admin.logs.logactivity') ? 'active' : '' }}"
                                    href="{{ route('admin.logs.logactivity') }}">
                                    Log Activity
                                </a>

                                <a class="nav-link {{ request()->routeIs('admin.logs.logdatabase') ? 'active' : '' }}"
                                    href="{{ route('admin.logs.logdatabase') }}">
                                    Log Database
                                </a>
                                <a class="nav-link {{ request()->routeIs('admin.logs.logerror') ? 'active' : '' }}"
                                    href="{{ route('admin.logs.logerror') }}">
                                    Log Error
                                </a>
                                <a class="nav-link {{ request()->routeIs('admin.logs.logapproval') ? 'active' : '' }}"
                                    href="{{ route('admin.logs.logapproval') }}">
                                    Log Approval
                                </a>
                            </nav>
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Scroll to Top -->
    <button id="scrollTopBtn" title="Back to Top">↑</button>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/scripts.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
    <script src="js/datatables-simple-demo.js"></script>

    <!-- Active Link Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const currentUrl = window.location.href;
            const links = document.querySelectorAll('.nav-link');
            links.forEach(link => {
                if (link.href === currentUrl || currentUrl.startsWith(link.href)) {
                    link.classList.add('active');
                }
            });
        });

        const scrollBtn = document.getElementById('scrollTopBtn');
        window.onscroll = function() {
            scrollBtn.style.display = (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) ?
                "block" : "none";
        };
        scrollBtn.onclick = function() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        };
    </script>

    <!-- Logout Script -->
    <script>
        function handleLogout() {
            Swal.fire({
                title: 'Yakin ingin logout?',
                text: "Anda akan keluar dari sistem.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya, logout',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const token = localStorage.getItem('access_token');

                    if (!token) {
                        Swal.fire('Info', 'Anda sudah logout sebelumnya.', 'info');
                        setTimeout(() => {
                            window.location.href = '/login';
                        }, 1000);
                        return;
                    }

                    fetch('/api/logout', {
                            method: 'POST',
                            headers: {
                                'Authorization': `Bearer ${token}`,
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(response => {
                            if (response.success) {
                                localStorage.removeItem('access_token');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Logout Berhasil',
                                    text: 'Anda berhasil logout.',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = '/login';
                                });
                            } else {
                                Swal.fire('Gagal', response.message || 'Gagal logout. Silakan coba lagi.',
                                    'error');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire('Error', 'Terjadi kesalahan saat logout.', 'error');
                        });
                }
            });
        }
    </script>

    <script src="{{ asset('js/interceptor.js') }}"></script>
    <script src="{{ asset('js/auth.js') }}"></script>
</body>

</html>
