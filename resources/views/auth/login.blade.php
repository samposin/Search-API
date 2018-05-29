@extends('default')

@section('title', 'Login')

@section('content')
    <!-- BEGIN LOGIN FORM -->
    <form id="login-form" class="login-form" action="{{ url('/login') }}" method="post">
        <h3 class="form-title">Sign In To Your Account</h3>
        {!! csrf_field() !!}

        <!-- Display Server side Error start -->

        @if (count($errors) > 0)
        <div class="alert alert-danger">
            <button class="close" data-close="alert"></button>
            <span>Invalid Username and/or Password</span>
        </div>
        @endif

        <!-- Display Server side Error end -->

        <div class="alert alert-danger msg-login display-hide">
            <button class="close" data-close="alert"></button>
            <span>
            Please Enter email and password. </span>
        </div>
        <div class="form-group">
            <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
            <label class="control-label visible-ie8 visible-ie9">Username</label>
            <input class="form-control form-control-solid placeholder-no-fix"  autocomplete="off"
                   type="email" name="email" id="email" value="{{ old('email') }}" placeholder="Enter email"/>
        </div>
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9">Password</label>
            <input class="form-control form-control-solid placeholder-no-fix" autocomplete="off"
                   type="password" name="password" id="password" placeholder="Password"/>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-success uppercase">Login</button>
            <label class="rememberme check">
                <input type="checkbox" name="remember" value="1"/>Remember
            </label>
            <!--<a href="javascript:;" id="forget-password" class="forget-password">Forgot Password?</a>-->
        </div>
        <!--<div class="login-options">
            <h4>Or login with</h4>
            <ul class="social-icons">
                <li>
                    <a class="social-icon-color facebook" data-original-title="facebook" href="javascript:;"></a>
                </li>
                <li>
                    <a class="social-icon-color twitter" data-original-title="Twitter" href="javascript:;"></a>
                </li>
                <li>
                    <a class="social-icon-color googleplus" data-original-title="Goole Plus" href="javascript:;"></a>
                </li>
                <li>
                    <a class="social-icon-color linkedin" data-original-title="Linkedin" href="javascript:;"></a>
                </li>
            </ul>
        </div>-->
        <div class="create-account">
            <p>&nbsp;
                <!--<a href="javascript:;" id="register-btn" class="uppercase">Create an account</a>-->
            </p>
        </div>
    </form>


@endsection

@section('page_level_plugins')
    <script type="text/javascript" src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js')}}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/global/plugins/jquery-validation/js/additional-methods.min.js')}}"></script>
@endsection

@section('page_level_scripts')

@endsection

@section('footer')
    <script language="JavaScript" type="text/javascript">
        jQuery(document).ready(function () {
            Login.init();
        });
    </script>
    <script src="{{ URL::asset('pages/scripts/login.js')}}" type="text/javascript"></script>
@endsection
