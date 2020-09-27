@extends('layouts.master')
@section('style')    
    <link href="{{asset('master/plugins/select2/dist/css/select2.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/select2/dist/css/select2-bootstrap.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/jquery-ui/jquery-ui.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/jquery-ui/timepicker/jquery-ui-timepicker-addon.min.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/daterangepicker/daterangepicker.min.css')}}" rel="stylesheet">
@endsection
@section('content')
    @php
        config(['site.page' => 'advanced_delete']);
    @endphp
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-trash-o"></i> {{__('page.advanced_delete')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li class="active">{{__('page.advanced_delete')}}</li>
                    </ol>
                </div>
            </div>    
        
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-body">                        
                        <form class="form-layout form-layout-1" action="" method="POST" id="delete_form">
                            @csrf
                            <div class="form-group my-3">
                                <label class="form-control-label">{{__('page.date')}}</label>
                                <input class="form-control" id="period" type="text" name="date" placeholder="{{__('page.date')}}" autocomplete="off">
                            </div>
                            @php
                                $suppliers = \App\Models\Supplier::orderBy('name')->get();
                            @endphp
                            <div class="form-group mb-2">
                                <label class="form-control-label">{{__('page.supplier')}}</label>
                                <select class="form-control select2" name="supplier" id="search_supplier" class="wd-100" multiple="multiple">
                                    @foreach ($suppliers as $item)
                                        <option value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-layout-footer mt-3">
                                <button type="submit" class="btn btn-primary" id="btn_request"><i class="fa fa-paper-plane mr-2"></i> {{__('page.request')}}</button>
                            </div>
                            <div class="form-group mt-3 verify" style="display: none">
                                <label class="form-control-label">{{__('page.verification_code')}}</label>
                                <input class="form-control" type="text" name="verification_code" id="verification_code" placeholder="{{__('page.input_verification_code')}}">
                            </div>
                            <div class="form-layout-footer mt-3 verify" style="display: none">
                                <button type="button" class="btn btn-primary" id="btn_verify"><i class="fa fa-check mr-2"></i> {{__('page.confirm')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>                
    </div>
@endsection

@section('script')
<script src="{{asset('master/plugins/select2/dist/js/select2.min.js')}}"></script>
<script src="{{asset('master/plugins/jquery-ui/jquery-ui.js')}}"></script>
<script src="{{asset('master/plugins/jquery-ui/timepicker/jquery-ui-timepicker-addon.min.js')}}"></script>
<script src="{{asset('master/plugins/daterangepicker/jquery.daterangepicker.min.js')}}"></script>
<script>
    $(document).ready(function () {
        $("#period").dateRangePicker({
            autoClose: false,
        });

        $('#search_supplier')
            .select2({
                width: 'resolve',
                multiple: true,
            });

        $("#delete_form").submit(function(e){
            e.preventDefault();
            let suppliers = $("#search_supplier").val();

            let request_data = {
                period: $("#period").val(),
                supplier: $("#search_supplier").val().toString(),
            };
            $.ajax({
                url: "{{route('advanced_delete.request')}}",
                data: request_data,
                method: 'POST',
                dataType: 'json',
                success: function (data) {
                    if(data.status == 200) {                       
                        $("#btn_request").attr('disabled', 'true');
                        $(".verify").show();
                    } else if (data.status == 400) {
                        Swal.fire(data.message)
                    } else {
                        Swal.fire('Something went wrong!');
                    }
                }
            });
        });
        $("#btn_verify").click(function () {
            $.ajax({
                url: "{{route('advanced_delete.verify')}}",
                data: {verification_code: $("#verification_code").val()},
                method: 'POST',
                dataType: 'json',
                success: function (data) {
                    if(data.status == 200) {                       
                        Swal.fire(data.message).then(function () {
                            window.location.reload();
                        });
                    } else if (data.status == 400) {
                        Swal.fire(data.message)
                    } else {
                        Swal.fire('Something went wrong!');
                    }
                }
            });
        })
    });
</script>
@endsection
