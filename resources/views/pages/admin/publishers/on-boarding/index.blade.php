@extends('app')

@section('title', 'Publishers')

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
    <style type="text/css">

    </style>
@stop

@section('content')

    <div class="row">
        <div class="col-md-12">
            <!-- Begin: life time stats -->
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-user font-blue-sharp"></i>
                        <span class="caption-subject font-blue-sharp bold uppercase">Publishers Onboarding</span>
                        <span class="caption-helper">view & filter...</span>
                    </div>
                    <div class="actions">
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
                        <div class="table-container" id="on_boarding_datatable_container">
                            <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
                                <thead>
                                <tr role="row" class="heading">
                                    <th width="">
                                        Publisher Name
                                    </th>
                                    <th width="11%">
                                        Created Publisher <br>from <br>configurator
                                    </th>

                                    <th width="11%">
                                        Admin section - Add Publisher Name and email ID
                                    </th>
                                     <th width="11%">
                                       Email sent <br>to<br> Publisher with JS
                                    </th>
                                      <th width="11%">
                                        Cross check analytics profile for this publisher
                                    </th>
                                    <th width="11%">
                                        Test <br>Generated JS <br>working or not
                                    </th>
                                    <th width="11%">
                                        Test Generated JS entering data on Analytics or not
                                    </th>
                                    <th width="15%">
                                        Created
                                    </th>
                                     <th class="no-sort" width="5%">&nbsp;
                                    </th>
                                </tr>
                                <tr role="row" class="filter">
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="publisher_name">
                                        <input type="hidden" class="form-control hdn-form-filter input-sm" name="hdn_publisher_name">
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <div class="input-group date date-picker margin-bottom-5" data-date-format="dd/mm/yyyy">
                                            <input type="text" class="form-control form-filter input-sm" readonly name="publisher_created_from" placeholder="From">
                                            <input type="hidden" class="form-control hdn-form-filter input-sm" name="hdn_publisher_created_from">
                                            <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                        </div>
                                        <div class="input-group date date-picker" data-date-format="dd/mm/yyyy">
                                            <input type="text" class="form-control form-filter input-sm" readonly name="publisher_created_to" placeholder="To">
                                            <input type="hidden" class="form-control hdn-form-filter input-sm" name="hdn_publisher_created_to">
                                            <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
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
        var data_table_list_ajax_url='{{url('admin/')}}/publishers-on-boarding-list-ajax';
        var publisher_on_boarding_submit_url='{{url('admin/')}}/publishers/on-boarding';
    </script>
    <script src="{{ URL::asset('pages/scripts/admin/publishers-on-boarding-index.js')}}"></script>
@stop