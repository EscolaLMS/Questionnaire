<?php

namespace EscolaLms\Questionnaire\Models;

use EscolaLms\Questionnaire\Database\Factories\QuestionnaireModelTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="QuestionnaireModelType",
 *     required={"title","model_class"},
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="title"
 *     ),
 *     @OA\Property(
 *          property="model_class",
 *          type="string",
 *          description="model_class"
 *     ),
 * )
 *
 * @property integer $id
 * @property string $title
 * @property string $model_class
 */
class QuestionnaireModelType extends Model
{
    use HasFactory;

    public $table = 'questionnaire_model_types';
    public $timestamps = false;

    /**
     * The attributes that should be casted to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'model_class' => 'string',
    ];

    public $fillable = [
        'title',
        'model_class',
    ];

    protected static function newFactory(): QuestionnaireModelTypeFactory
    {
        return QuestionnaireModelTypeFactory::new();
    }
}
