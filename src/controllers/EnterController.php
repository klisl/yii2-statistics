<?php

namespace Klisl\Statistics\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;




class EnterController extends Controller
{

    public function index(Request $request){

        $password_config = config('statistics.password');
        $password_enter = $request->input('password');
//        dd($password_enter);
        if($password_config == $password_enter){

            session(['ksl-statistics' => true]);
//            $cookie = cookie('ksl-statistics', 'ksl', 12*60);
//            \Session::set('ksl-statistics', true);

            return redirect()->route('statistics');

        } else {
            session()->flash('error', 'Неверный пароль');
            return view('Views::enter');
        }

    }
}
