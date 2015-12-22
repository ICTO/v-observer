<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if user can view the profile.
     *
     * @return bool
     */
    public function profileView(User $user, User $user_profile)
    {
        // if own profile
        if($user->id === $user_profile->id){
            return true;
        }

        // if profile of parent
        $user_groups = $user->groups()->get();
        foreach ($user_groups as $user_group) {
            if($user_group->id === $user_profile->id){
                return true;
            }
        }

        // if profile from same group
        $user_profile_groups = $user_profile->groups()->get();
        foreach ($user_groups as $user_group) {
            foreach($user_profile_groups as $user_profile_group){
                if($user_group->id === $user_profile_group->id) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Determine if user can view the profile.
     *
     * @return bool
     */
    public function profileEdit(User $user, User $user_profile)
    {
        // if own profile
        if($user->id === $user_profile->id){
            return true;
        }

        if($this->isGroupAdmin($user, $user_profile)){
            return true;
        }

        return false;
    }

    /**
     * Determine if user can remove the profile.
     *
     * @return bool
     */
    public function profileRemove(User $user, User $user_profile)
    {
        // if own profile
        if($user->id === $user_profile->id){
            return true;
        }

        if($this->isGroupAdmin($user, $user_profile)){
            return true;
        }

        return false;
    }

    /**
     * Determine if user can view admin dropdowns.
     *
     * @return bool
     */
    public function profileMenu(User $user, User $group)
    {
        if(    $this->profileEdit($user, $group)
            || $this->profileRemove($user, $group)
        ){
            return true;
        }

        return false;
    }

    /**
     * Determine if user can view the dashboard of a profile.
     *
     * @return bool
     */
    public function dashboard(User $user, User $user_dashboard)
    {
        // if own profile
        if($user->id === $user_dashboard->id){
            return true;
        }

        // if profile of parent
        $user_groups = $user->groups()->get();
        foreach ($user_groups as $user_group) {
            if($user_group->id === $user_dashboard->id){
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if user can create a user in a group.
     *
     * @return bool
     */
    public function userCreate(User $user, User $group)
    {
        return $this->isGroupAdmin($user, $group);
    }

    /**
     * Determine if user can create a user in a group.
     *
     * @return bool
     */
    public function userAdd(User $user, User $group)
    {
        return $this->isGroupAdmin($user, $group);
    }

    /**
     * Determine if user can remove a user in a group.
     *
     * @return bool
     */
    public function userRemove(User $user, User $group)
    {
        return $this->isGroupAdmin($user, $group);
    }

    /**
     * Determine if user can change the users role in a group.
     *
     * @return bool
     */
    public function userRoleEdit(User $user, User $group)
    {
        return $this->isGroupAdmin($user, $group);
    }

    /**
     * Determine if user can view admin dropdowns.
     *
     * @return bool
     */
    public function userMenu(User $user, User $group)
    {
        if(    $this->userRoleEdit($user, $group)
            || $this->userRemove($user, $group)
        ){
            return true;
        }

        return false;
    }

    /**
     * Determine if user can add a questionnaire.
     *
     * @return bool
     *
     * @TODO : move to questionPolicy if possible?
     */
    public function QuestionnaireCreate(User $user, User $owner)
    {
        // if own profile
        if($owner->id === $user->id){
            return true;
        }

        if($this->isGroupAdmin($user, $owner)){
            return true;
        }

        return false;
    }

    /**
     * Helper function to determin if the user is admin of the group.
     */
    private function isGroupAdmin(User $user, User $group){
        $user_group = $user->groups()->where('id', $group->id)->get();
        if(!$user_group->isEmpty() && $user_group->first()->pivot->role == 'admin'){
            return true;
        }
        return false;
    }
}
