<!DOCTYPE html>
<html lang="pt-BR" id="html-theme">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Restaurante – Pedidos')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="/css/style.css" rel="stylesheet">
    <script>
    (function(){var t=document.documentElement.getAttribute('data-theme')||localStorage.getItem('restaurante-theme')||'light';document.documentElement.setAttribute('data-theme',t);})();
    </script>
</head>
<body class="app-body">
    @auth
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a class="sidebar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-cup-hot-fill"></i>
                <span class="sidebar-brand-text">Restaurante</span>
            </a>
            <button class="sidebar-toggle d-lg-none" type="button" aria-label="Fechar menu" id="sidebarClose">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <nav class="sidebar-nav">
            <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            <a class="sidebar-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">
                <i class="bi bi-cart3"></i>
                <span>Pedidos</span>
            </a>
            @if(Auth::user()->permission_level >= 2)
                <a class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                    <i class="bi bi-people"></i>
                    <span>Usuários</span>
                </a>
                <a class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>Relatórios</span>
                </a>
            @endif
        </nav>
        <div class="sidebar-footer">
            <div class="theme-toggle-wrapper">
                <span class="sidebar-label"><i class="bi bi-brightness-high"></i> Modo claro</span>
                <button type="button" class="theme-toggle" id="themeToggle" aria-label="Alternar tema">
                    <span class="theme-toggle-track">
                        <span class="theme-toggle-thumb"></span>
                    </span>
                </button>
                <span class="sidebar-label"><i class="bi bi-moon-stars"></i> Modo escuro</span>
            </div>
            <div class="sidebar-user">
                <i class="bi bi-person-circle"></i>
                <div class="sidebar-user-info">
                    <strong>{{ Auth::user()->name }}</strong>
                    <span class="badge badge-role">{{ Auth::user()->permission_name }}</span>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="w-100">
                @csrf
                <button type="submit" class="sidebar-btn-logout">
                    <i class="bi bi-box-arrow-right"></i> Sair
                </button>
            </form>
        </div>
    </aside>
    <button class="sidebar-overlay d-lg-none" id="sidebarOverlay" aria-hidden="true"></button>
    <header class="topbar d-lg-none">
        <button class="btn btn-link text-dark theme-toggle-mobile me-2" id="themeToggleMobile" aria-label="Alternar tema">
            <i class="bi bi-moon-stars" id="iconThemeMobile"></i>
        </button>
        <button class="btn btn-link text-dark" type="button" id="sidebarOpen" aria-label="Abrir menu">
            <i class="bi bi-list fs-4"></i>
        </button>
    </header>
    @endauth

    <main class="main-content" id="mainContent">
        @auth
        <div class="main-content-inner">
            <div class="d-none d-lg-flex align-items-center justify-content-end gap-2 mb-3">
                <span class="text-muted small"><i class="bi bi-brightness-high me-1"></i> Claro</span>
                <button type="button" class="theme-toggle theme-toggle-inline" id="themeToggleDesktop" aria-label="Alternar tema">
                    <span class="theme-toggle-track">
                        <span class="theme-toggle-thumb"></span>
                    </span>
                </button>
                <span class="text-muted small"><i class="bi bi-moon-stars me-1"></i> Escuro</span>
            </div>
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        </div>
        @endauth
        @guest
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        @endguest
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    (function() {
        var STORAGE_KEY = 'restaurante-theme';
        var DARK = 'dark';
        var LIGHT = 'light';
        function getStored() { return localStorage.getItem(STORAGE_KEY) || LIGHT; }
        function setStored(val) { localStorage.setItem(STORAGE_KEY, val); }
        function applyTheme(theme) {
            var html = document.getElementById('html-theme');
            if (!html) return;
            html.setAttribute('data-theme', theme);
            setStored(theme);
            var iconMobile = document.getElementById('iconThemeMobile');
            if (iconMobile) iconMobile.className = theme === DARK ? 'bi bi-sun' : 'bi bi-moon-stars';
        }
        function toggleTheme() {
            var current = document.getElementById('html-theme').getAttribute('data-theme') || getStored();
            applyTheme(current === DARK ? LIGHT : DARK);
        }
        document.addEventListener('DOMContentLoaded', function() {
            applyTheme(getStored());
            [ 'themeToggle', 'themeToggleDesktop', 'themeToggleMobile' ].forEach(function(id) {
                var el = document.getElementById(id);
                if (el) el.addEventListener('click', toggleTheme);
            });
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('sidebarOverlay');
            var openBtn = document.getElementById('sidebarOpen');
            var closeBtn = document.getElementById('sidebarClose');
            if (sidebar) {
                function openSidebar() { sidebar.classList.add('open'); document.body.style.overflow = 'hidden'; }
                function closeSidebar() { sidebar.classList.remove('open'); document.body.style.overflow = ''; }
                if (openBtn) openBtn.addEventListener('click', openSidebar);
                if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
                if (overlay) overlay.addEventListener('click', closeSidebar);
            }
        });
    })();
    </script>
    @yield('scripts')
</body>
</html>
