<?php

namespace App\Http\Controllers;

use App\Events\AddEventLogs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
                'required' => 'Поле :attribute обязательно к заполнению!',
                'max' => 'Размер файла не должен быть больше 1Мб!',
                'mimes' => 'К загрузке разрешены только графические файлы с расширением jpeg,bmp,png!',
            ];
            $validator = Validator::make($input, [
                'avatar' => 'required|max:1024|file|mimes:jpeg,bmp,png',
            ], $messages);
            if ($validator->fails()) {
                $msg = 'К загрузке допускаются графические файлы объемом не более 1Мб и с расширением jpeg,bmp,png';
                return redirect('/profiles')->with('error', $msg);
            }
            $file = $request->file('avatar'); //загружаем файл
            $input['avatar'] = $file->getClientOriginalName();
            $file->move(public_path() . '/images', $input['avatar']);
            $user = User::find(Auth::user()->id);
            $user->image = '/images/' . $input['avatar'];
            $user->update();
            if ('/images/' . $input['avatar'] != $input['old_image'] && ($input['avatar'] != 'male.png' || $input['avatar'] != 'female.png'))
                unlink(public_path() . $input['old_image']); //удаляем старый аватар
            return redirect('/profiles');
        }
        $msg = 'Мой милый друг, спасибо! Мои извилины напряг не хило, еще бы файл ты прикрепил - вот это было б мило!';
        return redirect('/profiles')->with('error', $msg);
    }

    public function edit(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $messages = [
                'required' => 'Поле :attribute обязательно к заполнению!',
                'max' => 'Поле :attribute должно быть не более :max символов!',
                'string' => 'Поле :attribute должно быть строкой!',
                'email' => 'Поле :attribute должно быть корректным адресом e-mail!',
            ];
            $validator = Validator::make($input, [
                'name' => 'required|string|max:100',
                'email' => 'required|string|email|max:40',
                'sex' => 'required|string||max:6',

            ], $messages);
            if ($validator->fails()) {
                return 'NO';
            }
            $user = User::find($input['id']);
            $user->fill($input);
            //проверяем уникальность email
            $dbl = User::where(['email'=>$input['email']])->whereNotIn('login',[Auth::user()->login])->first();
            if($dbl)
                return 'DBL';
            if($user->update()){
                $msg = 'Данные пользователя '. $input['name'] .' были обновлены!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return 'OK';
            }
            else
                return 'ERR';
        }
    }

    public function newPass(Request $request){
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            if ($input['newpass'] != $input['confpass']) {
                return 'NO';
            }
            //проверяем что старый пароль указан верно
            $user = User::find(Auth::user()->id);
            $credentials = ['login' => Auth::user()->login, 'password' => $input['oldpass']];
            if (!Auth::validate($credentials)) {
                return 'NOT';
            }
            else{
                if($this->checkPass($input['newpass'])){
                    $user->password = Hash::make($input['newpass']);
                    $user->update();
                    return 'OK';
                }
                else
                    return 'BAD';
            }
        }
        return 'ERR';
    }

    private function checkPass($pass){
        $strong = 0;
        if(strlen($pass)>=8)
            $strong++;
        if(preg_match("/([0-9]+)/", $pass))
            $strong++;
        if(preg_match("/([a-z]+)/", $pass))
            $strong++;
        if(preg_match("/([A-Z]+)/", $pass))
            $strong++;
        if(preg_match("/\W/", $pass))
            $strong++;
        if($strong>=4)
            return true;
        else
            return false;
    }
}
