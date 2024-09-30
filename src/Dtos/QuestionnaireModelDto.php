<?php

namespace EscolaLms\Questionnaire\Dtos;

use EscolaLms\Core\Dtos\Contracts\DtoContract;
use EscolaLms\Core\Dtos\Contracts\InstantiateFromRequest;
use Illuminate\Http\Request;

class QuestionnaireModelDto implements DtoContract, InstantiateFromRequest
{
    protected int $id;
    protected string $modelTypeTitle;
    protected int $modelId;
    protected ?string $targetGroup;
    protected ?int $displayFrequencyMinutes;

    public function __construct(
        int $id,
        string $modelTypeTitle,
        int $modelId,
        ?string $targetGroup,
        ?int $displayFrequencyMinutes
    ) {
        $this->id = $id;
        $this->modelTypeTitle = $modelTypeTitle;
        $this->modelId = $modelId;
        $this->targetGroup = $targetGroup;
        $this->displayFrequencyMinutes = $displayFrequencyMinutes;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getModelTypeTitle(): string
    {
        return $this->modelTypeTitle;
    }

    public function getModelId(): int
    {
        return $this->modelId;
    }

    public function getTargetGroup(): ?string
    {
        return $this->targetGroup;
    }

    public function getDisplayFrequencyMinutes(): ?int
    {
        return $this->displayFrequencyMinutes;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'model_type_title' => $this->getModelTypeTitle(),
            'model_id' => $this->getModelId(),
            'target_group' => $this->getTargetGroup(),
            'display_frequency_minutes' => $this->getDisplayFrequencyMinutes(),
        ];
    }

    public static function instantiateFromRequest(Request $request): self
    {
        return new static(
            $request->route('id'),
            $request->route('model_type_title'),
            $request->route('model_id'),
            $request->route('target_group'),
            $request->input('display_frequency_minutes'),
        );
    }
}
