<?php

namespace EscolaLms\Questionnaire\Models;

use EscolaLms\Questionnaire\Database\Factories\QuestionnaireModelFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *     schema="QuestionnaireModel",
 *     required={"questionnaire_id","model_type_id","model_id"},
 *     @OA\Property(
 *         property="questionnaire_id",
 *         type="integer",
 *         description="identifier of the questionnaire object"
 *     ),
 *     @OA\Property(
 *         property="model_id",
 *         type="integer",
 *         description="identifier of the model object who is asigne to questionnaire"
 *     ),
 *     @OA\Property(
 *          property="model_type_id",
 *          type="integer",
 *          description="model_type_id"
 *     ),
 *     @OA\Property(
 *          property="model_title",
 *          type="integer",
 *          description="model_title"
 *     ),
 * )
 *
 * @property integer $id
 * @property integer $questionnaire_id
 * @property integer $model_type_id
 * @property integer $model_id
 */
class QuestionnaireModel extends Model
{
    use HasFactory;

    public $table = 'questionnaire_models';
    public $timestamps = false;

    /**
     * The attributes that should be casted to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'questionnaire_id' => 'integer',
        'model_type_id' => 'integer',
        'model_id' => 'integer',
    ];

    public $fillable = [
        'questionnaire_id',
        'model_type_id',
        'model_id',
    ];

    public function questionnaire(): BelongsTo
    {
        return $this->belongsTo(Questionnaire::class, 'questionnaire_id');
    }

    public function modelableType(): BelongsTo
    {
        return $this->belongsTo(QuestionnaireModelType::class, 'model_type_id');
    }

    public function foreignModel(): BelongsTo
    {
        return $this->belongsTo($this->modelableType->model_class, 'model_id');
    }

    protected static function newFactory(): QuestionnaireModelFactory
    {
        return QuestionnaireModelFactory::new();
    }
}
