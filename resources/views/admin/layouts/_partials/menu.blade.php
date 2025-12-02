<!-- Top Menu -->
<div class="navbar-custom">
    <div class="container-fluid">

        <div id="navigation">
            <!-- Navigation Menu-->
            <ul class="navigation-menu">

                <li>
                    <a href="{{ route('admin.home') }}">
                        <i class="zmdi zmdi-view-dashboard"></i>
                        <span> الرئيسية </span>
                    </a>
                </li>

                <li class="has-submenu">
                    <a href="#">
                        <i class="zmdi zmdi-layers"></i>
                        <span> إدارة النظام </span>
                    </a>
                    <ul class="submenu">
                        <li><a href="{{ route('users.index') }}">مديري النظام</a></li>
                        @if(auth()->user()->roles()->whereName('owner')->first())
                        <li><a href="{{ route('roles.index') }}">الصلاحيات والأدوار</a></li>
                        @endif
                    </ul>
                </li>

                <li class="has-submenu">
                    <a href="#">
                        <i class="zmdi zmdi-accounts"></i>
                        <span> إدارة المستخدمين </span>
                    </a>
                    <ul class="submenu">
                        <li><a href="{{ route('clients.index') }}">المستخدمين</a></li>
                        <li><a href="{{ route('business.index') }}">العملاء</a></li>
                    </ul>
                </li>

                <li><a href="{{ route('categories.index') }}">
                    <i class="zmdi zmdi-accounts-outline"></i> الأقسام
                </a></li>

                <li><a href="{{ route('posts.index') }}">
                    <i class="zmdi zmdi-collection-text"></i> المنشورات
                </a></li>

                <li><a href="{{ route('jobs.index') }}">
                    <i class="zmdi zmdi-case"></i> الوظائف
                </a></li>

                <li><a href="{{ route('sponsors.index') }}">
                    <i class="zmdi zmdi-flag"></i> الإعلانات
                </a></li>

                <li><a href="{{ route('transactions.index') }}">
                    <i class="zmdi zmdi-balance-wallet"></i> المعاملات المالية
                </a></li>

                <li><a href="{{ route('albums.index') }}">
                    <i class="zmdi zmdi-collection-image"></i> الألبومات
                </a></li>

                <li class="has-submenu">
                    <a href="#">
                        <i class="zmdi zmdi-ticket-star"></i>
                        <span> أكواد الخصم </span>
                    </a>
                    <ul class="submenu">
                        <li><a href="{{ route('coupons.index') }}">مشاهدة الأكواد</a></li>
                        <li><a href="{{ route('coupons.create') }}">إضافة كود خصم</a></li>
                    </ul>
                </li>

                <li class="has-submenu">
                    <a href="#">
                        <i class="zmdi zmdi-globe"></i>
                        <span> الدول والمدن </span>
                    </a>
                    <ul class="submenu">
                        <li><a href="{{ route('locations.index') }}">مشاهدة الدول</a></li>
                        <li><a href="{{ route('locations.create') }}">إضافة دولة أو مدينة</a></li>
                    </ul>
                </li>

            </ul>
            <!-- End navigation menu -->
        </div>

    </div>
</div>
