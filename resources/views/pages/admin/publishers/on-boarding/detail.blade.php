@extends('app')

@section('title', $publisher->name)

@section('page_level_styles')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('pages/css/profile.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/global/plugins/select2/select2.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css')}}"/>
@stop

@section('theme_level_styles')
    <link href="{{ URL::asset('assets/global/css/components-rounded.css')}}" id="style_components" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('assets/global/css/plugins.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('css/layout.css')}}" rel="stylesheet" type="text/css"/>
    <link id="style_color" href="{{ URL::asset('css/themes/light.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('css/custom.css')}}" rel="stylesheet" type="text/css"/>
@stop

@section('content')
    <!-- BEGIN PAGE CONTENT-->
    <div class="row">
        <div class="col-md-12">
            {{--
            <!-- BEGIN PROFILE SIDEBAR -->
            <div class="profile-sidebar" style="width:250px;">
                <a class="btn blue-madison btn-block margin-bottom-10 btn-sm" href="{{url('publishers')}}"> <i class="fa fa-arrow-left"></i> Back To Listing </a>
                <!-- PORTLET MAIN -->
                <div class="portlet light profile-sidebar-portlet">
                    <!-- SIDEBAR USER TITLE -->
                    <div class="profile-usertitle">
                        <div class="profile-usertitle-name">
                            {{$publisher->name}}
                        </div>
                        <div class="profile-usertitle-job">

                        </div>
                    </div>
                    <!-- END SIDEBAR USER TITLE -->
                    <!-- SIDEBAR BUTTONS -->
                    <div class="profile-userbuttons">
                        <button type="button" class="btn  blue-madison btn-sm"><i class="fa fa-edit"></i> New Note</button>
                        <button type="button" class="btn  blue-madison btn-sm"><i class="fa fa-bolt"></i> Log Activity</button>
                    </div>
                    <!-- END SIDEBAR BUTTONS -->
                    <!-- SIDEBAR MENU -->
                    <div class="profile-usermenu">
                        <ul class="nav">
                            <li class="active">
                                <a href="javascript:void(0);" >
                                    <i class="icon-home"></i>
                                    Overview </a>
                            </li>

                        </ul>
                    </div>
                    <!-- END MENU -->
                </div>
                <!-- END PORTLET MAIN -->
                <!-- PORTLET MAIN -->
                <div class="portlet light">
                    <!-- STAT -->
                    <div class="row list-separated profile-stat">
                        <div class="col-md-4 col-sm-4 col-xs-6">
                            <div class="uppercase profile-stat-title">
                                37
                            </div>
                            <div class="uppercase profile-stat-text">
                                Projects
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-6">
                            <div class="uppercase profile-stat-title">
                                51
                            </div>
                            <div class="uppercase profile-stat-text">
                                Tasks
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-6">
                            <div class="uppercase profile-stat-title">
                                61
                            </div>
                            <div class="uppercase profile-stat-text">
                                Uploads
                            </div>
                        </div>
                    </div>
                    <!-- END STAT -->
                    <div>
                        <h4 class="profile-desc-title">About {{$publisher->name}}</h4>
                        <span class="profile-desc-text"> Lorem ipsum dolor sit amet diam nonummy nibh dolore. </span>
                        <div class="margin-top-20 profile-desc-link">
                            <i class="fa fa-globe"></i>
                            <a href="http://test">test</a>
                        </div>
                        <div class="margin-top-20 profile-desc-link">
                            <i class="fa fa-envelope"></i>
                            <a href="mailto:test">test</a>
                        </div>
                    </div>
                </div>
                <!-- END PORTLET MAIN -->
            </div>
            --}}
            <!-- END BEGIN PROFILE SIDEBAR -->
            <!-- BEGIN PROFILE CONTENT -->
            <div class="profile-content">
                <form class="form-horizontal" name="frm_publisher_edit" id="frm_publisher_edit" method="post" novalidate="novalidate">
                    <div class="row">
                        <div class="col-md-12">
                            {!! csrf_field() !!}
                            <div class="portlet light">
                                <div class="portlet-title tabbable-line">
                                    <div class="caption caption-md">
                                        <i class="icon-equalizer font-blue-madison"></i>
                                        <span class="caption-subject font-blue-madison bold uppercase">Publisher Information</span>
                                    </div>
                                    <div class="actions">
                                        <a href="javascript:void(0);" id="anc_edit_publisher" class="btn btn-default btn-circle display_value_section">
                                            <i class="fa fa-pencil"></i>
                                            <span class="hidden-480">Edit</span>
                                        </a>
                                        <a class="btn blue-madison btn-circle btn-sm" href="{{url('admin/publishers')}}"> <i class="fa fa-arrow-left"></i> Back</a>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    {{-- print_r($errors) --}}
                                    @include('flash::message')
                                    @include('partials.flash')
                                    @if (count($errors) > 0)
                                        <div class="alert alert-danger">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true" >&times;</button>
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <div class="form-group">
                                        <div class="error_class_div">
                                            <label for="name" class="col-md-2 control-label">Publisher Name<span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <div class="display_control_section">
                                                    <input type="text" class="form-control" id="name" name="name" value="{{$publisher->name}}" placeholder="">
                                                </div>
                                                <label class="display_value_section control-label">
                                                    {{$publisher->name}}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="error_class_div">
                                            <label for="share" class="col-md-2 control-label">Publisher Email</label>
                                            <div class="col-md-4">
                                                <div class="display_control_section">
                                                    <div class="">
                                                        <input id="email" type="text" value="{{$publisher->email}}" name="email" class="form-control">
                                                    </div>
                                                    <span class="help-block">enter email id to send reports </span>
                                                </div>
                                                <label class="display_value_section control-label">
                                                     @if($publisher->email!=null)
                                                        {{$publisher->email}}
                                                    @else
                                                        N/A
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                           </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="portlet light">
                                <div class="portlet-title tabbable-line">
                                    <div class="caption caption-md">
                                        <i class="icon-equalizer font-blue-madison"></i>
                                        <span class="caption-subject font-blue-madison bold uppercase">Advertisers Information</span>
                                    </div>
                                    <div class="actions">

                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                        <tr role="row" class="heading">
                                            <th width="20%">
                                                <div class="col-xs-12">Advertiser</div>
                                            </th>
                                            <th  width="15%">
                                                <div class="col-xs-2">ID</div>
                                            </th>
                                            <th width="">
                                                <div class="col-xs-2">Share</div>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if(count($advertisers))
                                            @foreach($advertisers as $advertiser)
                                                <tr>
                                                    <td>
                                                        <div class="col-xs-12">{{$advertiser->advertisername}}</div>
                                                    </td>
                                                    <td>
                                                        <div class="col-xs-8">
                                                            <div class="display_control_section">
                                                                <input type="text" class="form-control" name="publisher_id1s[]"  value="{{$advertiser->publisher_id1}}">
                                                                <input type="hidden" class="form-control" name="advertizer_ids[]"  value="{{$advertiser->advertiserid}}">
                                                            </div>
                                                            <label class="display_value_section control-label">
                                                                {{$advertiser->publisher_id1}}&nbsp;
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="col-xs-6">
                                                            <div class="display_control_section">
                                                                <div class="input-inline input-medium">
                                                                    <input id="share" class="cls_publisher_share" type="text" value="{{$advertiser->share}}"  name="shares[]" class="form-control">
                                                                </div>
                                                            </div>
                                                            <label class="display_value_section control-label">
                                                                {{$advertiser->display_share}}&nbsp;
                                                            </label>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="form-actions display_control_section">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="pull-right">
                                            <button type="submit" class="btn green"><i class="fa fa-check"></i> Update</button>
                                            <button type="button" class="btn default" id="btnCancel"> Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- END PROFILE CONTENT -->
        </div>
    </div>
    <!-- END PAGE CONTENT-->

@stop

@section('page_level_plugins')
    <script type="text/javascript" src="{{ URL::asset('assets/global/plugins/select2/select2.min.js')}}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/global/plugins/datatables/media/js/jquery.dataTables.min.js')}}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js')}}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js')}}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/global/plugins/jquery-validation/js/additional-methods.min.js')}}"></script>
@stop

@section('page_level_scripts')
    <script src="{{ URL::asset('assets/global/scripts/metronic.js')}}" type="text/javascript"></script>
    <script src="{{ URL::asset('js/layout.js')}}"  type="text/javascript"></script>
@stop

@section('footer')
    <script language="JavaScript" type="text/javascript">
        var url_get_states='{{url('api/get_states')}}';
        var url_get_advertisers='{{url('api/get_advertisers')}}';

    </script>
    <script src="{{ URL::asset('pages/scripts/admin/publisher-detail.js')}}"></script>
@stop