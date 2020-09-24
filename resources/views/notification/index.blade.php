@extends('layouts.master')

@section('content')
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-bell"></i> {{__('page.notification')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li class="active">{{__('page.notification')}}</li>
                    </ol>
                </div>
            </div>
        
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="card card-body card-fill">
                <div class="table-responsive mt-2">
                    <table class="table table-bordered table-hover">
                        <thead class="">
                            <tr class="bg-blue">
                                <th width="40">#</th>
                                <th>{{__('page.company')}}</th>
                                <th>{{__('page.message')}}</th>
                                <th>{{__('page.reference_no')}}</th>
                                <th>{{__('page.supplier')}}</th>
                                <th>{{__('page.amount')}}</th>
                                <th>{{__('page.time')}}</th>
                            </tr>
                        </thead>
                        <tbody>                                
                            @foreach ($data as $item)
                                <tr>
                                    <td>{{ (($data->currentPage() - 1 ) * $data->perPage() ) + $loop->iteration }}</td>
                                    <td class="company">{{$item->company->name ?? ''}}</td>
                                    <td class="message">
                                        @switch($item->message)
                                            @case('purchase_approved')
                                                {{__('page.purchase_approved')}}
                                                @break
                                            @case('payment_approved')
                                                {{__('page.payment_approved')}}
                                                @break
                                            @case('purchase_rejected')
                                                {{__('page.purchase_rejected')}}
                                                @break
                                            @case('payment_rejected')
                                                {{__('page.payment_rejected')}}
                                                @break
                                            @case('refund_approved')
                                                {{__('page.refund_approved')}}
                                                @break
                                            @case('refund_rejected')
                                                {{__('page.refund_rejected')}}
                                                @break
                                            @default                                                    
                                        @endswitch
                                    </td>
                                    <td class="reference_no">{{$item->reference_no}}</td>
                                    <td class="supplier">{{$item->supplier}}</td>
                                    <td class="amount">{{number_format($item->amount)}}</td>
                                    <td class="time">{{$item->created_at}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>                
                    <div class="clearfix mt-2">
                        <div class="float-left" style="margin: 0;">
                            <p>{{__('page.total')}} <strong style="color: red">{{ $data->total() }}</strong> {{__('page.items')}}</p>
                        </div>
                        <div class="float-right" style="margin: 0;">
                            {!! $data->appends([])->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>                
    </div>
@endsection
