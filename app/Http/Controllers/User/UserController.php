<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Validator;
use Illuminate\Http\Request;
use Redirect;
use Mail;
use DB;
use \Illuminate\Auth\Passwords\TokenRepositoryInterface;

class UserController extends Controller
{

  protected $tokens;

  public function __construct(TokenRepositoryInterface $tokens)
  {
      $this->tokens = $tokens;
  }

  /**
   * Get the dashboard.
   *
   * @return View
   */
  protected function getDashboard($id = false)
  {
    if($id){
      $user = User::where('id',$id)->firstOrFail();
    } else {
      $user = Auth::user();
    }

    $this->authorize('dashboard', $user);

    $dataUsage = DB::table('questionnaires')
            ->join('videos', 'questionnaires.id', '=', 'videos.questionnaire_id')
            ->where('questionnaires.owner_id', '=', $user->id)
            ->sum('videos.size');

    $data = array(
      'user' => $user,
      'users' => $user->users()->get(),
      'groups' => $user->groups()->get(),
      'questionnaires' => $user->questionnaires()->paginate(15),
      'dataUsage' => $dataUsage
    );

    return view('user.dashboard', $data);
  }

  /**
   * Get the users profile.
   *
   * @return View
   */
  protected function getProfile($id = false)
  {
    if($id){
      $user = User::where('id',$id)->firstOrFail();
    } else {
      $user = Auth::user();
    }

    $this->authorize('profile-view', $user);

    $data = array(
      'user'=> $user
    );
    return view('user.profile', $data);
  }

  /**
   * Get the users profile edit form.
   *
   * @return View
   */
  protected function getEditProfile($id)
  {
    $user = User::where('id',$id)->firstOrFail();

    $this->authorize('profile-edit', $user);

    $data = array(
      'user'=> $user
    );
    return view('user.editProfile', $data);
  }

  /**
   * Edit the user.
   *
   * @return Redirect
   */
  protected function postEditProfile(Request $request, $id)
  {
    $user = User::where('id',$id)->firstOrFail();

    $this->authorize('profile-edit', $user);

    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'email' => 'email|unique:users,email,' . $user->id,
        'cas_username' => 'unique:users,cas_username,' . $user->id
    ]);

    if ($validator->fails()) {
        return Redirect::action('User\UserController@getEditProfile', $user->id)
            ->withInput()
            ->withErrors($validator);
    }

    $user->name = $request->name;
    $user->email = $request->email;
    $user->cas_username = $request->cas_username;
    $user->save();

    return Redirect::action('User\UserController@getProfile', $user->id)->with('status', 'Saved profile');
  }

  /**
   * Get the users profile remove form
   *
   * @return View
   */
  protected function getRemoveProfile($id)
  {
    $user = User::where('id',$id)->firstOrFail();

    $this->authorize('profile-remove', $user);

    $data = array(
      'user'=> $user
    );
    return view('user.removeProfile', $data);
  }

  /**
   * Remove the user
   *
   * @return Redirect
   */
  protected function postRemoveProfile(Request $request, $id)
  {
    $user = User::where('id',$id)->firstOrFail();

    $this->authorize('profile-remove', $user);

    $user->delete();

    return Redirect::action('User\UserController@getProfile')->with('status', 'Removed');
  }

  /**
   * Get the groups of a user.
   *
   * @return View
   */
  protected function getGroups()
  {
      $user = Auth::user();

      $data = array(
        'groups' => $user->groups()->get(),
      );

      return view('user.groups', $data);
  }

  /**
   * Return the form for creating a new group.
   *
   * @return View
   */
  protected function getCreateGroup()
  {
      return view('user.createGroup');
  }

  /**
   * Create and validate a new group.
   *
   * @return Redirect
   */
  protected function postCreateGroup(Request $request)
  {
    $user = Auth::user();

    $validator = Validator::make($request->all(), [
        'name' => 'required'
    ]);

    if ($validator->fails()) {
        return Redirect::action('User\UserController@getCreateGroup')
            ->withInput()
            ->withErrors($validator);
    }

    $group = new User(); // groups are build from users referencing to eachother. This allows complex user/group structures and gives the ability to do user action with a group.
    $group->name = $request->name;
    $group->save();
    $group->users()->attach($user->id, array('role'=> 'admin'));

    return Redirect::action('User\UserController@getGroups')->with('status', 'Group created');
  }

  /**
   * Return the form for creating a new user.
   *
   * @return View
   */
  protected function getCreateUser($group_id)
  {
    $group = User::where('id',$group_id)->firstOrFail();

    $this->authorize('user-create', $group);

    $data = array(
      'group' => $group
    );

    return view('user.createUser', $data);
  }

  /**
   * Create and validate a new user.
   *
   * @return Redirect
   */
  protected function postCreateUser(Request $request, $group_id)
  {
    $user = Auth::user();
    $group = User::where('id',$group_id)->firstOrFail();

    $this->authorize('user-create', $group);

    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'email' => 'required|email|unique:users,email',
        'cas_username' => 'unique:users,cas_username'
    ]);

    if ($validator->fails()) {
        return Redirect::action('User\UserController@getCreateUser', $group_id)
            ->withInput()
            ->withErrors($validator);
    }

    $new_user = new User();
    $new_user->name = $request->name;
    $new_user->email = $request->email;
    $new_user->cas_username = $request->cas_username;
    $new_user->save();
    $new_user->groups()->attach($group->id, array('role'=> 'member'));

    if($request->send_email){
      $token = $this->tokens->create($new_user);
      $data = array(
        'token' => $token,
        'new_user' => $new_user,
        'user' => $user
      );
      Mail::send('emails.newUser', $data, function($message) use ($new_user, $user){
          $message->to($new_user->email, $new_user->name)->subject('Invitation from ' . $user->name);
      });
    }

    return Redirect::action('User\UserController@getDashboard', $group->id)->with('status', 'User created');
  }

  /**
   * Return the form for adding a user to a group.
   *
   * @return View
   */
  protected function getAddUser($id)
  {
    $group = User::where('id',$id)->firstOrFail();
    $users = User::all();
    $users_in_group = $group->users()->get();

    $this->authorize('user-add', $group);

    $data = array(
      'group' => $group,
      'users' => $users->diff($users_in_group)->except($group->id)
    );

    return view('user.addUser', $data);
  }

  /**
   * Add and validate a user that needs to be added to a group
   *
   * @return Redirect
   */
  protected function postAddUser(Request $request, $group_id)
  {
    $group = User::where('id',$group_id)->firstOrFail();

    $this->authorize('user-add', $group);

    $validator = Validator::make($request->all(), [
        'user_id' => 'required'
    ]);

    if ($validator->fails()) {
        return Redirect::action('User\UserController@getAddUser', $request->group_id)
            ->withInput()
            ->withErrors($validator);
    }

    $user = User::where('id',$request->user_id)->firstOrFail();
    $users_in_group = $group->users()->get();
    if($users_in_group->keyBy('id')->has($user->id)){
      abort(403, 'User already in group');
    }
    if( $group->id === $user->id ){
      abort(403, 'User is same as group');
    }
    $group->users()->attach($user->id, array('role'=> 'member'));

    return Redirect::action('User\UserController@getDashboard', $group->id)->with('status', 'User added to group');
  }

  /**
   * Return the form removing a user.
   *
   * @return View
   */
  protected function getRemoveUser($group_id, $user_id)
  {
    $group = User::where('id',$group_id)->firstOrFail();
    $user = $group->users()->where('id', $user_id)->firstOrFail();

    $this->authorize('user-remove', $group);

    $admin_count_error = false;
    if($user->pivot->role == 'admin' && $this->countAdmins($group) == 1) {
      $admin_count_error = true;
    }

    $data = array(
      'group' => $group,
      'user' => $user,
      'admin_count_error' => $admin_count_error
    );

    return view('user.removeUser', $data);
  }

  /**
   * Remove a user.
   *
   * @return Redirect
   */
  protected function postRemoveUser(Request $request, $group_id, $user_id)
  {
    $group = User::where('id',$group_id)->firstOrFail();
    $user = $group->users()->where('id', $user_id)->firstOrFail();

    $this->authorize('user-remove', $group);

    if($user->pivot->role == 'admin' && $this->countAdmins($group) == 1) {
      abort(403, 'You need at least one admin role for this group');
    }

    $group->users()->detach($user->id);

    return Redirect::action('User\UserController@getDashboard', $group->id)->with('status', 'User removed from group');
  }

  /**
   * Return the form for changing a role inside the group.
   *
   * @return View
   */
  protected function getRoleUser($group_id, $user_id, $role)
  {
    $group = User::where('id',$group_id)->firstOrFail();
    $user = $group->users()->where('id', $user_id)->firstOrFail();
    $admin_count_error = false;
    if($role != 'admin' && $user->pivot->role == 'admin' && $this->countAdmins($group) == 1) {
      $admin_count_error = true;
    }

    $this->authorize('user-role-edit', $group);

    $data = array(
      'group' => $group,
      'user' => $user,
      'role' => $role,
      'admin_count_error' => $admin_count_error
    );

    return view('user.roleUser', $data);
  }

  /**
   * Change the role of a user.
   *
   * @return Redirect
   */
  protected function postRoleUser(Request $request, $group_id, $user_id, $role)
  {
    $group = User::where('id',$group_id)->firstOrFail();
    //$user = User::where('id',$user_id)->firstOrFail();

    $this->authorize('user-role-edit', $group);

    $user = $group->users()->where('id', $user_id)->firstOrFail();
    if($role != 'admin' && $user->pivot->role == 'admin' && $this->countAdmins($group) == 1) {
      abort(403, 'You need at least one admin role for this group');
    }

    $group->users()->updateExistingPivot($user->id, array('role' => $role));

    return Redirect::action('User\UserController@getDashboard', $group->id)->with('status', 'Changed user role');
  }

  private function countAdmins($group){
    $users = $group->users()->withPivot('role')->where('role','admin')->get();
    return $users->count();
  }
}
