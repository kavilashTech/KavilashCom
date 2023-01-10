<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use App\Models\User;
use App\Models\Cart;
use App\Models\BusinessSetting;
use Cookie;
use Session;
use Auth;
use Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Notifications\Messages\MailMessage;
use App\Mail\SecondEmailVerifyMailManager;

class CustomerController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:view_all_customers'])->only('index');
        $this->middleware(['permission:login_as_customer'])->only('login');
        $this->middleware(['permission:ban_customer'])->only('ban');
        $this->middleware(['permission:delete_customer'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $users = User::where('user_type', 'customer')->where('email_verified_at', '!=', null)->orderBy('created_at', 'desc');
        if ($request->has('search')){
            $sort_search = $request->search;
            $users->where(function ($q) use ($sort_search){
                $q->where('name', 'like', '%'.$sort_search.'%')->orWhere('email', 'like', '%'.$sort_search.'%');
            });
        }
        $users = $users->paginate(15);
        return view('backend.customer.customers.index', compact('users', 'sort_search'));
    }
    public function franciesindex(Request $request)
    {
        return view('frontend.user.franchisee_customer');
    }
    public function customerListindex(Request $request)
    {
        $user_id = Auth::user()->id;
        $customers = \App\Models\user::where('franchisee_id', $user_id)->paginate(15);
        return view('frontend.user.franchisee_customer_list', compact('customers'));
    }

    protected function validator(array $data)
    {
        if(isset($data['franchisee'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255',
                'password' => 'required|string|min:6|confirmed',
                'phone' => 'required',
                'state_id' => 'required',
                'city_id' => 'required',
            ]);
        }else{
            return Validator::make($data, [
                'name' => 'required|string|max:255',
                'password' => 'required|string|min:6|confirmed',
            ]);
        }
    }


    protected function create(array $data)
    {

        if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            if(isset($data['franchisee'])){
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'city' => $data['city_id'],
                    'state' => $data['state_id'],
                    'phone' => '+91'.$data['phone'],
                    'user_type' => 'customer',
                    'franchisee_id' =>Auth::user()->id,
                ]);
            }else{
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                ]);
            }
        }
        else {
            if (addon_is_activated('otp_system')){
                $user = User::create([
                    'name' => $data['name'],
                    'phone' => '+'.$data['country_code'].$data['phone'],
                    'password' => Hash::make($data['password']),
                    'verification_code' => rand(100000, 999999)
                ]);

                $otpController = new OTPVerificationController;
                $otpController->send_code($user);
            }
        }

        if(session('temp_user_id') != null){
            Cart::where('temp_user_id', session('temp_user_id'))
                    ->update([
                        'user_id' => $user->id,
                        'temp_user_id' => null
            ]);

            Session::forget('temp_user_id');
        }

        if(Cookie::has('referral_code')){
            $referral_code = Cookie::get('referral_code');
            $referred_by_user = User::where('referral_code', $referral_code)->first();
            if($referred_by_user != null){
                $user->referred_by = $referred_by_user->id;
                $user->save();
            }
        }

        return $user;
    }


    public function customerFranchiseeRegister(Request $request)
    {
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            if(User::where('email', $request->email)->first() != null){
                flash(translate('Email or Phone already exists.'));
                return back();
            }
        }
        elseif (User::where('phone', '+'.$request->country_code.$request->phone)->first() != null) {
            flash(translate('Phone already exists.'));
            return back();
        }

        $this->validator($request->all())->validate();

        $user = $this->create($request->all());



        if(BusinessSetting::where('type', 'email_verification')->first()->value != 1){
            $user->email_verified_at = date('Y-m-d H:m:s');
            $user->save();
            flash(translate('Registration successful.'))->success();
            return back();
        }
        else {

            try {
                $user->sendEmailVerificationNotification();
                flash(translate('Registration successful. Please verify your email.'))->success();
                return back();
            } catch (\Throwable $th) {

                $user->delete();

                flash(translate('Registration failed. Please try again later.'))->error();
                return back();
            }
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required',
            'email'         => 'required|unique:users|email',
            'phone'         => 'required|unique:users',
        ]);

        $response['status'] = 'Error';

        $user = User::create($request->all());

        $customer = new Customer;

        $customer->user_id = $user->id;
        $customer->save();

        if (isset($user->id)) {
            $html = '';
            $html .= '<option value="">
                        '. translate("Walk In Customer") .'
                    </option>';
            foreach(Customer::all() as $key => $customer){
                if ($customer->user) {
                    $html .= '<option value="'.$customer->user->id.'" data-contact="'.$customer->user->email.'">
                                '.$customer->user->name.'
                            </option>';
                }
            }

            $response['status'] = 'Success';
            $response['html'] = $html;
        }

        echo json_encode($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = User::findOrFail($id);
        $customer->customer_products()->delete();

        User::destroy($id);
        flash(translate('Customer has been deleted successfully'))->success();
        return redirect()->route('customers.index');
    }

    public function bulk_customer_delete(Request $request) {
        if($request->id) {
            foreach ($request->id as $customer_id) {
                $customer = User::findOrFail($customer_id);
                $customer->customer_products()->delete();
                $this->destroy($customer_id);
            }
        }

        return 1;
    }

    public function login($id)
    {
        $user = User::findOrFail(decrypt($id));

        auth()->login($user, true);

        return redirect()->route('dashboard');
    }

    public function ban($id) {
        $user = User::findOrFail(decrypt($id));

        if($user->banned == 1) {
            $user->banned = 0;
            flash(translate('Customer UnBanned Successfully'))->success();
        } else {
            $user->banned = 1;
            flash(translate('Customer Banned Successfully'))->success();
        }

        $user->save();

        return back();
    }

    public function franchiseeindex(Request $request)
    {
        $sort_search = null;
        $users = User::where('user_type', 'partner')->where('email_verified_at', '!=', null)->orderBy('status', 'Asc');
        if ($request->has('search')){
            $sort_search = $request->search;
            $users->where(function ($q) use ($sort_search){
                $q->where('name', 'like', '%'.$sort_search.'%')->orWhere('email', 'like', '%'.$sort_search.'%');
            });
        }
        $users = $users->paginate(15);
        return view('backend.franchisee.index', compact('users', 'sort_search'));
    }
    public function franchiseeapproved(Request $request)
    {

         $user = User::findOrFail($request->userid);
         $user->status = 1;
         $user->reason = '';


        $user->save();






        if (env('MAIL_USERNAME') != null ) {
            $array['view'] = 'emails.verification';
            $array['subject'] = translate('Your Profile approved');
            $array['from'] = env('MAIL_FROM_ADDRESS');
            $array['content'] =translate('Franchise approve login to continue.');

            try {
                Mail::to($user->email)->queue(new SecondEmailVerifyMailManager($array));
            } catch (\Exception $e) {
                dd($e);

            }
        }

        return 1;
    }

    public function franchiseerejected(Request $request)
    {
        $user = User::findOrFail($request->userid);
        $user->status = 2;
        $user->reason = $request->reason;


       $user->save();

       if (env('MAIL_USERNAME') != null ) {
        $array['view'] = 'emails.verification';
        $array['subject'] = translate('Your Profile approved');
        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['content'] =translate('Franchise approve login to continue.');

        try {
            Mail::to($user->email)->queue(new SecondEmailVerifyMailManager($array));
        } catch (\Exception $e) {

        }
    }

    return 1;

    }

    public function moveftanchisee()
    {

        $users = User::where('user_type', 'customer')->where('email_verified_at', '!=', null)->where('franchisee_id','=' ,0)->orderBy('created_at', 'desc')->get();
        $usersfranchisee = User::where('user_type', 'partner')->where('email_verified_at', '!=', null)->orderBy('created_at', 'desc')->get();

        return view('backend.customer.franchisee_move', compact('users','usersfranchisee'));
    }

    public function upgradefranchisee(Request $request)
    {
        $user = User::find($request->customer_id);
        $user->franchisee_id = $request->franchisee_id;
        $user->user_type = 'partner';
        $user->status = '1';
        $user->save();
        flash('Customer upgrade successfully')->success();
        return back();
    }

}
