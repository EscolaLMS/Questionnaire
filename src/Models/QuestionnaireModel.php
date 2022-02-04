<?php

namespace EscolaLms\Questionnaire\Models;

use EscolaLms\Questionnaire\Database\Factories\QuestionnaireModelFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     schema="QuestionnaireModel",
 *     required={"questionnaire_id","modelable_type_id","modelable_id"},
 *     @OA\Property(
 *         property="questionnaire_id",
 *         type="integer",
 *         description="identifier of the questionnaire object"
 *     ),
 *     @OA\Property(
 *         property="modelable_id",
 *         type="integer",
 *         description="identifier of the model object who is asigne to questionnaire"
 *     ),
 *     @OA\Property(
 *          property="modelable_type_id",
 *          type="integer",
 *          description="modelable_type_id"
 *     ),
 * )
 *
 * @property integer $id
 * @property integer $questionnaire_id
 * @property integer $modelable_type_id
 * @property integer $modelable_id
 */
class QuestionnaireModel extends Model
{
    use HasFactory;

    public $table = 'questionnaire_models';
    public $timestamps = false;

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'questionnaire_id' => 'integer',
        'modelable_type_id' => 'integer',
        'modelable_id' => 'integer',
    ];

    public $fillable = [
        'questionnaire_id',
        'modelable_type_id',
        'modelable_id',
    ];

    public function questionnaire(): BelongsTo
    {
        return $this->belongsTo(Questionnaire::class, 'questionnaire_id');
    }

    public function modelableType(): BelongsTo
    {
        return $this->belongsTo(QuestionnaireModelType::class, 'modelable_type_id');
    }

    public function questionAnswer(): HasMany
    {
        return $this->hasMany(QuestionAnswer::class, 'questionnaire_model_id');
    }

    protected static function newFactory(): QuestionnaireModelFactory
    {
        return QuestionnaireModelFactory::new();
    }
}
