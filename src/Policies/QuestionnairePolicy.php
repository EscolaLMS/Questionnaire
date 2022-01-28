<?php

namespace EscolaLms\Questionnaire\Policies;

use EscolaLms\Core\Models\User;
use EscolaLms\Questionnaire\Enums\QuestionnairePermissionsEnum;
use EscolaLms\Questionnaire\Models\Questionnaire;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuestionnairePolicy
{
    use HandlesAuthorization;

    public function list(User $user): bool
    {
        return $user->can(QuestionnairePermissionsEnum::QUESTIONNAIRE_LIST);
    }

    public function read(User $user): bool
    {
        return $user->can(QuestionnairePermissionsEnum::QUESTIONNAIRE_READ);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can(QuestionnairePermissionsEnum::QUESTIONNAIRE_CREATE);
    }

    /**
     * @param User $user
     * @param ?Questionnaire $questionnaire
     * @return bool
     */
    public function delete(User $user, ?Questionnaire $questionnaire = null): bool
    {
        return $user->can(QuestionnairePermissionsEnum::QUESTIONNAIRE_DELETE);
    }

    /**
     * @param User $user
     * @param ?Questionnaire $questionnaire
     * @return bool
     */
    public function update(User $user, ?Questionnaire $questionnaire = null): bool
    {
        return $user->can(QuestionnairePermissionsEnum::QUESTIONNAIRE_UPDATE);
    }
}
