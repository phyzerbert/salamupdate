@extends('layouts.master')
@section('style')
    <style>
        .card-image {
            position: relative;
            width: 40px;
            height: 40px;
            margin-right: 7px;
            cursor: pointer;
            float: left;
        }
        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .btn-delete-image {
            position: absolute;
            color: #EFA720;
            top: -2px;
            right: 2px;
        }
    </style>
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-cubes"></i> {{__('page.product_management')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li><a href="#">{{__('page.product')}}</a></li>
                        <li class="active">{{__('page.list')}}</li>
                    </ol>
                </div>
            </div>

            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="card card-body">
                <div class="">
                    @include('elements.pagesize')
                    @include('product.filter')
                    <a href="{{route('product.create')}}" class="btn btn-success btn-sm float-right tx-white mg-b-5" id="btn-add"><i class="fa fa-plus mg-r-2"></i> Add New</a>
                </div>
                <div class="table-responsive mt-2">
                    <table class="table table-bordered table-hover">
                        <thead class="">
                            <tr class="bg-blue">
                                <th style="width:30px;">#</th>
                                <th style="width:200px"></th>
                                <th>{{__('page.product_code')}}</th>
                                <th>{{__('page.product_name')}}</th>
                                <th>{{__('page.category')}}</th>
                                <th>{{__('page.product_cost')}}</th>
                                <th>{{__('page.product_price')}}</th>
                                <th>{{__('page.quantity')}}</th>
                                <th>{{__('page.product_unit')}}</th>
                                <th>{{__('page.alert_quantity')}}</th>
                                <th>{{__('page.action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                            @php
                                $quantity = $item->calc_quantity();
                            @endphp
                                <tr>
                                    <td>{{ (($data->currentPage() - 1 ) * $data->perPage() ) + $loop->iteration }}</td>
                                    <td class="py-1">
                                        @forelse ($item->images as $image)
                                            @if (file_exists($image->path))
                                                @php
                                                    $path_parts = pathinfo($image->path);
                                                    $ext = $path_parts['extension'];
                                                    if($ext == 'pdf') {
                                                        $image_path = '/images/pdf.png';
                                                    } else {
                                                        $image_path = $image->path;
                                                    }
                                                @endphp     
                                                <div class="card-image">
                                                    <img src="{{asset($image_path)}}" href="{{asset($image->path)}}" class="product-image border rounded" alt="">
                                                    <span class="btn-delete-image btn-confirm" href="{{route('purchase.image.delete', $image->id)}}"><i class="fa fa-times-circle-o"></i></span>
                                                </div>
                                            @endif
                                        @empty
                                            <p class="text-muted my-2">No Images</p>
                                        @endforelse
                                    </td>
                                    <td class="code">{{$item->code}}</td>
                                    <td class="name">{{$item->name}}</td>
                                    <td class="category">@isset($item->category->name){{$item->category->name}}@endisset</td>
                                    <td class="cost">{{number_format($item->cost)}}</td>
                                    <td class="">{{number_format($item->price)}}</td>
                                    <td class="quantity">{{number_format($quantity)}}</td>
                                    <td class="unit">{{$item->unit}}</td>
                                    <td class="alert_quantity">{{$item->alert_quantity}}</td>
                                    <td class="py-2" align="center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-info dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                {{__('page.action')}}
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="{{route('product.detail', $item->id)}}" class="dropdown-item">{{__('page.details')}}</a></li>
                                                <li><a href="{{route('product.edit', $item->id)}}" class="dropdown-item">{{__('page.edit')}}</a></li>
                                                <li><a href="{{route('product.delete', $item->id)}}" class="dropdown-item btn-confirm">{{__('page.delete')}}</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="clearfix mt-2">
                        <div class="float-left" style="margin: 0;">
                            <p>{{__('page.total')}} <strong style="color: red">{{ $data->total() }}</strong> {{__('page.items')}}</p>
                        </div>
                        <div class="float-right" style="margin: 0;">
                            {!! $data->appends(['name' => $name, 'code' => $code, 'category_id' => $category_id])->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script src="{{asset('master/plugins/ezview/EZView.js')}}"></script>
<script src="{{asset('master/plugins/jquery-ui/jquery-ui.js')}}"></script>
<script>
    $(document).ready(function () {
        $("#btn-reset").click(function(){
            $("#search_code").val('');
            $("#search_name").val('');
            $("#search_category").val('');
        });
        if($(".product-image").length) {
            $(".product-image").EZView();
        }
    });
</script>
@endsection
