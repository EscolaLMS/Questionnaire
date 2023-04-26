<?php

namespace EscolaLms\Questionnaire\Database\Seeders;

use EscolaLms\Core\Enums\UserRole;
use EscolaLms\Questionnaire\Enums\QuestionnairePermissionsEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class QuestionnairePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::findOrCreate(UserRole::ADMIN, 'api');
        $tutor = Role::findOrCreate(UserRole::TUTOR, 'api');

        $permissions = [
            QuestionnairePermissionsEnum::QUESTIONNAIRE_LIST,
            QuestionnairePermissionsEnum::QUESTIONNAIRE_READ,
            QuestionnairePermissionsEnum::QUESTIONNAIRE_DELETE,
            QuestionnairePermissionsEnum::QUESTIONNAIRE_UPDATE,
            QuestionnairePermissionsEnum::QUESTIONNAIRE_CREATE,

            QuestionnairePermissionsEnum::QUESTION_LIST,
            QuestionnairePermissionsEnum::QUESTION_READ,
            QuestionnairePermissionsEnum::QUESTION_DELETE,
            QuestionnairePermissionsEnum::QUESTION_UPDATE,
            QuestionnairePermissionsEnum::QUESTION_CREATE,

            QuestionnairePermissionsEnum::QUESTION_ANSWER_VISIBILITY_CHANGE,
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'api');
        }

        $admin->givePermissionTo($permissions);
        $tutor->givePermissionTo($permissions);
    }
}
