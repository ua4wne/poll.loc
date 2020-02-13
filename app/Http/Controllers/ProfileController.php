<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class ProfileController extends Controller
{
    public function index()
    {
        if (view()->exists('profile')) {
            $title = 'Профиль пользователя';
            $user = User::find(Auth::user()->id);
            $data = [
                'title' => $title,
                'head' => 'Профиль пользователя ' . Auth::user()->name,
                'user' => $user,
            ];
            return view('profile', $data);
        }
        abort(404);
    }

    public function avatar(Request $request)
    {
        if ($request->hasFile('avatar')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $messages = [
                'required' => 'Поле :attribute обязательно к заполнению',
                'max' => 'Размер файла не должен быть больше 1Мб!',
                'mimes' => 'К загрузке разрешены только графические файлы с расширением jpeg,bmp,png!',
            ];
            $validator = Validator::make($input, [
                'avatar' => 'required|max:1024|file|mimes:jpeg,bmp,png',
            ], $messages);
            if ($validator->fails()) {
                return redirect('/profiles')->withErrors($validator)->withInput();
            }
            $file = $request->file('avatar'); //загружаем файл
            $input['avatar'] = $file->getClientOriginalName();
            $file->move(public_path() . '/images', $input['avatar']);
            $user = User::find(Auth::user()->id);
            $user->image = '/images/'.$input['avatar'];
            $user->update();
            if ('/images/'.$input['avatar'] != $input['old_image'] && ($input['avatar'] != 'male.png' || $input['avatar'] != 'female.png'))
                unlink(public_path() . $input['old_image']); //удаляем старый аватар
            return redirect('/profiles');
        }
    }

    public function edit(Request $request){
        if($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            dd($input);
        }
    }
}
