<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Api\UserRequest;
use Cache;

class UsersController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth', [            
            'except' => ['show', 'create', 'store',]
        ]);
        
         /*$this->middleware('guest', [
            'only' => ['create']
        ]);*/
    }
    
    //注册视图
    public function create()
    {
        return view('users.create');
    }
    
    //用户展示视图
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }
    
    //注册逻辑
    public function store(UserRequest $request)
    {
        $verifyData = Cache::get($request->verification_key);

        if (!$verifyData) {
            return $this->response->error('验证码已失效', 422);
        }

        if (!hash_equals($verifyData['email']['code'], $request->verification_code)) {
            // 返回401
            return $this->response->errorUnauthorized('验证码错误');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $verifyData['email']['e_name'],
            'password' => bcrypt($request->password),
            'information'=>$request->information,
        ]);

        // 清除验证码缓存
        Cache::forget($request->verification_key);

        return $this->response->created();
    }
    
    //修改视图
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }
    
    //修改逻辑，不包括密码
    public function update(User $user, Request $request)
    {
        $this->authorize('update', $user);
        
        $this->validate($request, [
            'name' => 'required|min:2|max:10',
            'information'=>'nullable|max:300',
        ]);

        $data = [];
        $data['name'] = $request->name;
        if ($request->information) {
            $data['information'] = $request->information;
        }
        $user->update($data);
        
        session()->flash('success', '修改成功！');
        
        
        return redirect()->route('users.show', $user->id);
    }
}