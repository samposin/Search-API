@extends('app')

@section('title', 'Advertisers - Search defaults')

@section('page_level_styles')
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

    <div class="row">
        <div class="col-md-12">
            <!-- Begin: life time stats -->
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-user font-blue-sharp"></i>
                        <span class="caption-subject font-blue-sharp bold uppercase">Advertisers - Search defaults</span>
                        <span class="caption-helper">view & filter...</span>
                    </div>
                    <div class="actions">
                        <!--<a href="javascript:void(0);" id="anc_create_advertiser" class="btn btn-default btn-circle">
                            <i class="fa fa-plus"></i>
							<span class="hidden-480">
								New Advertiser
                            </span>
                        </a>-->
                        <!--<div class="btn-group">
                            <a class="btn btn-default btn-circle" href="javascript:;" data-toggle="dropdown">
                                <i class="fa fa-share"></i>
									<span class="hidden-480">
									Tools </span>
                                <i class="fa fa-angle-down"></i>
                            </a>
                            <ul id="companies_export_dropdown" class="dropdown-menu pull-right">
                                <li>
                                    <a href="javascript:;" data-export-type="csv">
                                        Export to CSV </a>
                                </li>
                            </ul>
                        </div>-->
                    </div>
                </div>
                <div class="portlet-body">
                    {{-- qw
                      --}}
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
                    <form name="frm_datatable" id="frm_datatable" method="post" >
                        {!! csrf_field() !!}
                        <input type="hidden" name="action" id="action" value="">
                        <div class="table-container">
                            <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
                                <thead>
                                <tr role="row" class="heading">
                                    <th width="10%">
                                        GEO
                                    </th>
                                    <th width="30%">
                                        Main
                                    </th>
                                    <th width="30%">
                                        1st Backfill
                                    </th>
                                    <th >
                                        2nd Backfill
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                     @foreach($country_available_api_arr as $k=>$v)
                                     <tr>
                                         <td>
                                             {{$k}}
                                         </td>
                                         <td>
                                             <select data-geo="{{$k}}" data-api-priority="main"  name="main_api[]" class="form-control">
                                                <option value="">Select...</option>
                                                @if(count($v))
                                                    @foreach($v as $api)
                                                        @if(isset($advertiser_search_defaults_arr[$k]['main']) && $advertiser_search_defaults_arr[$k]['main']==$api['api_name'])
                                                        <option value="{{$api['api_name']}}" selected="selected">{{$api['api_display_name']}}</option>
                                                        @else
                                                        <option value="{{$api['api_name']}}">{{$api['api_display_name']}}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </select>
                                         </td>
                                         <td>
                                             <select data-geo="{{$k}}" data-api-priority="first"   name="first_backfill_api[]"  class="form-control">
                                                <option value="">Select...</option>
                                                @if(count($v))
                                                    @foreach($v as $api)
                                                        @if(isset($advertiser_search_defaults_arr[$k]['first']) && $advertiser_search_defaults_arr[$k]['first']==$api['api_name'])
                                                        <option value="{{$api['api_name']}}" selected="selected">{{$api['api_display_name']}}</option>
                                                        @else
                                                        <option value="{{$api['api_name']}}">{{$api['api_display_name']}}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </select>
                                         </td>
                                         <td>
                                             <select  data-geo="{{$k}}" data-api-priority="second"  name="second_backfill_api[]" class="form-control">
                                                <option value="">Select...</option>
                                                @if(count($v))
                                                    @foreach($v as $api)
                                                        @if(isset($advertiser_search_defaults_arr[$k]['second']) && $advertiser_search_defaults_arr[$k]['second']==$api['api_name'])
                                                        <option value="{{$api['api_name']}}" selected="selected">{{$api['api_display_name']}}</option>
                                                        @else
                                                        <option value="{{$api['api_name']}}">{{$api['api_display_name']}}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </select>
                                         </td>
                                     </tr>
                                     @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
            <!-- End: life time stats -->
        </div>
    </div>
@stop

@section('page_level_plugins')
    <script type="text/javascript" src="{{ URL::asset('assets/global/plugins/select2/select2.min.js')}}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/global/plugins/datatables/media/js/jquery.dataTables.min.js')}}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js')}}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js')}}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/global/plugins/jquery-validation/js/additional-methods.min.js')}}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/global/plugins/bootbox/bootbox.min.js')}}"></script>
@stop

@section('page_level_scripts')
    <script src="{{ URL::asset('assets/global/scripts/metronic.js')}}" type="text/javascript"></script>
    <script src="{{ URL::asset('js/layout.js')}}"  type="text/javascript"></script>
    <script src="{{ URL::asset('assets/global/scripts/datatable.js')}}"></script>
@stop

@section('footer')
    <script language="JavaScript" type="text/javascript">
        var csrf_token ='{{ csrf_token() }}';
        var url_get_states='{{url('api/get_states')}}';
        var url_get_advertiser_widgets='{{url('api/get_advertiser_widgets')}}';
        var advertiser_search_defaults_submit_url='{{url('admin/')}}/advertisers/search-defaults'
    </script>
    <script src="{{ URL::asset('pages/scripts/admin/advertisers-search-defaults-index.js')}}"></script>
@stop