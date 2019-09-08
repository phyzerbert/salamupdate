@extends('layouts.master')
@section('style')
    <link href="{{asset('master/lib/select2/css/select2.min.css')}}" rel="stylesheet">
    <link href="{{asset('master/lib/jquery-ui/jquery-ui.css')}}" rel="stylesheet">
    <link href="{{asset('master/lib/jquery-ui/timepicker/jquery-ui-timepicker-addon.min.css')}}" rel="stylesheet">
    <script src="{{asset('master/lib/vuejs/vue.js')}}"></script>
    <script src="{{asset('master/lib/vuejs/axios.js')}}"></script>
@endsection
@section('content')
    <div class="br-mainpanel" id="app">
        <div class="br-pageheader pd-y-15 pd-l-20">
            <nav class="breadcrumb pd-0 mg-0 tx-12">
                <a class="breadcrumb-item" href="{{route('home')}}">{{__('page.home')}}</a>
                <a class="breadcrumb-item" href="#">{{__('page.sale')}}</a>
                <a class="breadcrumb-item active" href="#">{{__('page.edit')}}</a>
            </nav>
        </div>
        <div class="pd-x-20 pd-sm-x-30 pd-t-20 pd-sm-t-30">
            <h4 class="tx-gray-800 mg-b-5"><i class="fa fa-edit"></i> {{__('page.edit_sale')}}</h4>   
            <input type="hidden" name="" data-id="{{$sale->id}}" data-type="sale" id="data">         
        </div>

        @php
            $role = Auth::user()->role->slug;
        @endphp
        <div class="br-pagebody">
            <div class="br-section-wrapper">
                <form class="form-layout form-layout-1" action="{{route('sale.update')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{$sale->id}}">
                    <div class="row mg-b-25">
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.sale_date')}}: <span class="tx-danger">*</span></label>
                                <input class="form-control" type="text" name="date" id="sale_date" value="{{date('Y-m-d H:i', strtotime($sale->timestamp))}}" placeholder="Sale Date" autocomplete="off" required>
                                @error('date')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.reference_number')}}:</label>
                                <input class="form-control" type="text" name="reference_number" value="{{$sale->reference_no}}" placeholder="{{__('page.reference_number')}}">
                                @error('reference_number')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>                        
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.user')}}:</label>
                                <select class="form-control select2-show-search" name="user" data-placeholder="{{__('page.user')}}">
                                    <option label="{{__('page.user')}}"></option>
                                    @foreach ($users as $item)
                                        <option value="{{$item->id}}" @if($sale->biller_id == $item->id) selected @endif>{{$item->name}}</option>
                                    @endforeach
                                </select>
                                @error('user')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row mg-b-25">                        
                        <div class="col-md-6 col-lg-3">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.store')}}:</label>
                                <select class="form-control select2" name="store" data-placeholder="Select Store">
                                    <option label="Select Store"></option>
                                    @foreach ($stores as $item)
                                        <option value="{{$item->id}}" @if($sale->store_id == $item->id) selected @endif>{{$item->name}}</option>
                                    @endforeach
                                </select>
                                @error('store')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.customer')}}:</label>
                                <select class="form-control select2-show-search" name="customer" data-placeholder="{{__('page.customer')}}">
                                    <option label="Customer"></option>
                                    @foreach ($customers as $item)
                                        <option value="{{$item->id}}" @if($sale->customer_id == $item->id) selected @endif>{{$item->name}}</option>
                                    @endforeach
                                </select>
                                @error('customer')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.attachment')}}:</label>
                                <input type="file" name="attachment" id="file2" class="file-input-styled">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.status')}}:</label>
                                <select class="form-control select2" name="status" data-placeholder="Status">
                                    <option label="Status"></option>
                                    <option value="0" @if($sale->status == 0) selected @endif>{{__('page.pending')}}</option>
                                    <option value="1" @if($sale->status == 1) selected @endif>{{__('page.received')}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mg-b-25">
                        <div class="col-md-12">
                            <div>
                                <h5 class="mg-t-10" style="float:left">{{__('page.order_items')}}</h5>
                                <a href="#" class="btn btn-primary btn-icon rounded-circle mg-b-10 add-product" style="float:right" @click="add_item()"><div><i class="fa fa-plus"></i></div></a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-colored table-success" id="product_table">
                                    <thead>
                                        <tr>
                                            <th>{{__('page.product_name_code')}}</th>
                                            <th>{{__('page.product_price')}}</th>
                                            <th>{{__('page.quantity')}}</th>
                                            <th>{{__('page.product_tax')}}</th>
                                            <th>{{__('page.subtotal')}}</th>
                                            <th class="wd-30"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $orders = $sale->orders;
                                        @endphp
                                        <tr v-for="(item,i) in order_items" :key="i">
                                            <td>
                                                <select class="form-control input-sm select2 product" name="product_id[]" v-model="item.product_id" @change="get_product(i)">
                                                    <option value="" hidden>{{__('page.select_product')}}</option>
                                                    <option :value="product.id" v-for="(product, i) in products" :key="i">@{{product.name}}(@{{product.code}})</option>
                                                </select>
                                            </td>
                                            <td><input type="number" class="form-control form-control-sm price" name="price[]" v-model="item.price" placeholder="{{__('page.product_price')}}" /></td>
                                            <td><input type="number" class="form-control input-sm quantity" name="quantity[]" v-model="item.quantity" placeholder="Quantity" /></td>
                                            <td class="tax">@{{item.tax_name}}</td>
                                            <td class="subtotal">
                                                @{{item.sub_total}}
                                                <input type="hidden" name="subtotal[]" :value="item.sub_total" />
                                                <input type="hidden" name="order_id[]" :value="item.order_id" />
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-warning btn-icon rounded-circle mg-t-3 remove-product" @click="remove(i)"><div style="width:25px;height:25px;"><i class="fa fa-times"></i></div></a>
                                            </td>
                                        </tr>

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2">{{__('page.total')}}</td>
                                            <td class="total_quantity">@{{total.quantity}}</td>
                                            <td class="total_tax"></td>
                                            <td colspan="2" class="total">@{{total.price}}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.note')}}:</label>
                                <textarea class="form-control" name="note" rows="5" placeholder="{{__('page.note')}}">{{$sale->note}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-layout-footer text-right">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-check mg-r-2"></i>{{__('page.save')}}</button>
                        <a href="{{route('sale.index')}}" class="btn btn-warning"><i class="fa fa-times mg-r-2"></i>{{__('page.cancel')}}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script src="{{asset('master/lib/select2/js/select2.min.js')}}"></script>
<script src="{{asset('master/lib/jquery-ui/jquery-ui.js')}}"></script>
<script src="{{asset('master/lib/jquery-ui/timepicker/jquery-ui-timepicker-addon.min.js')}}"></script>
<script src="{{asset('master/lib/styling/uniform.min.js')}}"></script>
<script>
    $(document).ready(function () {

        $("#sale_date").datetimepicker({
            dateFormat: 'yy-mm-dd',
        });
        $(".expire_date").datepicker({
            dateFormat: 'yy-mm-dd',
        });  

        $('.file-input-styled').uniform({
            fileButtonClass: 'action btn bg-primary text-white'
        });

    });
</script>
<script src="{{ asset('js/sale_edit_order_items.js') }}"></script>
@endsection
