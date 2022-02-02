<?php

namespace EscolaLms\Questionnaire\Models;

use EscolaLms\Questionnaire\Database\Factories\QuestionnaireModelTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     schema="QuestionnaireModelType",
 *     required={"title","modelable_class"},
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="title"
 *     ),
 *     @OA\Property(
 *          property="modelable_class",
 *          type="string",
 *          description="modelable_class"
 *     ),
 * )
 *
 * @property integer $id
 * @property string $title
 * @property string $modelable_class
 */
class QuestionnaireModelType extends Model
{
    use HasFactory;

    public $table = 'questionnaire_model_types';
    public $timestamps = false;

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'modelable_class' => 'string',
    ];

    public $fillable = [
        'title',
        'modelable_class',
    ];

    public function questionnaireModel(): HasMany
    {
        return $this->hasMany(QuestionnaireModel::class, 'modelable_type_id');
    }

    protected static function newFactory(): QuestionnaireModelTypeFactory
    {
        return QuestionnaireModelTypeFactory::new();
    }
}
