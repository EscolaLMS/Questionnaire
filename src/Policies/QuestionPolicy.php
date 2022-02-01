<?php

namespace EscolaLms\Questionnaire\Policies;

use EscolaLms\Core\Models\User;
use EscolaLms\Questionnaire\Enums\QuestionnairePermissionsEnum;
use EscolaLms\Questionnaire\Models\Question;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuestionPolicy
{
    use HandlesAuthorization;

    public function list(User $user): bool
    {
        return $user->can(QuestionnairePermissionsEnum::QUESTION_LIST);
    }

    public function read(User $user): bool
    {
        return $user->can(QuestionnairePermissionsEnum::QUESTION_READ);
    }

    public function create(User $user): bool
    {
        return $user->can(QuestionnairePermissionsEnum::QUESTION_CREATE);
    }

    public function delete(User $user, ?Question $question = null): bool
    {
        return $user->can(QuestionnairePermissionsEnum::QUESTION_DELETE);
    }

    public function update(User $user, ?Question $question = null): bool
    {
        return $user->can(QuestionnairePermissionsEnum::QUESTION_UPDATE);
    }
}
