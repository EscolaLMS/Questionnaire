<?php

namespace EscolaLms\Questionnaire\Policies;

use EscolaLms\Core\Models\User;
use EscolaLms\Questionnaire\Enums\QuestionnairePermissionsEnum;
use EscolaLms\Questionnaire\Models\QuestionAnswer;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuestionAnswerPolicy
{
    use HandlesAuthorization;

    public function changeVisibility(User $user, QuestionAnswer $answer): bool
    {
        return $user->can(QuestionnairePermissionsEnum::QUESTION_ANSWER_VISIBILITY_CHANGE);
    }
}
