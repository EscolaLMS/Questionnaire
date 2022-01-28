<?php

namespace EscolaLms\Questionnaire\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     schema="Questionnaire",
 *     required={"title","model","model_id"},
 *     @OA\Property(
 *          property="title",
 *          type="string",
 *          description="questionnaire title"
 *     ),
 *     @OA\Property(
 *         property="model_id",
 *         type="integer",
 *         description="identifier of the model object who is asigne to questionnaire"
 *     ),
 *     @OA\Property(
 *          property="model",
 *          type="string",
 *          description="Questionnaire for model"
 *     ),
 *     @OA\Property(
 *          property="active",
 *          type="boolean",
 *          description="Questionnaire is active"
 *     ),
 * )
 *
 * @property integer $id
 * @property string $model
 * @property string $title
 * @property integer $model_id
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
        'model' => 'string',
        'title' => 'string',
        'model_id' => 'integer',
        'active' => 'boolean'
    ];

    public $fillable = [
        'model',
        'title',
        'model_id',
        'active'
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'questionnaire_id');
    }
}
