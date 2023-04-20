<?php

namespace EscolaLms\Questionnaire\Models;

use EscolaLms\Core\Models\User;
use EscolaLms\Questionnaire\Database\Factories\QuestionAnswerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *     schema="QuestionAnswer",
 *     required={"rate","question_id","user_id","questionnaire_model_id"},
 *     @OA\Property(
 *          property="rate",
 *          type="integer",
 *          description="question rate"
 *     ),
 *     @OA\Property(
 *         property="question_id",
 *         type="integer",
 *         description="identifier of the question object"
 *     ),
 *     @OA\Property(
 *          property="user_id",
 *          type="integer",
 *          description="identifier of the user object"
 *     ),
 *     @OA\Property(
 *          property="questionnaire_model_id",
 *          type="integer",
 *          description="identifier of the questionnaire model object"
 *     ),
 *     @OA\Property(
 *          property="note",
 *          type="string",
 *          description="text answer"
 *     ),
 *     @OA\Property(
 *          property="visible_on_front",
 *          type="boolean",
 *          description="whether answers is visible on front"
 *     ),
 * )
 *
 * @property integer $id
 * @property integer $rate
 * @property integer $user_id
 * @property integer $question_id
 * @property integer $questionnaire_model_id
 * @property string $note
 * @property boolean $visible_on_front
 */
class QuestionAnswer extends Model
{
    use HasFactory;

    public $table = 'question_answers';
    public $timestamps = false;

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'question_id' => 'integer',
        'questionnaire_model_id' => 'integer',
        'rate' => 'integer',
        'note' => 'string',
        'visible_on_front' => 'boolean',
    ];

    public $fillable = [
        'user_id',
        'question_id',
        'questionnaire_model_id',
        'rate',
        'note',
        'visible_on_front',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function questionnaireModel(): BelongsTo
    {
        return $this->belongsTo(QuestionnaireModel::class, 'questionnaire_model_id');
    }

    protected static function newFactory(): QuestionAnswerFactory
    {
        return QuestionAnswerFactory::new();
    }
}
