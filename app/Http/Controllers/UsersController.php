<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', [            
            'except' => ['show', 'create', 'store','index','confirmEmail']
        ]);
        
         $this->middleware('guest', [
            'only' => ['create']
        ]);
    }
    
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }
    
    public function create()
    {
        return view('users.create');
    }
    
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
           'name' => 'required|min:2|max:10|unique:users,name',
           'email' => 'required|unique:users,email|email',
           'password' => 'required|min:6|max:12|confirmed',
           'information'=>'nullable|max:300',
        ]);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'information'=>$request->information,
        ]);
        
        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');
    }
    
    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = '383973155@qq.com';
        $name = 'Zhao';
        $to = $user->email;
        $subject = "感谢注册！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
            $message->from($from, $name)->to($to)->subject($subject);
        });
    }
    
    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }
    
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }
    
    public function update(User $user, Request $request)
    {
        $this->authorize('update', $user);
        
        $this->validate($request, [
            'name' => 'required|min:2|max:10',
            'password' => 'nullable|confirmed|min:6|max:12',
            'information'=>'nullable|max:300',
        ]);

        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        if ($request->information) {
            $data['information'] = $request->information;
        }
        $user->update($data);
        
        session()->flash('success', '修改成功！');
        
        
        return redirect()->route('users.show', $user->id);
    }
}
