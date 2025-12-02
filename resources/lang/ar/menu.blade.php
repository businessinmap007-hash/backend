<div class="navbar-custom">
    <div class="container">
        <div id="navigation">
            <!-- Navigation Menu-->
            <ul class="navigation-menu" style="    font-size: 14px;">
                <li>
                    <a href="{{ route('admin.home') }}">
                        <i class="zmdi zmdi-view-dashboard"></i>
                        <span> @lang('menu.home') </span>
                    </a>
                </li>

                @can('users_manage')
                    <li class="has-submenu">
                        <a href="#"><i
                                    class="zmdi zmdi-accounts"></i><span>@lang('maincp.customer_management')   </span>
                        </a>
                        <ul class="submenu">
                            <li><a href="{{ route('clients.index').'?type=active' }}">@lang('trans.active_customers')</a></li>
                            <li><a href="{{ route('clients.index').'?type=suspended' }}">@lang('trans.suspended_customers')</a></li>
                            <li><a href="{{ route('clients.index').'?type=outer' }}">@lang('trans.outer_customers')</a></li>


                        </ul>
                    </li>

                @endcan

                @can('users_manage')
                    <li class="has-submenu">
                        <a href="#"><i
                                    class="zmdi zmdi-layers"></i><span>  @lang('trans.managers_ctrl_panel') </span>
                        </a>
                        <ul class="submenu ">
                            <li><a href="{{ route('users.index') }}"> @lang('trans.managers_system')</a></li>
                            <li>
                                <a href="{{ route('roles.index') }}">
                                    @lang('trans.roles_and_permission')
                                </a>
                            </li>


                        </ul>
                    </li>
                @endcan




                @can('customer_service_manage')
                    <li class="has-submenu">
                        <a href="#"><i
                                    class="zmdi zmdi-accounts"></i><span>@lang('otrans.customer_service')   </span>
                        </a>
                        <ul class="submenu">
                            <li><a href="#">@lang('otrans.customer_service_users')</a></li>
                            <li><a href="#">@lang('otrans.view_chats')</a></li>

                        </ul>
                    </li>

                @endcan








            @can('app_general_settings_management')


                    <li class="has-submenu">
                        <a href="#">
                            <i class="zmdi zmdi-settings"></i>
                            <span>@lang('maincp.setting')
                                <i class="fa fa-arrow-down visible-xs" aria-hidden="true"></i>
                            </span>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="{{ route('settings.app.general') }}">@lang('trans.general_setting_app')</a>
                            </li>

                            <li>
                                <a href="{{ route('banks.index') }}">@lang('trans.banks_accounts')</a>
                            </li>

                            <li>
                                <a href="{{ route('cities.index') }}">@lang('trans.cities')</a>
                            </li>


                            <li>
                                <a href="{{ route('categories.index') }}">@lang('trans.categories')</a>
                            </li>

                            <li>
                                <a href="{{ route('products.index') }}">@lang('trans.types')</a>
                            </li>

                            <li>
                                <a href="{{ route('offers.index') }}">@lang('trans.offers')</a>
                            </li>

                            <li>
                                <a href="{{ route('calories.index') }}">@lang('trans.calories')</a>
                            </li>

                            <li>
                                <a href="{{ route('times.index') }}">@lang('trans.times')</a>
                            </li>

                        </ul>
                    </li>
                @endcan





                @can('subscriptions_management')

                    <li class="has-submenu">
                        <a href="#">
                            <i class="zmdi zmdi-settings"></i>
                            <span>@lang('trans.subscriptions')
                                <i class="fa fa-arrow-down visible-xs" aria-hidden="true"></i>
                            </span>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="{{ route('subscriptions.index') }}">@lang('trans.subscriptionsTypes')</a>
                            </li>

                            <li>
                                <a href="{{ route('periods.index') }}">@lang('trans.periods')</a>
                            </li>

                            <li>
                                <a href="{{ route('programs.index') }}">@lang('trans.programs')</a>
                            </li>
                        </ul>
                    </li>
                @endcan

                @can('content_management')
                    <li class="has-submenu">
                        <a href="javascript:;">
                            <i class="zmdi zmdi-square-o"></i>
                            <span>  @lang('maincp.content_management')  </span>
                        </a>
                        <ul class="submenu ">

                            <li>
                                <a href="{{ route('settings.terms') }}">@lang('trans.terms')</a>
                            </li>

                            <li>
                                <a href="{{ route('settings.aboutus') }}">@lang('trans.about_app')</a>
                            </li>

                            <li>
                                <a href="{{ route('branches.index') }}">@lang('trans.branches')</a>
                            </li>

                            @can('supports_management')
                            <li>
                                <a href="{{ route('support.index') }}">@lang('maincp.contact_us')</a>
                            </li>
                            @endcan


                            <li>
                                <a href="{{ route('faqs.index') }}">@lang('maincp.common_questions')</a>
                            </li>

                            {{--<li>--}}
                                {{--<a href="{{ route('public.notifications') }}">@lang('trans.public_notifications')</a>--}}
                            {{--</li>--}}
                        </ul>
                    </li>


                @endcan


                @can('orders_management')

                    <li class="has-submenu">
                        <a href="#"><i
                                    class="zmdi zmdi-border-all"></i><span>@lang('trans.orders_management')   </span>
                        </a>
                        <ul class="submenu ">
                            <li>
                                <a href="{{ route('orders.index') }}">
                                    @lang('otrans.products_and_offers_requests')
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.bankTransfer') }}">
                                    @lang('otrans.bank_transfers')
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('cancel_subscription.index') }}">
                                    @lang('otrans.cancel_subscriptions_requests')
                                </a>
                            </li>`
                        </ul>
                    </li>

                @endcan

                @can('reports_management')
                    <li class="has-submenu">
                        <a href="#"><i class="zmdi zmdi-flag"></i><span>@lang('maincp.reports')</span> </a>
                        <ul class="submenu">
                            <li>
                                <a href="{{ route('reports.bank.transfer.client') }}">@lang('trans.reports_bank_transfer_from_client') </a>
                            </li>
                            <li>
                                <a href="{{route('reports.orders')}}">@lang('otrans.orders_reports') </a>
                            </li>
                            <li>
                                <a href="{{route('reports.chefs')}}">@lang('otrans.chefs_reports') </a>
                            </li>
                            <li>
                                <a href="{{route('reports.delivery')}}">@lang('otrans.delivery_reports') </a>
                            </li>

                            <li>
                                <a href="{{ route('report.clients.subscriptions') }}">@lang('otrans.clients_subscriptions_reports') </a>
                            </li>

                            <li><a href="{{ route('report.clients.outer') }}">@lang('otrans.outer_clients_reports') </a></li>

                        </ul>
                    </li>
                @endcan




                <li>
                    <a href="{{ route('messages.index') }}">
                        <i class="zmdi zmdi-markunread-mailbox"></i>
                        <span> @lang('menu.conversations') </span>
                    </a>
                </li>
            </ul>
            <!-- End navigation menu  -->
        </div>
    </div>
</div>