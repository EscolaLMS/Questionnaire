<?php

namespace EscolaLms\Questionnaire\Models;

use EscolaLms\Questionnaire\Database\Factories\QuestionnaireFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     schema="Questionnaire",
 *     required={"title"},
 *     @OA\Property(
 *          property="title",
 *          type="string",
 *          description="questionnaire title"
 *     ),
 *     @OA\Property(
 *          property="active",
 *          type="boolean",
 *          description="Questionnaire is active"
 *     ),
 *     @OA\Property(
 *          property="models",
 *          type="array",
 *          @OA\Items(ref="#/components/schemas/QuestionnaireModel")
 *     ),
 * )
 *
 * @property integer $id
 * @property string $title
 * @property boolean $active
 */
class Questionnaire extends Model
{
    use HasFactory;

    public $table = 'questionnaires';
    public $timestamps = false;

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'active' => 'boolean'
    ];

    public $fillable = [
        'title',
        'active'
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'questionnaire_id')->orderBy('position');
    }

    public function questionnaireModels(): HasMany
    {
        return $this->hasMany(QuestionnaireModel::class, 'questionnaire_id');
    }

    protected static function newFactory(): QuestionnaireFactory
    {
        return QuestionnaireFactory::new();
    }
}
