@extends('layouts.auth')

@section('content')
    <div class="wrapper-page">
        <div class="card card-pages">
            <div class="card-header py-4" style="background-image: url('/images/sign_in.jpg')"> 
                <div class="bg-overlay"></div>
                <h3 class="text-center m-t-10 text-white"> {{__('page.password_expired')}}</h3>
            </div> 

            <div class="card-body">
                <form class="form-horizontal m-t-20" action="{{route('password.post_expired')}}" method="post">
                    @csrf
                    <div class="form-group">
                        <input type="password" class="form-control" name="current_password" required autofocus placeholder="{{__('page.current_password')}}">
                        @error('current_password')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <input id="password" type="password" class="form-control" name="password" required placeholder="{{__('page.new_password')}}">
    
                        @error('password')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required placeholder="{{__('page.confirm_password')}}">
                    </div>
                    
                    <div class="form-group row text-center mt-3">
                        <div class="col-md-12 pt-3">
                            <a href="{{route('lang', 'en')}}" class="btn btn-outline p-0 @if(config('app.locale') == 'en') border-primary border-2 @endif" title="English"><img src="{{asset('images/lang/en.png')}}" width="45px"></a>
                            <a href="{{route('lang', 'es')}}" class="btn btn-outline ml-2 p-0 @if(config('app.locale') == 'es') border-primary border-2 @endif" title="Spanish"><img src="{{asset('images/lang/es.png')}}" width="45px"></a>
                        </div>
                        <div class="col-md-12">
                            <button class="btn btn-primary btn-lg w-lg waves-effect waves-light mt-2" type="submit"><i class="fa fa-sign-in"></i> {{__('page.reset_password')}}</button>
                        </div>
                    </div>

                    
                </form> 
            </div>                
        </div>
    </div>
@endsection

@section('script')
    <script>
        var notification = '<?php echo session()->get("ip_restriction"); ?>';
        if(notification != ''){
            Swal.fire({
                type: 'error',
                title: notification,
            })
        }
    </script>
@endsection
