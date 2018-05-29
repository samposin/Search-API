@extends('app')

@section('title', 'Notes')

@section('page_level_styles')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css')}}"/>
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
        <div class="col-md-6">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-link font-blue-sharp"></i>
                        <span class="caption-subject font-blue-sharp bold uppercase">Vision Api Input Url</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-container">
                        <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
                            <thead>
                            <tr role="row" class="heading">
                                <th width="15%">
                                    Advertiser
                                </th>
                                <th >
                                    Link
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        Dealspricer
                                    </td>
                                    <td >
                                        <a target="_blank" href="https://www.dropbox.com/sh/n1idgxm22aj59xt/AACYksVPFU_HjMRqzeBGjtn9a?dl=0">https://www.dropbox.com/sh/n1idgxm22aj59xt/AACYksVPFU_HjMRqzeBGjtn9a?dl=0</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        FoxyDeal
                                    </td>
                                    <td >
                                        <a target="_blank" href="https://www.dropbox.com/sh/gfpbryf5ww9l9bh/AADkqz3Z_1aOXaR1_CBdztBka?dl=0">https://www.dropbox.com/sh/gfpbryf5ww9l9bh/AADkqz3Z_1aOXaR1_CBdztBka?dl=0</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Kelkoo
                                    </td>
                                    <td >
                                        <a target="_blank" href="https://partner.kelkoo.com/statsSelectionService.xml?pageType=custom&username=VisionAPIKelkoo&password=ih90ry47">https://partner.kelkoo.com/statsSelectionService.xml?pageType=custom&username=VisionAPIKelkoo&password=ih90ry47</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Shopzilla
                                    </td>
                                    <td >
                                        <a target="_blank" href="https://www.dropbox.com/sh/bv5adtlvdgrl10w/AABstXKe-1VwZJYs5KWdj-KDa?dl=0">https://www.dropbox.com/sh/bv5adtlvdgrl10w/AABstXKe-1VwZJYs5KWdj-KDa?dl=0</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Twenga
                                    </td>
                                    <td >
                                        <a target="_blank" href="ftp://visionapi:0eSAaSJS2pKm@ftp-01.twenga.com/subid/">ftp://visionapi:0eSAaSJS2pKm@ftp-01.twenga.com/subid/</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        AdWorks
                                    </td>
                                    <td >
                                        <a target="_blank" href="https://www.dropbox.com/sh/alvg262pu8e338t/AABQI_3ubSUPDspQtebmuhO2a?dl=0">https://www.dropbox.com/sh/alvg262pu8e338t/AABQI_3ubSUPDspQtebmuhO2a?dl=0</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-link font-blue-sharp"></i>
                        <span class="caption-subject font-blue-sharp bold uppercase">Vision Api Output Url</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-container">
                        <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
                            <thead>
                            <tr role="row" class="heading">
                                <th >
                                    Link
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td >
                                    <a target="_blank" href="https://www.dropbox.com/sh/bs0orwitz2vnq4k/AADgdf37IIY_f5gaOUXjz5B5a?dl=0">https://www.dropbox.com/sh/bs0orwitz2vnq4k/AADgdf37IIY_f5gaOUXjz5B5a?dl=0</a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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
@stop

@section('footer')
    <script language="JavaScript" type="text/javascript">
        var csrf_token ='{{ csrf_token() }}';
        var url_get_states='{{url('api/get_states')}}';
    </script>
    <script src="{{ URL::asset('pages/scripts/admin/advertisers-index.js')}}"></script>
@stop