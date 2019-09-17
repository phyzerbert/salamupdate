@extends('layouts.master')

@section('style')
    
@endsection

@section('content')
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-wechat"></i> {{__('page.chatting')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li class="active">{{__('page.chatting')}}</li>
                    </ol>
                </div>
            </div>
            <div class="" id="app">
                <chat-component :user="{{auth()->user()}}"></chat-component>
            </div>
        </div>                
    </div>    
@endsection

@section('script')
    <script src="{{ asset('js/app.js') }}"></script>
@endsection