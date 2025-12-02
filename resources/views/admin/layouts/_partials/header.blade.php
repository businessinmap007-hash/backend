<!-- Navigation Bar-->
<header id="topnav" class="topnav-modern">

    <div class="topbar-main">
        <div class="container d-flex justify-content-between align-items-center">

            <!-- LOGO -->
            <div class="topbar-left d-flex align-items-center">
                <a href="{{ route('admin.home') }}" class="logo-modern">
                    BIM
                </a>
            </div>

            <!-- RIGHT MENU -->
            <div class="d-flex align-items-center">

                <!-- USER DROPDOWN -->
                <div class="dropdown user-box-modern">
                    <a href="#" class="dropdown-toggle profile-modern" data-toggle="dropdown">
                        <img src="{{ $helper->getDefaultImage(auth()->user()->image, asset('assets/admin/images/default.png')) }}"
                             alt="user-img" class="user-avatar-modern">
                    </a>

                    <ul class="dropdown-menu dropdown-menu-right modern-dropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('user.profile') }}?profileId={{ auth()->id() }}">
                                <i class="ti-user m-r-5"></i> @lang('maincp.personal_page')
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('users.edit', auth()->id()) }}">
                                <i class="ti-settings m-r-5"></i> @lang('global.settings')
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="ti-power-off m-r-5"></i> @lang('maincp.log_out')
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- MOBILE SIDEBAR BUTTON -->
                <a class="navbar-toggle-modern" id="sidebarToggle">
                    <div class="modern-lines">
                        <span></span><span></span><span></span>
                    </div>
                </a>

            </div>

        </div>
    </div>

    <form id="logout-form" action="{{ route('administrator.logout') }}" method="POST" style="display:none;">
        @csrf
    </form>
</header>
<!-- End Navigation Bar-->
