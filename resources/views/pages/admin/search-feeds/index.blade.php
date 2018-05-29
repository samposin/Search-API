@extends('app')

@section('title', 'Search Feeds')

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
                        <span class="caption-subject font-blue-sharp bold uppercase">Search Feeds</span>
                        <span class="caption-helper">view & filter...</span>
                    </div>
                    <div class="actions">
                        <a href="javascript:void(0);" id="anc_create_search_feed" class="btn btn-default btn-circle">
                            <i class="fa fa-plus"></i>
							<span class="hidden-480">
								New Search Feed
                            </span>
                        </a>
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

                    <form name="frm_datatable" id="frm_datatable" method="post">
                        {!! csrf_field() !!}
                        <input type="hidden" name="action" id="action" value="">
                        <div class="table-container">
                            <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
                                <thead>
                                <tr role="row" class="heading">
                                    <!--<th class="no-sort" width="2%">
                                        <input type="checkbox" class="group-checkable">
                                    </th>-->
                                    <th width="5%">
                                        Search Feed&nbsp;#
                                    </th>
                                    <th width="25%">
                                        Client Name
                                    </th>
                                    <th >
                                        Url
                                    </th>
                                    <th width="10%" >
                                        Active
                                    </th>
                                    <th width="12%">
                                        Created
                                    </th>

                                    <th class="no-sort" width="10%">
                                        Actions
                                    </th>
                                </tr>
                                <tr role="row" class="filter">
                                    <!--<td>
                                    </td>-->
                                    <td >
                                        <input type="text" class="form-control form-filter input-sm" name="search_feed_id">
                                        <input type="hidden" class="form-control hdn-form-filter input-sm" name="hdn_search_feed_id">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="search_feed_client_name">
                                        <input type="hidden" class="form-control hdn-form-filter input-sm" name="hdn_search_feed_client_name">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="search_feed_url">
                                        <input type="hidden" class="form-control hdn-form-filter input-sm" name="hdn_search_feed_url">
                                    </td>
                                    <td>
                                        <select name="search_feed_is_active" class="form-control form-filter input-sm">
                                            <option value="">Select...</option>
                                            <option value="yes">Active</option>
                                            <option value="no">Inactive</option>
                                        </select>
                                        <input type="hidden" class="form-control hdn-form-filter input-sm" name="hdn_search_feed_is_active">
                                    </td>
                                    <td>
                                        <div class="input-group date date-picker margin-bottom-5" data-date-format="dd/mm/yyyy">
                                            <input type="text" class="form-control form-filter input-sm" readonly name="search_feed_created_from" placeholder="From">
                                            <input type="hidden" class="form-control hdn-form-filter input-sm" name="hdn_search_feed_created_from">
                                            <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                        </div>
                                        <div class="input-group date date-picker" data-date-format="dd/mm/yyyy">
                                            <input type="text" class="form-control form-filter input-sm" readonly name="search_feed_created_to" placeholder="To">
                                            <input type="hidden" class="form-control hdn-form-filter input-sm" name="hdn_search_feed_created_to">
                                            <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="margin-bottom-5">
                                            <button class="btn btn-sm yellow filter-submit margin-bottom"><i class="fa fa-search"></i> Search</button>
                                        </div>
                                        <button class="btn btn-sm red filter-cancel"><i class="fa fa-times"></i> Reset</button>
                                    </td>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
            <!-- End: life time stats -->
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <!-- Begin: life time stats -->
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-pencil-square font-blue-sharp"></i>
                        <span class="caption-subject font-blue-sharp bold uppercase">Note</span>
                        <span class="caption-helper">:</span>
                    </div>
                    <div class="actions">
                    </div>
                </div>
                <div class="portlet-body">
                    <p>
                        This page is used to create search feeds from other advertisers.
                    </p>
                    <p>
                       <a target="_blank" href="{{URL::to('/')}}/search-feeds/schema/input">Search Feed Input Schema</a> <i>(This format will be used to pass parameters to provided search url)</i>
                    </p>
                     <p>
                        <a target="_blank" href="{{URL::to('/')}}/search-feeds/schema/output">Search Feed Output Schema</a> <i>(Output json we expects from advertiser search feed)</i>
                    </p>

                </div>
            </div>
            <!-- End: life time stats -->
        </div>
    </div>

    <!-- Modal Html for Create Company start -->

    <div id="modal_create_search_feed" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <form name="frm_search_feed_add" id="frm_search_feed_add" action="" method="post" data-remote="data-remote" class="form-horizontal">
                {!! csrf_field() !!}
                <div class="modal-content">
                    <div class="modal-header ">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Create Search Feed</h4>
                    </div>
                    <div class="modal-body" style="padding-bottom: 0px;">

                        <div class="form-group">
                            <label class="col-xs-3 control-label" for="name">Client Name<span class="required">*</span></label>
                            <div class="col-xs-9">
                                <input type="text" placeholder="" id="client_name" name="client_name" required="" value="" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-3 control-label" for="url">URL<span class="required">*</span></label>
                            <div class="col-xs-9">
                                <input type="text" placeholder=""   class="form-control" id="url" value="" name="url">
                            </div>
                        </div>
                       <div class="form-group">
                            <label class="col-xs-3 control-label" for="active">Active</label>
                            <div class="radio-list col-xs-9">
                                <label class="radio-inline">
                                <input type="radio" name="is_active" id="rd_active" value="1" checked> Yes</label>
                                <label class="radio-inline">
                                <input type="radio" name="is_active" id="rd_inactive" value="0"> No </label>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit"  class="btn btn-primary">Create</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Html for Create Company end -->
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
    </script>
    <script src="{{ URL::asset('pages/scripts/admin/search-feeds-index.js')}}"></script>
@stop