<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\Company;

use Auth;
use Hash;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        config(['site.page' => 'user']);
        $companies = Company::all();
        $mod = new User();
        $company_id = $name = $phone_number = '';
        if ($request->get('company_id') != ""){
            $company_id = $request->get('company_id');
            $mod = $mod->where('company_id', "$company_id");
        }
        if ($request->get('name') != ""){
            $name = $request->get('name');
            $mod = $mod->where('name', 'LIKE', "%$name%");
        }
        if ($request->get('phone_number') != ""){
            $phone_number = $request->get('phone_number');
            $mod = $mod->where('phone_number', 'LIKE', "%$phone_number%");
        }
        $pagesize = session('pagesize');
        if(!$pagesize){$pagesize = 15;}
        $data = $mod->orderBy('created_at', 'desc')->paginate($pagesize);
        return view('admin.users', compact('data', 'companies', 'company_id', 'name', 'phone_number'));
    }

        
    public function profile(Request $request){
        $user = Auth::user();
        config(['site.page' => 'profile']);
        $companies = Company::all();
        return view('profile', compact('user', 'companies'));
    }

    public function updateuser(Request $request){
        $validation_rules = [
            'name'=>'required',
            'phone_number'=>'required',
        ];
        if($request->get('password') != '') {
            $validation_rules['password'] = [
                'confirmed',
                'string',
                'min:8',             // must be at least 10 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[@$!%*#?&]/', // must contain a special character
            ];
        }
        $request->validate($validation_rules);
        $user = Auth::user();
        $user->name = $request->get("name");
        $user->email = $request->get("email");
        $user->phone_number = $request->get("phone_number");
        $user->first_name = $request->get("first_name");
        $user->last_name = $request->get("last_name");

        if($request->get('password') != '') {
            if(Hash::check($request->get('password'), $user->password)) {
                return back()->withErrors(['password' => __('page.same_password')]);
            }
            $user->password = Hash::make($request->get('password'));
            $user->password_updated_at = date('Y-m-d H:i:s');
        }
        if($request->has("picture")){
            $picture = request()->file('picture');
            $imageName = time().'.'.$picture->getClientOriginalExtension();
            $picture->move(public_path('images/profile_pictures'), $imageName);
            $user->picture = 'images/profile_pictures/'.$imageName;
        }
        $user->update();
        return back()->with("success", __('page.updated_successfully'));
    }

    public function edituser(Request $request){
        $user = User::find($request->get("id"));
        $validation_rules = [
            'name'=>'required',
            'phone_number'=>'required',
        ];
        if($request->get('password') != '') {
            $validation_rules['password'] = [
                'confirmed',
                'string',
                'min:8',             // must be at least 10 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[@$!%*#?&]/', // must contain a special character
            ];
        }
        $request->validate($validation_rules);
        
        $user->name = $request->get("name");
        $user->email = $request->get("email");
        $user->first_name = $request->get("first_name");
        $user->last_name = $request->get("last_name");
        $user->phone_number = $request->get("phone_number");
        $user->company_id = $request->get("company_id");
        $user->ip_address = $request->get("ip_address");

        if($request->get('password') != ''){
            if(Hash::check($request->get('password'), $user->password)) {
                return response()->json(['message' => __('page.same_password')]);
            }
            $user->password = Hash::make($request->get('password'));
            $user->password_updated_at = date('Y-m-d H:i:s');
        }
        $user->save();
        return response()->json('success');
    }

    public function create(Request $request){
        $validate_array = array(
            'name'=>'required|string|unique:users',
            'role'=>'required',
            'phone_number'=>'required',
            'password'=> [
                'required',
                'confirmed',
                'string',
                'min:8',             // must be at least 10 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[@$!%*#?&]/', // must contain a special character
            ],
        );
        if($request->get('role') == '2' || $request->get('role') == '4'){
            $validate_array['company_id'] = 'required';
        }
        
        $request->validate($validate_array);
        
        User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'phone_number' => $request->get('phone_number'),
            'company_id' => $request->get('company_id'),
            'role_id' => $request->get('role'),
            'ip_address' => $request->get('ip_address'),
            'password' => Hash::make($request->get('password'))
        ]);
        return response()->json('success');
    }

    public function delete($id){
        $user = User::find($id);
        if(!$user){
            return back()->withErrors(["delete" => __('page.something_went_wrong')]);
        }
        $user->delete();
        return back()->with("success", __('page.deleted_successfully'));
    }
}
