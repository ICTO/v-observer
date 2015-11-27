<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\User;
use Redirect;
use Hash;
use Validator;
use Illuminate\Http\Request;

class PageController extends Controller
{
  /**
   * Get the welcome page.
   *
   * @return View
   */
  protected function getWelcome()
  {
    $user = User::get()->first();

    if(!$user){
      return Redirect::action('Pages\PageController@getSetup');
    }

    return view('pages.welcome');
  }

  /**
   * Get the setup page.
   *
   * @return View
   */
  protected function getSetup()
  {
    $user = User::get()->first();

    if($user){
      return Redirect::action('Pages\PageController@getWelcome')->with('status','Setup is already finished');
    }

    return view('pages.setup');
  }

  /**
   * Get the setup page.
   *
   * @return View
   */
  protected function postSetup(Request $request)
  {
    $user = User::get()->first();

    if($user){
      abort('403', 'Setup already finished');
    }

    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'email' => 'required|email|unique:users,email',
        'cas_username' => 'unique:users,cas_username',
        'password' => 'required|min:8'
    ]);

    if ($validator->fails()) {
        return Redirect::action('Pages\PageController@getSetup')
            ->withInput()
            ->withErrors($validator);
    }

    $user = new User();
    $user->name = $request->name;
    $user->email = $request->email;
    $user->password = Hash::make($request->password);
    $user->cas_username = $request->cas_username;
    $user->super_admin = true;
    $user->save();

    return Redirect::action('User\UserController@getDashboard', $user->id )->with('status','Setup finished');
  }
}
