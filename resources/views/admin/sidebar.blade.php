<div class="main-sidebar">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ route('admin.dashboard') }}"><img class="admin_logo" src="{{ asset($setting->logo) ?? '' }}"
                    alt="{{ $setting->app_name ?? '' }}"></a>
        </div>

        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ route('admin.dashboard') }}"><img src="{{ asset($setting->favicon) ?? '' }}"
                    alt="{{ $setting->app_name ?? '' }}"></a>
        </div>

        @php
            // Hide only: unused menus are hidden here, modules are not removed.
            $adminMenuVisibility = [
                'course' => false,
                'certificate_builder' => false,
                'badges' => false,
                'payment_withdraw' => false,
                'addons' => false,
            ];
        @endphp

        <ul class="sidebar-menu">
            @adminCan('dashboard.view')
                <li class="{{ isRoute('admin.dashboard', 'active') }}">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i>
                        <span>{{ __('Dashboard') }}</span>
                    </a>
                </li>
            @endadminCan

            <li class="{{ Route::is('admin.student-plans.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.student-plans.index') }}"><i class="fas fa-box"></i>
                    <span>{{ __('Paketler') }}</span>
                </a>
            </li>

            <li class="{{ Route::is('admin.trial-lesson-requests.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.trial-lesson-requests.index') }}"><i class="fas fa-user-clock"></i>
                    <span>{{ __('Deneme Dersi Talepleri') }}</span>
                </a>
            </li>

            <li class="{{ Route::is('admin.outreach-campaigns.*') || Route::is('admin.outreach-messages.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.outreach-campaigns.index') }}"><i class="fas fa-paper-plane"></i>
                    <span>{{ __('Outreach Bot') }}</span>
                </a>
            </li>

            @if (Module::isEnabled('ContactMessage'))
                <li class="{{ Route::is('admin.contact-messages') || Route::is('admin.contact-message') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.contact-messages') }}"><i class="fas fa-envelope"></i>
                        <span>{{ __('Contact Messages') }}</span>
                    </a>
                </li>
            @endif

            @if(checkAdminHasPermission('course.management') || checkAdminHasPermission('course.certificate.management') || checkAdminHasPermission('badge.management') || checkAdminHasPermission('blog.view'))
                <li class="menu-header">{{ __('Manage Contents') }}</li>

                @if ($adminMenuVisibility['course'] && Module::isEnabled('Course') && checkAdminHasPermission('course.management'))
                    @include('course::sidebar')
                @endif

                @if ($adminMenuVisibility['certificate_builder'] && Module::isEnabled('CertificateBuilder') && checkAdminHasPermission('course.certificate.management'))
                    @include('certificatebuilder::sidebar')
                @endif

                @if ($adminMenuVisibility['badges'] && Module::isEnabled('Badges') && checkAdminHasPermission('badge.management'))
                    @include('badges::sidebar')
                @endif

                @if (Module::isEnabled('Blog'))
                    @include('blog::sidebar')
                @endif
            @endif

            @if(checkAdminHasPermission('order.management') || checkAdminHasPermission('coupon.management') || checkAdminHasPermission('withdraw.management'))
                <li class="menu-header">{{ __('Manage Orders') }}</li>

                @if (Module::isEnabled('Order') && checkAdminHasPermission('order.management'))
                    @include('order::sidebar')
                @endif

                @if (Module::isEnabled('Coupon') && checkAdminHasPermission('coupon.management'))
                    @include('coupon::sidebar')
                @endif

                @if ($adminMenuVisibility['payment_withdraw'] && Module::isEnabled('PaymentWithdraw') && checkAdminHasPermission('withdraw.management'))
                    @include('paymentwithdraw::admin.sidebar')
                @endif
            @endif

            @if(checkAdminHasPermission('instructor.request.list') || checkAdminHasPermission('customer.view') || checkAdminHasPermission('location.view'))
                <li class="menu-header">{{ __('Manage Users') }}</li>
                @if (
                    (Module::isEnabled('InstructorRequest') && checkAdminHasPermission('instructor.request.list')) ||
                        checkAdminHasPermission('instructor.request.setting'))
                    @include('instructorrequest::sidebar')
                @endif

                @if (Module::isEnabled('Customer') && checkAdminHasPermission('customer.view'))
                    @include('customer::sidebar')
                @endif

                @if (Module::isEnabled('Location') && checkAdminHasPermission('location.view'))
                    @include('location::sidebar')
                @endif
            @endif

            @if(checkAdminHasPermission('appearance.management') || checkAdminHasPermission('section.management') || checkAdminHasPermission('footer.management') || checkAdminHasPermission('brand.managemen'))
                <li class="menu-header">{{ __('Site Contents') }}</li>
                @if (Module::isEnabled('SiteAppearance') && checkAdminHasPermission('appearance.management'))
                    @include('siteappearance::sidebar')
                @endif

                @if (Module::isEnabled('Frontend') && checkAdminHasPermission('section.management'))
                    @include('frontend::sidebar')
                @endif

                @if (Module::isEnabled('Brand') && checkAdminHasPermission('brand.management'))
                    @include('brand::sidebar')
                @endif

                @if (Module::isEnabled('FooterSetting') && checkAdminHasPermission('footer.management'))
                    @include('footersetting::sidebar')
                @endif
            @endif


            @if(checkAdminHasPermission('menu.view') || checkAdminHasPermission('page.management') || checkAdminHasPermission('social.link.management') || checkAdminHasPermission('faq.view'))
                <li class="menu-header">{{ __('Manage Website') }}</li>

                @if (Module::isEnabled('MenuBuilder') && checkAdminHasPermission('menu.view'))
                    @include('menubuilder::sidebar')
                @endif
                
                @if (Module::isEnabled('PageBuilder') && checkAdminHasPermission('page.management'))
                    @include('pagebuilder::sidebar')
                @endif

                @if (Module::isEnabled('SocialLink') && checkAdminHasPermission('social.link.management'))
                    @include('sociallink::sidebar')
                @endif

                @if (Module::isEnabled('Faq') && checkAdminHasPermission('faq.view'))
                    @include('faq::sidebar')
                @endif
            @endif

            @if(checkAdminHasPermission('setting.view') || checkAdminHasPermission('basic.payment.view') || checkAdminHasPermission('payment.view') || checkAdminHasPermission('currency.view') || checkAdminHasPermission('role.view') || checkAdminHasPermission('admin.view') || checkAdminHasPermission('addon.view'))
                <li class="menu-header">{{ __('Settings') }}</li>
                <li class="{{ isRoute('admin.settings', 'active') }}">
                    <a class="nav-link" href="{{ route('admin.settings') }}"><i class="fas fa-cog"></i>
                        <span>{{ __('Settings') }}</span>
                    </a>
                </li>
            @endif

            @if(checkAdminHasPermission('newsletter.view') || checkAdminHasPermission('testimonial.view'))
                <li class="menu-header">{{ __('Utility') }}</li>

                @if (Module::isEnabled('NewsLetter') && checkAdminHasPermission('newsletter.view'))
                    @include('newsletter::sidebar')
                @endif

                @if (Module::isEnabled('Testimonial') && checkAdminHasPermission('testimonial.view'))
                    @include('testimonial::sidebar')
                @endif

            @endif
            @if ($adminMenuVisibility['addons'])
                <li class="nav-item dropdown {{ isRoute('admin.addon.*') ? 'active' : '' }}" id="addon_sidemenu">
                    <a class="nav-link has-dropdown" data-toggle="dropdown" href="#"><i class="fas fa-gem"></i>
                        <span>{{ __('Manage Addons') }} </span>
                    </a>
                    <ul class="dropdown-menu addon_menu">
                        @includeIf('admin.addons')
                    </ul>
                </li>
            @endif
        </ul>
        <div class="py-3 text-center">
            <div class="btn-sm-group-vertical version_button" role="group" aria-label="Responsive button group">
                <button class="btn btn-primary logout_btn mt-2" disabled>{{ __('version') }}
                    {{ $setting->version ?? '1.0.0' }}</button>
                <button class="btn btn-danger mt-2"
                    onclick="event.preventDefault(); $('#admin-logout-form').trigger('submit');"><i
                        class="fas fa-sign-out-alt"></i></button>
            </div>
        </div>
    </aside>
</div>
