<?php

namespace EscolaLms\Questionnaire\Enums;

use EscolaLms\Core\Enums\BasicEnum;

class QuestionAnswersRequiredRateEnum extends BasicEnum
{
    const RATE_REQUIRED = [
        QuestionTypeEnum::RATE,
        QuestionTypeEnum::REVIEW,
    ];
}
