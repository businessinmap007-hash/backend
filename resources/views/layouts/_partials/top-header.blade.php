<!-- start header1 -->
{{--<div class="header1">--}}

{{--<div class="container">--}}
{{--<nav class="navbar navbar-default">--}}
{{--<div class="container-fluid">--}}

{{--<ul class="nav navbar-nav ">--}}
{{--<li class="nav-item"><a href="{{ route('cart') }}"><img--}}
{{--src="{{ request()->root() }}/public/assets/front/img/icon2.png"><span> مشترياتى</span></a>--}}
{{--</li>--}}

{{--<li class="nav-item">--}}
{{--@if(auth()->check())--}}
{{--<a href="{{ route('profile') }}">--}}
{{--<img src="{{ request()->root() }}/public/assets/front/img/icon3.png">--}}
{{--<span>حسابي</span>--}}
{{--</a>--}}
{{--@else--}}
{{--<a data-toggle="dropdown" class="nav-link dropdown-toggle" href="#">--}}
{{--<img src="{{ request()->root() }}/public/assets/front/img/icon3.png">--}}
{{--<span>دخول</span>--}}
{{--</a>--}}
{{--<ul class="dropdown-menu form-wrapper">--}}
{{--<li>--}}

{{--<form class="submission-form" action="{{ route('user.login') }}" method="post"--}}
{{--data-parsley-validate novalidate>--}}
{{--{{ csrf_field() }}--}}
{{--<input  hidden name="from-page"  value="login" />--}}

{{--<h5 class="hint-text"> تسجيل الدخول </h5>--}}
{{--<h6> قم بملأ بيانات تسجيل الدخول الخاصة بك </h6>--}}

{{--<div class="form-group">--}}
{{--<input type="email" name="email" class="form-control"--}}
{{--placeholder="البريد الإلكترونى"--}}
{{--required="required"--}}
{{--data-parsley-required-message="@lang('trans.email_required')"--}}
{{--/>--}}
{{--</div>--}}
{{--<div class="form-group">--}}
{{--<input type="password" name="password" class="form-control"--}}
{{--placeholder="كلمة المرور" required="required"--}}
{{--data-parsley-required-message="@lang('trans.password_required')"/>--}}
{{--</div>--}}
{{--<input type="submit" id="btn-submit" class="the-btn1 btn-block"--}}
{{--value="تسجيل الدخول">--}}
{{--<button type="submit" id="btn-submit" class="the-btn1 btn-block">تسجيل الدخول</button>--}}
{{--<div class="form-footer">--}}
{{--<a href="#">نسيت كلمة المرور ؟</a>--}}
{{--<a href="{{ route('get.user.login') }}"> لا تملك حساب ؟ سجل الآن </a>--}}
{{--</div>--}}
{{--</form>--}}
{{--</li>--}}
{{--</ul>--}}
{{--@endif--}}
{{--</li>--}}
{{--<li class="nav-item">--}}
{{--<a href="#">--}}
{{--<img src="{{ request()->root() }}/public/assets/front/img/icon1.png"><span>English</span>--}}
{{--</a>--}}
{{--</li>--}}
{{--</ul>--}}

{{--<div class="navbar-header navbar-right">--}}
{{--<a class="navbar-brand" href="{{ route('user.home') }}"><img--}}
{{--src="{{ request()->root() }}/public/assets/front/img/logo.png"></a>--}}
{{--</div>--}}

{{--</div>--}}
{{--</nav>--}}
{{--</div>--}}
{{--</div>--}}
{{--<!-- end header1 -->--}}



<!-- start header1 -->
<div class="header1">
    <div class="container">
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12 col-md-3">
                        <div class="navbar-header navbar-right">
                            <a class="navbar-brand" href="{{ route('user.home') }}"><img
                                        src="{{ request()->root() }}/public/assets/front/img/logo.png"></a>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-10">
                        <div class="top-search">
                            <form class="navbar-form navbar-left" action="{{ route('category.products') }}">
                                <div class="input-group">
                                    {{--<input type="text" class="form-control" placeholder="ابحث عن باسم المنتج">--}}

                                    <input type="text" class="form-control" name="s" value="{{ request('s') }}"
                                           placeholder="ابحث عن باسم المنتج">
                                    <div class="input-group-btn">
                                        <button class="submit" type="submit"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-3">
                        <ul class="nav navbar-nav">
                            <li><a href="{{ route('cart') }}"><img
                                            src="{{ request()->root() }}/public/assets/front//img/icon2.png"><span>مشترياتى</span></a>
                            </li>
                            <li><a href="account.html"><img
                                            src="{{ request()->root() }}/public/assets/front//img/icon3.png"><span>حسابى</span></a>
                            </li>
                            <li><a href="#"><img src="{{ request()->root() }}/public/assets/front//img/icon1.png"><span>English</span></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</div>
<!-- end header1 -->