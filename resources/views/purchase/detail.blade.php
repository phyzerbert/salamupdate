@extends('layouts.master')
@section('style')    
    <link href="{{asset('master/plugins/select2/dist/css/select2.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/select2/dist/css/select2-bootstrap.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('master/plugins/imageviewer/css/jquery.verySimpleImageViewer.css')}}">
    <style>
        #image_preview {
            max-width: 600px;
            height: 600px;
        }
        .image_viewer_inner_container {
            width: 100% !important;
        }
    </style>
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-info-circle"></i> {{__('page.purchase_detail')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li><a href="{{route('purchase.index')}}">{{__('page.purchase')}}</a></li>
                        <li class="active">{{__('page.details')}}</li>
                    </ol>
                </div>
            </div>
        
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="card card-body">
                <div class="row row-deck">
                    <div class="col-lg-4">
                        <div class="card card-fill card-body bg-success detail-card">
                            <div class="row">
                                <div class="col-xl-3 text-center">
                                    <div class="detail-card-icon bg-warning"><i class="fa fa-plug"></i></div>
                                </div>
                                <div class="col-xl-9">
                                    <h3 class="text-white mb-2">{{__('page.supplier')}}</h3>
                                    <p class="text-light" style="font-size:16px;"><strong>{{__('page.name')}}</strong> : @isset($purchase->supplier->name){{$purchase->supplier->name}}@endisset</p>
                                    <p class="text-light" style="font-size:16px;"><strong>{{__('page.email')}}</strong> : @isset($purchase->supplier->email){{$purchase->supplier->email}}@endisset</p>
                                    <p class="text-light" style="font-size:16px;"><strong>{{__('page.phone')}}</strong> : @isset($purchase->supplier->phone_number){{$purchase->supplier->phone_number}}@endisset</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card card-fill card-body bg-info detail-card">
                            <div class="row">
                                <div class="col-xl-3 text-center">
                                    <div class="detail-card-icon bg-white"><i class="fa fa-truck"></i></div>
                                </div>
                                <div class="col-xl-9">
                                    <h3 class="text-white mb-2">{{__('page.store')}}</h3>
                                    <p class="text-light" style="font-size:16px;"><strong>{{__('page.name')}}</strong> : @isset($purchase->store->name){{$purchase->store->name}}@endisset</p>
                                    <p class="text-light" style="font-size:16px;"><strong>{{__('page.company')}}</strong> : @isset($purchase->store->company->name){{$purchase->store->company->name}}@endisset</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">                    
                        <div class="card card-fill card-body bg-secondary detail-card">
                            <div class="row">
                                <div class="col-xl-3 text-center">
                                    <div class="detail-card-icon bg-primary text-light"><i class="fa fa-file-text-o"></i></div>
                                </div>
                                <div class="col-xl-9">
                                    <h3 class="text-white mb-2">{{__('page.reference')}}</h3>
                                    <p class="text-light" style="font-size:16px;"><strong>{{__('page.number')}}</strong> : {{$purchase->reference_no}}</p>
                                    <p class="text-light" style="font-size:16px;"><strong>{{__('page.date')}}</strong> : {{$purchase->timestamp}}</p>
                                    <p class="text-light" style="font-size:16px;">
                                        <strong>{{__('page.attachment')}} : </strong>
                                        @if ($purchase->attachment != "")
                                            <a href="#" class="attachment" data-value="{{$purchase->attachment}}">&nbsp;&nbsp;&nbsp;<i class="fa fa-paperclip"></i></a>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-t-20">
                    <div class="col-md-12">
                        <h5>Supplier Note</h5>
                        <p class="mx-2">@isset($purchase->supplier->note){{$purchase->supplier->note}}@endisset</p>
                    </div>
                </div>
                <div class="row mg-t-20">
                    <div class="col-12 table-responsive">
                        <h5>{{__('page.order_items')}}</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>{{__('page.product_code')}}</th>
                                    <th>{{__('page.product_name')}}</th>
                                    <th>{{__('page.product_cost')}}</th>
                                    <th>{{__('page.quantity')}}</th>
                                    <th>{{__('page.product_tax')}}</th>
                                    <th>{{__('page.subtotal')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total_quantity = 0;
                                    $total_tax_rate = 0;
                                    $total_amount = 0;
                                    $paid = $purchase->payments()->sum('amount');
                                @endphp
                                @foreach ($purchase->orders as $item)
                                    @php
                                        $tax = ($item->product->tax) ? $item->product->tax->rate : 0;
                                        $quantity = $item->quantity;
                                        $cost = $item->cost;
                                        $tax_rate = $cost * $tax / 100;
                                        $subtotal = $item->subtotal;

                                        $total_quantity += $quantity;
                                        $total_tax_rate += $tax_rate;
                                        $total_amount += $subtotal;
                                    @endphp
                                    <tr>
                                        <td>{{$loop->index+1}}</td>
                                        <td>@isset($item->product->code){{$item->product->code}}@endisset</td>
                                        <td>@isset($item->product->name){{$item->product->name}}@endisset</td>
                                        <td>{{number_format($item->cost)}}</td>
                                        <td>{{$item->quantity}}</td>
                                        <td>@isset($item->product->tax->name){{$item->product->tax->name}}@endisset</td>
                                        <td>{{number_format($item->subtotal)}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="tx-bold tx-black">
                                <tr>
                                    <th colspan="4" class="tx-bold" style="text-align:right">{{__('page.total')}} (COP)</th>
                                    <th>{{$total_quantity}}</th>
                                    <th>{{$total_tax_rate}}</th>
                                    <th>{{number_format($total_amount)}}</th>
                                </tr>
                                <tr>
                                    <th colspan="6" style="text-align:right">{{__('page.discount')}} (COP)</th>
                                    <th>
                                        @if(strpos( $purchase->discount_string , '%' ) !== false)
                                            {{$purchase->discount_string}} ({{number_format($purchase->discount)}})
                                        @else
                                            {{number_format($purchase->discount)}}
                                        @endif
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="6" style="text-align:right">{{__('page.shipping')}} (COP)</th>
                                    <th>
                                        @if(strpos( $purchase->shipping_string , '%' ) !== false)
                                            {{$purchase->shipping_string}} ({{number_format($purchase->shipping)}})
                                        @else
                                            {{number_format($purchase->shipping)}}
                                        @endif
                                    </th>
                                </tr>
                                
                                <tr>
                                    <th colspan="6" style="text-align:right">{{__('page.returns')}}</th>
                                    <th>
                                        {{number_format($purchase->returns)}}
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="6" style="text-align:right">{{__('page.total_amount')}} (COP)</th>
                                    <th>{{number_format($purchase->grand_total)}}</th>
                                </tr>
                                <tr>
                                    <th colspan="6" style="text-align:right">{{__('page.paid')}} (COP)</th>
                                    <th>{{number_format($paid)}}</th>
                                </tr>
                                <tr>
                                    <th colspan="6" style="text-align:right">{{__('page.balance')}} (COP)</th>
                                    <th>{{number_format($purchase->grand_total - $paid)}}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>{{__('page.note')}}</h5>
                        <p class="mx-2">{{$purchase->note}}</p>
                    </div>
                </div>
                <h5>{{__('page.payment_list')}}</h5>
                <div class="row">
                    <div class="col-12 table-resposnive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th>{{__('page.date')}}</th>
                                    <th>{{__('page.reference_no')}}</th>
                                    <th>{{__('page.amount')}}</th> 
                                    <th>{{__('page.note')}}</th>
                                </tr>
                            </thead>
                            <tbody>                                
                                @foreach ($purchase->payments as $item)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td class="date">{{date('Y-m-d H:i', strtotime($item->timestamp))}}</td>
                                        <td class="reference_no">{{$item->reference_no}}</td>
                                        <td class="amount" data-value="{{$item->amount}}">{{number_format($item->amount)}}</td>
                                        <td class="" data-path="{{$item->attachment}}">
                                            <span class="tx-info note">{{$item->note}}</span>&nbsp;
                                            @if($item->attachment != "")
                                                <a href="{{asset($item->attachment)}}" download><i class="fa fa-paperclip"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table> 
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-sm-12 col-md-3">
                        <div class="card card-body card-fill bg-success ">
                            <h6 class="card-title text-white mb-2">{{__('page.created_by')}} @isset($purchase->user->name){{$purchase->user->name}}@endisset</h6>
                            <h6 class="card-title text-white">{{__('page.created_at')}} {{$purchase->created_at}}</h6>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-9 text-right">
                        <a href="{{route('purchase.index')}}" class="btn btn-secondary"><i class="fa fa-credit-card"></i> {{__('page.purchases_list')}}</a>
                        <a href="{{route('payment.index', ['purchase', $purchase->id])}}" class="btn btn-info"><i class="icon ion-cash"></i> {{__('page.payment_list')}}</a>
                    </div>
                </div>
            </div>
        </div>                
    </div>

    <div class="modal fade" id="attachModal">
        <div class="modal-dialog" style="margin-top:17vh">
            <div class="modal-content">
                <div id="image_preview"></div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script src="{{asset('master/plugins/imageviewer/js/jquery.verySimpleImageViewer.min.js')}}"></script>
<script src="{{asset('master/plugins/select2/dist/js/select2.min.js')}}"></script>
<script>
    $(document).ready(function () {
        $(".attachment").click(function(e){
            e.preventDefault();
            let path = '{{asset("/")}}' + $(this).data('value');
            $("#image_preview").html('')
            $("#image_preview").verySimpleImageViewer({
                imageSource: path,
                frame: ['100%', '100%'],
                maxZoom: '900%',
                zoomFactor: '10%',
                mouse: true,
                keyboard: true,
                toolbar: true,
            });
            $("#attachModal").modal();
        });

    });
</script>
@endsection
