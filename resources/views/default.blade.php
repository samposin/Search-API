<!-- This is index for login, registration, forgot password -->
<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
    <head>
        @include('includes.header')
        <!-- BEGIN PAGE LEVEL STYLES -->
        <link href="{{ URL::asset('pages/css/login.css')}}" rel="stylesheet" type="text/css"/>
        <!-- END PAGE LEVEL STYLES -->
        <!-- BEGIN THEME STYLES -->
        <link href="{{ URL::asset('assets/global/css/components-rounded.css')}}" id="style_components" rel="stylesheet" type="text/css"/>
        <link href="{{ URL::asset('assets/global/css/plugins.css')}}" rel="stylesheet" type="text/css"/>
        <link href="{{ URL::asset('css/default-layout.css')}}" rel="stylesheet" type="text/css"/>
        <link href="{{ URL::asset('css/default-themes/default.css')}}" rel="stylesheet" type="text/css" id="style_color"/>
        <link href="{{ URL::asset('css/default-custom.css')}}" rel="stylesheet" type="text/css"/>
        <!-- END THEME STYLES -->

        @yield('header')
    </head>
    <!-- END HEAD -->
    <!-- BEGIN BODY -->
    <body class="login">
        <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
        <div class="menu-toggler sidebar-toggler">
        </div>
        <!-- END SIDEBAR TOGGLER BUTTON -->

        <!-- BEGIN LOGIN -->
        <div class="content">
            <!-- BEGIN LOGO -->
            <div class="logo">
                <!--<a href="/">
                    <img style="width: 250px;" src="{{ URL::asset('img/vision-api-logo-51-300x93.png')}}" alt="Vision API" />
                </a>-->
                 <a href="/">
                    <img style="width: 144px;" src="{{ URL::asset('img/logo-144x144.png')}}" alt="Baseify" />
                </a>
                <!--<div class="text-primary" style="font-weight: bold;font-size:3em;margin-bottom: -20px;">Baseify</div>-->
            </div>
            <!-- END LOGO -->
            @yield('content')
        </div>
        <div class="copyright">
            2005-2015 © Baseify
        </div>
        <!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
        @include('includes.footer')

        <!-- BEGIN PAGE LEVEL PLUGINS -->
        @yield('page_level_plugins')
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
        <script src="{{ URL::asset('assets/global/scripts/metronic.js')}}" type="text/javascript"></script>
        @yield('page_level_scripts')
        <!-- END PAGE LEVEL SCRIPTS -->
        <script>
            jQuery(document).ready(function () {
                Metronic.init(); // init metronic core components
            });
        </script>
        @yield('footer')
        <!-- END JAVASCRIPTS -->

    </body>
    <!-- END BODY -->
</html>