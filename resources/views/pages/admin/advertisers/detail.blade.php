@extends('app')

@section('title', $advertiser->name)

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
                <a class="btn blue-madison btn-block margin-bottom-10 btn-sm" href="{{url('advertisers')}}"> <i class="fa fa-arrow-left"></i> Back To Listing </a>
                <!-- PORTLET MAIN -->
                <div class="portlet light profile-sidebar-portlet">
                    <!-- SIDEBAR USER TITLE -->
                    <div class="profile-usertitle">
                        <div class="profile-usertitle-name">
                            {{$advertiser->name}}
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
                        <h4 class="profile-desc-title">About {{$advertiser->name}}</h4>
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
                <div class="row">
                    <div class="col-md-12">
                        <form class="form-horizontal" name="frm_advertiser_edit" id="frm_advertiser_edit" method="post" novalidate="novalidate">
                            {!! csrf_field() !!}
                            <div class="portlet light">
                                <div class="portlet-title tabbable-line">
                                    <div class="caption caption-md">
                                        <i class="icon-equalizer font-blue-madison"></i>
                                        <span class="caption-subject font-blue-madison bold uppercase">Advertiser Information</span>
                                    </div>
                                    <div class="actions">
                                        <a href="javascript:void(0);" id="anc_edit_customer" class="btn btn-default btn-circle display_value_section">
                                            <i class="fa fa-pencil"></i>
                                            <span class="hidden-480">Edit</span>
                                        </a>
                                        <a class="btn blue-madison btn-circle btn-sm" href="{{url('admin/advertisers')}}"> <i class="fa fa-arrow-left"></i> Back</a>
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
                                            <label for="name" class="col-md-2 control-label">Advertiser Name<span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <div class="display_control_section">
                                                    <input type="text" class="form-control" id="name" name="name" value="{{$advertiser->name}}" placeholder="">
                                                </div>
                                                <label class="display_value_section control-label">
                                                    {{$advertiser->name}}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="error_class_div">
                                            <label for="name" class="col-md-2 control-label">Widgets<span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <div class="display_control_section">
                                                    <input type="hidden" autocomplete="off" class="form-control" id="advertiser_widget_ids" value="" name="advertiser_widget_ids">
                                                </div>
                                                <label class="display_value_section control-label">
                                                    {{$advertiser_widgets_name_str}}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="error_class_div">
                                            <label for="name" class="col-md-2 control-label">Type<span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <div class="display_control_section">
                                                    <select class="form-control" id="advertiser_type_id" name="advertiser_type_id">
                                                        <option value="0">Select Type</option>
                                                        @if(count($advertiser_types))
                                                            @foreach($advertiser_types as $advertiser_type)
                                                                @if($advertiser_type->id==$advertiser->type_id)
                                                                    <option value="{{$advertiser_type->id}}" selected="selected" >{{$advertiser_type->type}}</option>
                                                                @else
                                                                    <option value="{{$advertiser_type->id}}">{{$advertiser_type->type}}</option>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <label class="display_value_section control-label">
                                                    @if($advertiser->advertiser_type!=null)
                                                        {{$advertiser->advertiser_type->type}}
                                                    @else
                                                        N/A
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    </div>

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
                        </form>
                    </div>
                </div>
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
        var url_get_advertiser_widgets='{{url('api/get_advertiser_widgets')}}';
        var advertiser_widgets_json={!!$advertiser_widgets_json!!};
    </script>
    <script src="{{ URL::asset('pages/scripts/admin/advertiser-detail.js')}}"></script>
@stop