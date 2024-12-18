<?php

namespace EscolaLms\Questionnaire\Models;

use EscolaLms\Questionnaire\Database\Factories\QuestionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     schema="Question",
 *     required={"title","questionnaire_id"},
 *     @OA\Property(
 *          property="title",
 *          type="string",
 *          description="question title"
 *     ),
 *     @OA\Property(
 *         property="questionnaire_id",
 *         type="integer",
 *         description="identifier of the questionnaire object"
 *     ),
 *     @OA\Property(
 *          property="description",
 *          type="string",
 *          description="question description"
 *     ),
 *     @OA\Property(
 *         property="position",
 *         type="integer",
 *         description="question position"
 *     ),
 *     @OA\Property(
 *          property="active",
 *          type="boolean",
 *          description="question is active"
 *     ),
 *     @OA\Property(
 *          property="type",
 *          type="string",
 *          description="type: rate, text"
 *     ),
 *     @OA\Property(
 *          property="public_answers",
 *          type="boolean",
 *          description="whether answers to questions are public"
 *     ),
 *     @OA\Property(
 *         property="max_score",
 *         type="integer",
 *         description="maximum score for question"
 *     ),
 * )
 *
 * @property integer $id
 * @property string $description
 * @property string $title
 * @property integer $questionnaire_id
 * @property integer $position
 * @property boolean $active
 * @property string $type
 * @property boolean $public_answers
 * @property integer $max_score
 */
class Question extends Model
{
    use HasFactory;

    public $table = 'questions';
    public $timestamps = false;

    /**
     * The attributes that should be casted to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'description' => 'string',
        'title' => 'string',
        'questionnaire_id' => 'integer',
        'position' => 'integer',
        'active' => 'boolean',
        'type' => 'string',
        'public_answers' => 'boolean',
        'max_score' => 'integer',
    ];

    public $fillable = [
        'description',
        'title',
        'questionnaire_id',
        'position',
        'active',
        'type',
        'public_answers',
        'max_score',
    ];

    public function questionnaire(): BelongsTo
    {
        return $this->belongsTo(Questionnaire::class, 'questionnaire_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuestionAnswer::class, 'question_id');
    }

    protected static function newFactory(): QuestionFactory
    {
        return QuestionFactory::new();
    }
}
