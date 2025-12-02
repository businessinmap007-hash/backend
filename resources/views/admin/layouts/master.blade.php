<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $main->designDirection() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Dashboard">

    <title>@yield('title') | @lang('global.dashboard')</title>

    <link rel="shortcut icon" href="{{ asset('assets/admin/images/favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css?family=Tajawal" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    @include('admin.layouts._partials.styles')
    @yield('styles')

<style>
    /* ===== GLOBAL ===== */
    body {
        background: #f5f6fa;
        overflow-x: hidden;
        font-family: "Tajawal", sans-serif;
        padding-top: 90px !important; /* ارتفاع الهيدر بعد الترتيب */
        color: #344054;
    }

    a { text-decoration: none !important; }

    /* ===== HEADER (TOPNAV) ===== */
    #topnav.topnav-modern {
        background: #ffffff;
        box-shadow: 0 2px 10px rgba(30, 41, 59, 0.08);
        position: fixed;
        top: 0;
        right: 0;
        left: 0;
        z-index: 1001;
    }

    .topbar-main {
        padding: 8px 0;
    }

    /* كل عناصر الهيدر في سطر واحد */
    .topbar-main .container {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .topbar-left {
        display: flex;
        align-items: center;
    }

    .logo-modern {
        font-size: 22px;
        font-weight: 800;
        letter-spacing: 1px;
        color: #1a73e8 !important;
        display: inline-flex;
        align-items: center;
        margin: 0;
    }

    /* الجهة اليمنى (صورة المستخدم + زر السايدبار) */
    .topbar-main .d-flex.align-items-center {
        display: flex !important;
        align-items: center !important;
        gap: 16px;
    }

    .user-box-modern {
        display: flex;
        align-items: center;
    }

    .user-avatar-modern {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e5e7eb;
        transition: .2s;
        display: inline-block;
    }

    .user-avatar-modern:hover { transform: scale(1.05); }

    .modern-dropdown {
        min-width: 180px;
        padding: 6px 0;
        border-radius: 10px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .modern-dropdown li a {
        font-size: 14px;
        padding: 10px 15px;
        color: #374151;
    }

    .modern-dropdown li a:hover {
        background: #eef2ff;
        color: #1a73e8;
    }

    .navbar-toggle-modern {
        cursor: pointer;
        padding: 8px 12px;
        border-radius: 8px;
        transition: 0.2s;
    }

    .navbar-toggle-modern:hover { background: #eef2ff; }

    .modern-lines span {
        display: block;
        width: 22px;
        height: 3px;
        margin: 3px 0;
        background: #4b5563;
        border-radius: 30px;
    }

    /* ===== LAYOUT (SIDEBAR + CONTENT) ===== */
    .admin-main {
        display: flex;
        flex-direction: row-reverse; /* القائمة على اليمين */
        min-height: calc(100vh - 90px);
    }

    .admin-sidebar {
        position: fixed;
        top: 90px; /* تحت الهيدر مباشرة */
        right: 0;
        width: 240px;
        height: calc(100vh - 90px);
        background: #ffffff;
        border-left: 1px solid #e5e7eb;
        box-shadow: -2px 0 8px rgba(15, 23, 42, 0.03);
        overflow-y: auto;
        z-index: 900;
        padding-top: 10px;
    }

    .admin-content {
        flex: 1;
        padding: 24px 24px 40px;
        margin-right: 240px;
    }

    /* ===== SIDEBAR MENU ===== */
    .admin-sidebar-menu { list-style: none; padding: 0; margin:0; }

    .admin-sidebar-link {
        display: flex;
        align-items: center;
        padding: 10px 18px;
        font-size: 14px;
        color: #4b5563;
        transition: .2s;
        border-radius: 10px 0 0 10px;
        margin-left: 8px;
    }

    .admin-sidebar-link i { font-size: 18px; margin-left: 10px; }

    .admin-sidebar-link:hover {
        background: #eef2ff;
        color: #1a73e8;
        transform: translateX(-2px);
    }

    .admin-sidebar-link.active {
        background: #1a73e8;
        color: #fff;
    }

    .admin-sidebar-item.open .admin-sidebar-submenu { display: block; }

    .admin-sidebar-submenu li a {
        font-size: 13px;
        padding: 6px 4px;
        color: #6b7280;
    }

    .admin-sidebar-submenu li a:hover {
        background: #f3f4ff;
        color: #1a73e8;
    }

    /* ===== RESPONSIVE (MOBILE) ===== */
    @media (max-width: 991px) {
        .admin-main { display: block; }

        .admin-sidebar {
            right: -260px;
            transition: right .3s;
        }

        .admin-sidebar.open { right: 0; }

        .admin-content {
            margin-right: 0;
            padding: 16px 12px;
        }
    }
</style>

</head>
<body>

<div id="loading-spinner" style="position:fixed;top:0;width:100%;height:100%;background:#0005;z-index:999999;display:none;">
    <div style="position:absolute;top:40%;left:47%;padding:30px 50px;background:#000;border-radius:10px;color:#fff;font-size:32px;opacity:.6;">
        <i class="fas fa-circle-notch fa-spin"></i>
    </div>
</div>

@include('admin.layouts._partials.header')

<div class="admin-main">
    @include('admin.layouts._partials.sidebar')

    <div class="admin-content">
        <div class="container-fluid">
            @yield('content')
        </div>
    </div>
</div>

@include('admin.layouts._partials.scripts')
@yield('scripts')

<script>
    (function () {
        const toggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('adminSidebar');

        if (toggle && sidebar) {
            toggle.addEventListener('click', () => {
                sidebar.classList.toggle('open');
            });
        }
    })();
</script>

</body>
</html>
