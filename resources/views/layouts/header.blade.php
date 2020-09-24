<div class="topbar">
        <div class="topbar-left">
            <div class="text-center">
                <a href="{{route('home')}}" class="logo"><span>{{config('app.name')}} </span></a>
            </div>
        </div>
        
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="list-inline menu-left mb-0 flex-grow-1 d-flex">
                    <div class="float-left">
                        <a href="#" class="button-menu-mobile open-left">
                            <i class="fa fa-bars"></i>
                        </a>
                    </div>
                    {{-- Check last password updated date --}}
                    @php
                        $password_change_flag = 0;
                        if(Auth::user()->password_updated_at) {
                            $password_updated_at = new \Carbon\Carbon(Auth::user()->password_updated_at);
                            $now = \Carbon\Carbon::now();
                            $difference = $password_updated_at->diff($now)->days;
                            if($difference >= 30) {
                                $password_change_flag = 1;
                            }
                        } else {
                            $password_change_flag = 1;
                        }
                    @endphp
                    @if ($password_change_flag)
                        <div class="pl-md-3 flex-grow-1 text-center">
                            <div class="alert alert-danger mb-0 mt-3 py-2 alert-dismissible">
                                <button type="button" class="close py-2" data-dismiss="alert">&times;</button>
                                {{__('page.please_change_your_password')}}
                            </div>
                        </div>
                    @endif
                    
                </div>
    
                <ul class="nav navbar-right float-right list-inline">
                    <li class="user-company hide-phone mr-5 pt-2">
                        @if (Auth::user()->hasRole('user') || Auth::user()->hasRole('secretary'))
                            <span class="text-light">{{ Auth::user()->company->name }}</span>
                        @endif
                    </li>
                    <li class="dropdown dropdown-lang">
                        @php $locale = session()->get('locale'); @endphp
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                            @switch($locale)
                                @case('en')
                                    <img src="{{asset('images/lang/en.png')}}" width="30px">&nbsp;&nbsp;<span class="hide-phone"> English</span>
                                    @break
                                @case('es')
                                    <img src="{{asset('images/lang/es.png')}}" width="30px">&nbsp;&nbsp;<span class="hide-phone"> Español</span>
                                    @break
                                @default
                                    <img src="{{asset('images/lang/es.png')}}" width="30px">&nbsp;&nbsp;<span class="hide-phone"> Español</span>
                            @endswitch
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li><a class="dropdown-item" href="{{route('lang', 'en')}}"><img src="{{asset('images/lang/en.png')}}" class="rounded-circle" width="30px" height="30"> English</a></li>
                            <li><a class="dropdown-item" href="{{route('lang', 'es')}}"><img src="{{asset('images/lang/es.png')}}" class="rounded-circle" width="30px" height="30"> Español</a></li>
                        </ul>
                    </li>
                    {{-- Notification --}}
                    @php
                        if (Auth::user()->hasRole('user') || Auth::user()->hasRole('secretary')) {
                            $notification_count = Auth::user()->company->notifications()->count();
                            $notifications = Auth::user()->company->notifications()->orderBy('created_at', 'desc')->get()->take(10);
                        } else {
                            $notification_count = \App\Models\Notification::count();
                            $notifications = \App\Models\Notification::orderBy('created_at', 'desc')->get()->take(10);
                        }
                    @endphp
                    <li class="dropdown notification-list">
                        <a class="nav-link dropdown-toggle  waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <i class="md md-notifications"></i>
                            <span class="badge badge-info badge-pill badge-xs">{{$notification_count}}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-lg" style="min-width: 300px">
                            <div class="dropdown-item noti-title py-2">
                                <h4 class="m-0">
                                    {{__('page.notification')}}
                                </h4>
                            </div>

                            <div class="slimscroller">
                    
                                @foreach ($notifications as $item)                                    
                                    <a href="javascript:;" class="dropdown-item notify-item">
                                        <p class="notify-details ml-0">
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
                                            <small class="noti-time">{{$item->created_at}}</small>
                                        </p>
                                    </a>
                                @endforeach
                            </div>
                            <!-- All-->
                            <a href="{{route('notification.index')}}" class="dropdown-item text-center notify-item notify-all">
                                {{__('page.view_all_notifications')}}
                            </a>
                        </div>
                    </li>
                    {{-- End Notification --}}
                    <li class="dropdown open">
                        <a href="#" class="dropdown-toggle profile" data-toggle="dropdown" aria-expanded="true">
                            <img src="@if (Auth::user()->picture != ''){{asset(Auth::user()->picture)}} @else {{asset('images/avatar128.png')}} @endif" class="wd-32 rounded-circle" alt="">
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="{{route('profile')}}" class="dropdown-item"><i class="fa fa-user mr-2"></i> {{__('page.my_profile')}}</a></li>
                            <li>
                                <a href="#"
                                    class="dropdown-item"
                                    onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();" 
                                ><i class="fa fa-sign-out mr-2"></i> {{__('page.sign_out')}}</a>
                            </li>                        
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </ul>
                    </li>
                    @if(!Auth::user()->hasRole('buyer'))
                        <li class="">
                            <a href="#" class="right-bar-toggle waves-effect waves-light">
                                <i class="md md-chat"></i>
                                <span class="badge badge-pill badge-xs badge-danger" id="total_unreads"></span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </nav>
    </div>