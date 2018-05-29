<!-- This is Master page for admin section -->
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    @include('includes.header')
    <!-- BEGIN PAGE LEVEL STYLES -->
    @yield('page_level_styles')
    <!-- END PAGE LEVEL STYLES -->
    <!-- BEGIN THEME STYLES -->
    @yield('theme_level_styles')
    <!-- END THEME STYLES -->
    <script>
        var URL = {
            'base'    : '{{ URL::to('/') }}',
            'current' : '{{ URL::current() }}',
            'full'    : '{{ URL::full() }}'
        };
        var rows_per_page='{{ Config::get('custom.rows_per_page')}}';
    </script>

</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<!-- DOC: Apply "page-header-fixed-mobile" and "page-footer-fixed-mobile" class to body element to force fixed header or footer in mobile devices -->
<!-- DOC: Apply "page-sidebar-closed" class to the body and "page-sidebar-menu-closed" class to the sidebar menu element to hide the sidebar by default -->
<!-- DOC: Apply "page-sidebar-hide" class to the body to make the sidebar completely hidden on toggle -->
<!-- DOC: Apply "page-sidebar-closed-hide-logo" class to the body element to make the logo hidden on sidebar toggle -->
<!-- DOC: Apply "page-sidebar-hide" class to body element to completely hide the sidebar on sidebar toggle -->
<!-- DOC: Apply "page-sidebar-fixed" class to have fixed sidebar -->
<!-- DOC: Apply "page-footer-fixed" class to the body element to have fixed footer -->
<!-- DOC: Apply "page-sidebar-reversed" class to put the sidebar on the right side -->
<!-- DOC: Apply "page-full-width" class to the body element to have full width page without the sidebar menu -->
<body class="page-header-fixed page-sidebar-closed-hide-logo ">
<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top">
    <!-- BEGIN HEADER INNER -->
    <div class="page-header-inner">
        <!-- BEGIN LOGO -->
        <div class="page-logo">
            <a href="/">
                <!--<img style="width: 180px;margin-left:0px;margin-top:9px;margin-right:0px;" src="{{ URL::asset('img/vision-api-logo-51-300x93.png')}}" alt="Vision API" class="logo-default"/>-->
                <img style="margin-left:0px;margin-top:0px;margin-right:0px;" src="{{ URL::asset('img/logo-72x72.png')}}" alt="Baseify" class="logo-default"/>
                <!--<div class="text-primary" style="font-weight: bold;font-size:2em;margin-top: 15px;">Baseify</div>-->
            </a>

            <div class="menu-toggler sidebar-toggler">
                <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
            </div>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
        </a>
        <!-- END RESPONSIVE MENU TOGGLER -->

        <!-- BEGIN PAGE TOP -->
        <div class="page-top">
            <!-- BEGIN TOP NAVIGATION MENU -->
            <div class="top-menu">
                <ul class="nav navbar-nav pull-right">
                    <li class="separator hide">
                    </li>
                    <!-- BEGIN USER LOGIN DROPDOWN -->
                    <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                    <li class="dropdown dropdown-user dropdown-dark">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
						<span class="username username-hide-on-mobile">
						Admin </span>
                            <!-- DOC: Do not remove below empty space(&nbsp;) as its purposely used -->
                            <img alt="" class="img-circle" src="{{ URL::asset('img/avatar.png')}}"/>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-default">
                            <li>
                                <a href="/logout">
                                    <i class="icon-key"></i> Log Out </a>
                            </li>
                        </ul>
                    </li>
                    <!-- END USER LOGIN DROPDOWN -->
                </ul>
            </div>
            <!-- END TOP NAVIGATION MENU -->
        </div>
        <!-- END PAGE TOP -->
    </div>
    <!-- END HEADER INNER -->
</div>
<!-- END HEADER -->
<div class="clearfix">
</div>
<!-- BEGIN CONTAINER -->
<div class="page-container">
    <!-- BEGIN SIDEBAR -->
    <div class="page-sidebar-wrapper">
        <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
        <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
        <div class="page-sidebar navbar-collapse collapse">
            <!-- BEGIN SIDEBAR MENU -->
            <!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
            <!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
            <!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
            <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
            <!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
            <!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
            <ul class="page-sidebar-menu " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
                <li class="start {{ Request::is('admin/dashboard*') ? 'active' : '' }}">
                    <a href="{{url('admin/dashboard')}}">
                        <i class="fa fa-home"></i>
                        <span class="title">Dashboard</span>
                    </a>
                </li>
                <li class=" {{ Request::is('admin/advertisers*') ? 'active open' : '' }}">
                    <a href="{{url('admin/advertisers')}}">
                        <i class="fa fa-user"></i>
                        <span class="title">Advertisers</span>
                        <span class="arrow {{ Request::is('admin/advertisers*') ? 'active open' : ''}}"></span>
                    </a>
                    <ul class="sub-menu">
                        <li {{  Request::is('admin/advertisers*') ? !Request::is('admin/advertisers/search-defaults*') ? 'class=active' : '':'' }}>
                            <a href="{{url('admin/advertisers')}}"><i class="fa fa-list"></i> List</a>
                        </li>
                        <li {{ Request::is('admin/advertisers/search-defaults*') ? 'class=active' : '' }}>
                            <a href="{{url('admin/advertisers/search-defaults')}}"><i class="fa fa-cog"></i> Search Defaults</a>
                        </li>
                    </ul>
                </li>
                <li class=" {{ Request::is('admin/publishers*') ? 'active open' : '' }}">
                    <a href="{{url('admin/publishers')}}">
                        <i class="fa fa-user"></i>
                        <span class="title">Publishers</span>
                        <span class="arrow {{ Request::is('admin/publishers*') ? 'active open' : ''}}"></span>
                    </a>
                    <ul class="sub-menu">
                        <li {{  Request::is('admin/publishers*') ? !Request::is('admin/publishers/on-boarding*') ? 'class=active' : '':'' }}>
                            <a href="{{url('admin/publishers')}}"><i class="fa fa-list"></i> List</a>
                        </li>
                        <li {{ Request::is('admin/publishers/on-boarding*') ? 'class=active' : '' }}>
                            <a href="{{url('admin/publishers/on-boarding')}}"><i class="fa fa-cog"></i> Onboarding</a>
                        </li>
                    </ul>
                </li>
                <li class=" {{ Request::is('admin/search-feeds*') ? 'active' : '' }}">
                    <a href="{{url('admin/search-feeds')}}">
                        <i class="fa fa-user"></i>
                        <span class="title">Search feeds</span>
                    </a>
                </li>
                <!--<li class="{{-- Request::is('admin/notes*') ? 'active' : '' --}}">
                    <a href="{{--url('admin/notes')--}}">
                        <i class="fa fa-pencil-square"></i>
                        <span class="title">Notes</span>
                    </a>
                </li>-->

                <li class="">
                    <a target="_blank" href="{{URL::to('/')}}/configurator/">
                        <i class="fa fa-cog"></i>
                        <span class="title">Configurator</span>
                    </a>
                </li>
                <li class="last">
                    <a target="_blank" href="http://analytics.baseify.com/index.php">
                        <i class="fa fa-cog"></i>
                        <span class="title">Piwik Admin</span>
                    </a>
                </li>
            </ul>
            <!-- END SIDEBAR MENU -->
        </div>
    </div>
    <!-- END SIDEBAR -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE CONTENT-->
            @yield('content')
            <!-- END PAGE CONTENT-->
        </div>

    </div>
    <!-- END CONTENT -->
</div>
<!-- END CONTAINER -->
<!-- BEGIN FOOTER -->
<div class="page-footer">
    <div class="page-footer-inner">
        2005-2015 © Baseify
    </div>
    <div class="scroll-to-top">
        <i class="icon-arrow-up"></i>
    </div>
</div>
<!-- END FOOTER -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
@include('includes.footer')
<!-- BEGIN PAGE LEVEL PLUGINS -->
@yield('page_level_plugins')
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
@yield('page_level_scripts')
<!-- END PAGE LEVEL SCRIPTS -->
<script>
    jQuery(document).ready(function() {
        Metronic.init(); // init metronic core components
        Layout.init(); // init current layout
    });
</script>
@yield('footer')
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>