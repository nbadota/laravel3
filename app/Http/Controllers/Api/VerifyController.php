<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\Api\SendEmailVerify;
use App\Notifications\EmailVerify;

use Carbon\Carbon;
use Notification;
use Cache;


class VerifyController extends Controller
{
    public function store(SendEmailVerify $request){
        $cooldown = 60; // 验证码发送冷却间隔。
 
        $key = 'Verify@emailcode:' . md5($request->email);
 
        // 检测间隔时间。
        if (($email = Cache::get($key)) && ($seconds = $email['created_at']->diffInSeconds(null, false)) < $cooldown) {
            // 返回剩余冷却时间。
            return $cooldown - $seconds;
        }
 
        // 创建验证码。
        $email = [
            'code' => sprintf('%06d', mt_rand(0, 999999))
        ];
 
        // 发送邮件验证码。
        Notification::route('mail', $request->email)->notify(new EmailVerify($email['code']));
 
        // 将验证码放入缓存中。
        $email['created_at'] = Carbon::now();
        $email['e_name'] = $request->email;
        $expires_at = Carbon::now()->addMinutes(10); // 过期时间为 10 分钟。
        Cache::put($key, $email, $expires_at);
        return $this->response->array([
            'key' => $key,
            'expired_at' => $expires_at->toDateTimeString(),
            'email' => $email,
            'cooldown' => $cooldown,
        ])->setStatusCode(201);
	
    }
}
