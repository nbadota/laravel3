<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SessionsController extends Controller
{
    //登录视图
     public function create()
    {
        return view('sessions.create');
    }
    
    //登录逻辑
    public function store(Request $request)
    {
       $credentials = $this->validate($request, [
           'email' => 'required|email',
           'password' => 'required|min:6|max:12'
       ]);

       if (Auth::attempt($credentials, $request->has('remember'))) {
               session()->flash('success', '欢迎回来！');
               $fallback = route('users.show', Auth::user());
               return redirect()->intended($fallback);
       } else {
           session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
           return redirect()->back()->withInput();
       }
    }
    
    public function destroy()
    {
        Auth::logout();
        session()->flash('success', '您已成功退出！');
        return redirect('login');
    }
}
