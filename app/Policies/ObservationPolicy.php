<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Questionnaire;
use App\Models\User;

class ObservationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if user can view the questionnaire menu.
     *
     * @return bool
     */
    public function QuestionnaireMenu(User $user, Questionnaire $questionnaire)
    {
        if(    $this->QuestionnaireEdit($user, $questionnaire)
            || $this->QuestionnaireExport($user, $questionnaire)
            || $this->QuestionnaireRemove($user, $questionnaire)
        ){
            return true;
        }

        return false;
    }

    /**
     * Determine if user can view the questionnaire.
     *
     * @return bool
     */
    public function QuestionnaireView(User $user, Questionnaire $questionnaire)
    {
        // if own questionnaire
        if($questionnaire->owner_id === $user->id){
            return true;
        }

        // if questionnaire of parent (in group)
        $user_groups = $user->groups()->get();
        foreach ($user_groups as $user_group) {
            if($user_group->id === $questionnaire->owner_id){
                return true;
            }
        }
    }

    /**
     * Determine if user can edit the questionnaire.
     *
     * @return bool
     */
    public function QuestionnaireEdit(User $user, Questionnaire $questionnaire)
    {
        // if own questionnaire
        if($questionnaire->owner_id === $user->id){
            return true;
        }

        if($this->isGroupAdmin($user, $questionnaire->owner()->get()->first())){
            return true;
        }

        return false;
    }

    /**
     * Determine if user can remove the questionnaire.
     *
     * @return bool
     */
    public function QuestionnaireRemove(User $user, Questionnaire $questionnaire)
    {
        return $this->QuestionnaireEdit($user, $questionnaire);
    }

    /**
     * Determine if user can export the questionnaire.
     *
     * @return bool
     */
    public function QuestionnaireExport(User $user, Questionnaire $questionnaire)
    {
        return $this->QuestionnaireEdit($user, $questionnaire);
    }

    /**
     * Determine if user can view the blocks of a questionnaire.
     *
     * @return bool
     */
    public function QuestionnaireBlockView(User $user, Questionnaire $questionnaire)
    {
        return $this->QuestionnaireView($user, $questionnaire);
    }

    /**
     * Determine if user can edit a block of a questionnaire.
     *
     * @return bool
     */
    public function QuestionnaireBlockEdit(User $user, Questionnaire $questionnaire)
    {
        if($questionnaire->locked){
            return false;
        }
        return $this->QuestionnaireEdit($user, $questionnaire);
    }

    /**
     * Determine if user can edit a the interval of a questionnaire.
     *
     * @return bool
     */
    public function QuestionnaireIntervalEdit(User $user, Questionnaire $questionnaire)
    {
        if($questionnaire->locked){
            return false;
        }
        return $this->QuestionnaireEdit($user, $questionnaire);
    }

    /**
     * Determine if user can view a video.
     *
     * @return bool
     */
    public function VideoView(User $user, Questionnaire $questionnaire)
    {
        return $this->QuestionnaireView($user, $questionnaire);
    }

    /**
     * Determine if user can edit a video.
     *
     * @return bool
     */
    public function VideoEdit(User $user, Questionnaire $questionnaire)
    {
        return $this->QuestionnaireEdit($user, $questionnaire);
    }

    /**
     * Determine if user can create a video.
     *
     * @return bool
     */
    public function VideoCreate(User $user, Questionnaire $questionnaire)
    {
        return $this->QuestionnaireEdit($user, $questionnaire);
    }

    /**
     * Determine if user can remove a video.
     *
     * @return bool
     */
    public function VideoRemove(User $user, Questionnaire $questionnaire)
    {
        return $this->QuestionnaireEdit($user, $questionnaire);
    }

    /**
     * Determine if user can view the video menu.
     *
     * @return bool
     */
    public function VideoMenu(User $user, Questionnaire $questionnaire)
    {
        if(    $this->VideoEdit($user, $questionnaire)
            || $this->VideoRemove($user, $questionnaire)
            || $this->VideoEditTranscript($user, $questionnaire)
            || $this->VideoAnalysis($user, $questionnaire)
            || $this->VideoAnalysisExport($user, $questionnaire)
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
    public function VideoMenu2(User $user, Questionnaire $questionnaire)
    {
        if($this->VideoEditTranscript($user, $questionnaire)
            || $this->VideoAnalysis($user, $questionnaire)
            || $this->VideoAnalysisExport($user, $questionnaire)
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
    public function VideoEditTranscript(User $user, Questionnaire $questionnaire)
    {
        return $this->QuestionnaireEdit($user, $questionnaire);
    }

    /**
     * Determine if user can do an analysis of a video.
     *
     * @return bool
     */
    public function VideoAnalysis(User $user, Questionnaire $questionnaire)
    {
        return $this->QuestionnaireView($user, $questionnaire);
    }

    /**
     * Determine if user can export an analysis of a video.
     *
     * @return bool
     */
    public function VideoAnalysisExport(User $user, Questionnaire $questionnaire)
    {
        return $this->QuestionnaireView($user, $questionnaire);
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
