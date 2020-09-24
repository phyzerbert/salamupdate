<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Notification;

use Auth;

class NotificationController extends Controller
{
    public function index(Request $request) {
        config(['site.page' => 'notification']);
        $mod = new Notification();
        if(Auth::user()->company) {
            $mod = $mod->where('company_id', Auth::user()->company_id);
        }
        $data = $mod->orderBy('created_at', 'desc')->paginate(15);
        return view('notification.index', compact('data'));
    }
}
