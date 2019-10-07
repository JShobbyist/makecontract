<?php

namespace App\Http\Controllers;

use App\Advert;
use App\Deposit;
use App\Interest;
use App\InterestLog;
use App\Invest;
use App\Notice;
use App\Notifications\AdminCreateUser;
use App\Profile;
use App\Referral;
// use App\Reflink;
use App\Share;
use App\TransferLog;
use App\User;
use App\UserLog;
use App\Video;
use App\Withdraw;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AdminUsersController extends Controller
{

    public function __construct()
    {

        $this->middleware('admin');

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $s= $request->input('s');

        $users = User::latest()->search($s)->paginate(10);

        return view('admin.users.index',compact('users','s'));


    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function affiliate(Request $request)
    {
        //
        $s= $request->input('s');

        $users = User::latest()->where('admin', '2')->search($s)->paginate(10);

        return view('admin.users.affiliate',compact('users','s'));


    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function client(Request $request)
    {
        //
        $s= $request->input('s');

        $users = User::latest()->where('admin', '0')->search($s)->paginate(10);

        return view('admin.users.client',compact('users','s'));


    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function verified(Request $request)
    {
        //
        $s= $request->input('s');

        $users = User::whereActive(1)->latest()->search($s)->paginate(10);

        return view('admin.users.active',compact('users','s'));


    }
    public function banned(Request $request)
    {

        $s= $request->input('s');
        $users = User::whereBan(1)->latest()->search($s)->paginate(10);
        return view('admin.users.banned',compact('users','s'));

    }
    public function unverified(Request $request)
    {

        $s= $request->input('s');
        $users = User::whereActive(0)->latest()->search($s)->paginate(10);
        return view('admin.users.unverified',compact('users','s'));

    }
    public function create()
    {
        //


        return view('admin.users.create');


    }
    public function createclient(Request $request)
    {
        //

        $s= $request->input('s');

        $users = User::latest()->where('admin', '2')->paginate(10);

        $searchuser = User::latest()->search($s)->first()->name;

        return view('admin.users.createclient',compact('users','s', 'searchuser'));


    }


    public function contract(Request $request)
    {
        $s= $request->input('s');

        $users = User::latest()->search($s)->paginate(10);

        return view('admin.users.contract',compact('users','s'));
    }



    public function editcontract()
    {
        return view('admin.users.eidtcontract');
    }


    public function upcontract(Request $request)
    {

        $user = User::whereEmail($request->email)->firstOrFail();


        $notice = new Notice();
        $notice->user_id = $user->id;
        $notice->title = "Contrato";
        $notice->priority = 3;
        $notice->body = "Aqui está o seu Contrato.<br> Por favor, faça o Download.";
        $notice->status = 0;

        if ($request->hasFile('featured')){

            $featured = $request->featured;

            $featured_new_name = time().$featured->getClientOriginalName();

            $featured->move('uploads/posts', $featured_new_name);

            $notice->file = 'uploads/posts/'. $featured_new_name;

        }

        $notice->save();

        session()->flash('message', "Você enviou um Contratato para ".$user->name." com sucesso!");
        Session::flash('type', 'success');
        Session::flash('title', 'Contrato enviado com sucesso!');

        return redirect()->route('admin.user.contract');

    }

    public function viewcontract($id)
    {

        $user = User::whereid($id)->firstOrFail();
        
        $inboxes = Notice::whereUser_id($id)->where('title', 'Contrato')->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.users.contractview', compact('inboxes', 'user'));
    }


    public function contractShow($id)
    {
        $inbox = Notice::find($id);
        $userid = Notice::whereid($id)->firstOrFail()->user_id;
        $user = User::whereid($userid)->firstOrFail();

        return view('admin.users.contractShow', compact('inbox', 'user'));
    }



    /**
     * Store a newly created resource in storage(affiliate).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

        $this->validate($request, [

            'name'=> 'required',
            'email' => 'required|email',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required',
            'confirm-password' => 'required|same:password'
        ]);

        $user = User::create([

            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'admin'=>2,
            'active'=>0,
            'membership_id'=>1,
            'membership_started'=>date('Y-m-d'),
            'membership_expired'=>'2020-12-31',
            'token'=>str_random(25),

        ]);


        $profile = Profile::create([

            'user_id' => $user->id,
            'avatar'=>'uploads/avatars/default.jpg'

        ]);


        $data = (object) array(

            "email"=>$request->email,
            "password"=>$request->password,
            "token"=>$user->token,
        );

        (new User)->forceFill([
            'email' => $request->email,
        ])->notify(new AdminCreateUser($data));

        session()->flash('message', 'The User Has Been Successfully Created.');
        Session::flash('type', 'success');
        Session::flash('title', 'Created Successful');

        return redirect(route('admin.users.index'));


    }


    /**
     * Store a newly created resource in storage(Client).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_client(Request $request)
    {
        //

        $this->validate($request, [

            'name'=> 'required',
            'email' => 'required|email',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required',
            'confirm-password' => 'required|same:password'
        ]);

        $user = User::create([

            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'admin'=>0,
            'parent_id'=> $request->affiliate,
            'active'=>0,
            'membership_id'=>1,
            'membership_started'=>date('Y-m-d'),
            'membership_expired'=>'2020-12-31',
            'token'=>str_random(25),

        ]);


        $profile = Profile::create([

            'user_id' => $user->id,
            'avatar'=>'uploads/avatars/default.jpg'

        ]);


        $data = (object) array(

            "email"=>$request->email,
            "password"=>$request->password,
            "token"=>$user->token,
        );

        (new User)->forceFill([
            'email' => $request->email,
        ])->notify(new AdminCreateUser($data));

        session()->flash('message', 'The User Has Been Successfully Created.');
        Session::flash('type', 'success');
        Session::flash('title', 'Created Successful');

        return redirect(route('admin.users.index'));


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
        $user=User::find($id);
        $invest= Invest::whereUser_id($user->id)->select('amount')->sum('amount');
        $interest= InterestLog::whereUser_id($user->id)->select('amount')->sum('amount');
        
        $ptc= UserLog::whereUser_id($user->id)->whereType(1)->select('amount')->sum('amount');
        $ppv= UserLog::whereUser_id($user->id)->whereType(2)->select('amount')->sum('amount');

        return view('admin.users.show',compact('user','invest','interest','ptc','ppv'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user=User::find($id);

        return view('admin.users.edit',compact('user'));
    }
    public function unSuspend($id)
    {
        $user=User::find($id);
        $user->ban = 0;
        $user->note = null;
        $user->save();


        session()->flash('message', 'The User Account Has Been Successfully Active.');
        Session::flash('type', 'success');
        Session::flash('title', 'Active Successful');

        return redirect()->back();
    }

    public function suspend(Request $request, $id)
    {
        $this->validate($request, [

            'note'=> 'required|min:10|max:200'

        ]);

        $user=User::find($id);
        $user->ban = 1;
        $user->note = $request->note;
        $user->save();


        session()->flash('message', 'The User Has Been Successfully Suspended.');
        Session::flash('type', 'success');
        Session::flash('title', 'Suspended Successful');

        return redirect()->back();
    }

    public function interest($id)
    {
        $user = User::find($id);
        $logs = InterestLog::whereUser_id($user->id)->latest()->get();

        return view('admin.users.interest',compact('logs','user'));
    }
    public function investment($id)
    {
        $user = User::find($id);
        $investments = Invest::whereUser_id($user->id)->latest()->paginate(20);
        return view('admin.users.invest',compact('investments','user'));

    }
    public function cashLinks($id)
    {
        $user = User::find($id);
        $logs = Advert::whereUser_id($user->id)->whereStatus(1)->latest()->get();
        return view('admin.users.ptc',compact('logs','user'));

    }

    public function cashVideos($id)
    {
        $user = User::find($id);
        $logs = Video::whereUser_id($user->id)->whereStatus(1)->latest()->get();
        return view('admin.users.ppv',compact('logs','user'));

    }
    public function LinkShare($id)
    {
        $user = User::find($id);
        $logs = Share::whereUser_id($user->id)->whereStatus(1)->latest()->get();
        return view('admin.users.share',compact('logs','user'));

    }
    public function transfer($id)
    {
        $user = User::find($id);
        $logs = TransferLog::whereUser_id($user->id)->whereStatus(1)->latest()->get();
        return view('admin.users.transfer',compact('logs','user'));

    }
    public function deposit($id)
    {
        $user = User::find($id);
        $logs = Deposit::whereUser_id($user->id)->whereStatus(1)->latest()->get();
        return view('admin.users.deposit',compact('logs','user'));

    }
    public function withdraw($id)
    {
        $user = User::find($id);
        $logs = Withdraw::whereUser_id($user->id)->whereStatus(1)->latest()->get();
        return view('admin.users.withdraw',compact('logs','user'));

    }
    public function details($id)
    {

        $investment = Invest::find($id);
        $interest = Interest::whereInvest_id($investment->id)->first();
        $current = new Carbon($investment->start_time);
        $trialExpires = $current->addDays(30);
        $amount = $investment->amount;
        $percentage =  $investment->plan->percentage;
        $profit = (($percentage / 100) * $amount);

        return view('admin.users.preview',compact('investment','trialExpires','interest','profit'));

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

            $this->validate($request, [

                'name'=> 'required',
                'email' => 'required|email',
                'occupation' => 'required|max:30',
                'mobile' => 'required|min:16|max:18',
                'address' => 'required|max:50',
                'city' => 'required|max:30',
                'state' => 'required|max:30',
                'postcode' => 'required|max:20'

            ]);

            $user = User::find($id);


        if ($request->hasFile('avatar')){

            $this->validate($request, [

                'avatar' => 'required|image'
            ]);



            $avatar = $request->avatar;

            $avatar_new_name = time().$avatar->getClientOriginalName();

            $avatar->move('uploads/avatars', $avatar_new_name);

            $user->profile->avatar = 'uploads/avatars/'. $avatar_new_name;

            $user->profile->save();

        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->admin = $request->admin;
        $user->active = $request->active;
        $user->profile->main_balance = $request->main_balance;
        $user->profile->referral_balance = $request->referral_balance;
        $user->profile->deposit_balance = $request->deposit_balance;
        $user->profile->occupation = $request->occupation;
        $user->profile->mobile = $request->mobile;
        $user->profile->address = $request->address;
        $user->profile->address2 = $request->address2;
        $user->profile->city = $request->city;
        $user->profile->state = $request->state;
        $user->profile->postcode = $request->postcode;
        $user->profile->country = $request->country;
        $user->profile->facebook = $request->facebook;
        $user->profile->about = $request->about;


        $user->save();

        $user->profile->save();

        if ($request->has('password')){

            $user->password = bcrypt($request->password);

            $user->save();


        }



        session()->flash('message', 'The User Has Been Successfully Updated.');
        Session::flash('type', 'success');
        Session::flash('title', 'Updated Successful');

        return redirect(route('admin.users.index'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $user = User::findOrFail($id);
        $user->delete();

        session()->flash('message', 'The User Has Been Successfully Deleted.');
        Session::flash('type', 'success');
        Session::flash('title', 'Deleted Successful');

        return redirect()->back();


    }
    public function admin($id)
    {
        $user = User::find($id);

        $user->admin=1;

        $user->save();

        session()->flash('message', 'The User Has Been Successfully Get Admin Permission.');
        Session::flash('type', 'success');
        Session::flash('title', 'Permission Granted');

        return redirect()->back();

    }
    public function adminRemove($id)
    {
        $user = User::find($id);

        $user->admin=0;

        $user->save();

        session()->flash('message', 'The User Has Been Successfully Removed Admin Permission.');
        Session::flash('type', 'success');
        Session::flash('title', 'Permission Removed');

        return redirect()->back();


    }

}
