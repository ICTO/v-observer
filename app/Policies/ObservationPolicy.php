<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Questionaire;
use App\Models\User;

class ObservationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if user can view the questionaire menu.
     *
     * @return bool
     */
    public function QuestionaireMenu(User $user, Questionaire $questionaire)
    {
        if(    $this->QuestionaireEdit($user, $questionaire)
            || $this->QuestionaireRemove($user, $questionaire)
        ){
            return true;
        }

        return false;
    }

    /**
     * Determine if user can view the questionaire.
     *
     * @return bool
     */
    public function QuestionaireView(User $user, Questionaire $questionaire)
    {
        // if own questionaire
        if($questionaire->owner_id === $user->id){
            return true;
        }

        // if questionaire of parent (in group)
        $user_groups = $user->groups()->get();
        foreach ($user_groups as $user_group) {
            if($user_group->id === $questionaire->owner_id){
                return true;
            }
        }
    }

    /**
     * Determine if user can edit the questionaire.
     *
     * @return bool
     */
    public function QuestionaireEdit(User $user, Questionaire $questionaire)
    {
        // if own questionaire
        if($questionaire->owner_id === $user->id){
            return true;
        }

        if($this->isGroupAdmin($user, $questionaire->owner()->get()->first())){
            return true;
        }

        return false;
    }

    /**
     * Determine if user can remove the questionaire.
     *
     * @return bool
     */
    public function QuestionaireRemove(User $user, Questionaire $questionaire)
    {
        return $this->QuestionaireEdit($user, $questionaire);
    }

    /**
     * Determine if user can view the blocks of a questionaire.
     *
     * @return bool
     */
    public function QuestionaireBlockView(User $user, Questionaire $questionaire)
    {
        return $this->QuestionaireView($user, $questionaire);
    }

    /**
     * Determine if user can edit a block of a questionaire.
     *
     * @return bool
     */
    public function QuestionaireBlockEdit(User $user, Questionaire $questionaire)
    {
        if($questionaire->locked){
            return false;
        }
        return $this->QuestionaireEdit($user, $questionaire);
    }

    /**
     * Determine if user can edit a video.
     *
     * @return bool
     */
    public function VideoEdit(User $user, Questionaire $questionaire)
    {
        return $this->QuestionaireEdit($user, $questionaire);
    }

    /**
     * Determine if user can create a video.
     *
     * @return bool
     */
    public function VideoCreate(User $user, Questionaire $questionaire)
    {
        return $this->QuestionaireEdit($user, $questionaire);
    }

    /**
     * Determine if user can remove a video.
     *
     * @return bool
     */
    public function VideoRemove(User $user, Questionaire $questionaire)
    {
        return $this->QuestionaireEdit($user, $questionaire);
    }

    /**
     * Determine if user can view the video menu.
     *
     * @return bool
     */
    public function VideoMenu(User $user, Questionaire $questionaire)
    {
        if(    $this->VideoEdit($user, $questionaire)
            || $this->VideoRemove($user, $questionaire)
            || $this->VideoEditTranscript($user, $questionaire)
            || $this->VideoAnalysis($user, $questionaire)
        ){
            return true;
        }

        return false;
    }

    /**
     * Determine if user can view the video menu.
     *
     * @return bool
     */
    public function VideoMenu2(User $user, Questionaire $questionaire)
    {
        if($this->VideoEditTranscript($user, $questionaire)
            || $this->VideoAnalysis($user, $questionaire)
        ){
            return true;
        }

        return false;
    }

    /**
     * Determine if user can edit a transcript of a video.
     *
     * @return bool
     */
    public function VideoEditTranscript(User $user, Questionaire $questionaire)
    {
        return $this->QuestionaireEdit($user, $questionaire);
    }

    /**
     * Determine if user can do an analysis of a video.
     *
     * @return bool
     */
    public function VideoAnalysis(User $user, Questionaire $questionaire)
    {
        return $this->QuestionaireView($user, $questionaire);
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
