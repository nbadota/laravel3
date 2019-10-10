<?php

namespace App\Http\Requests\Api;

use Dingo\Api\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|between:2,10|regex:/^[A-Za-z0-9\-\_]+$/|unique:users,name',
            'password' => 'required|string|between:6,12',
            'information' =>'nullable|max:300',
            'verification_key' => 'required|string',
            'verification_code' => 'required|string',
        ];
    }

    public function attributes()
    {
        return [
            'verification_key' => '邮箱验证码 key',
            'verification_code' => '邮箱验证码',
        ];
    }
}