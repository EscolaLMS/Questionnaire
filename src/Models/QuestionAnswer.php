<?php

namespace EscolaLms\Questionnaire\Models;

use EscolaLms\Core\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     schema="QuestionAnswer",
 *     required={"rate","question_id","user_id"},
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
 *     )
 * )
 *
 * @property integer $id
 * @property integer $rate
 * @property integer $user_id
 * @property integer $question_id
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
        'rate' => 'integer',
    ];

    public $fillable = [
        'user_id',
        'question_id',
        'questionnaire_id',
        'rate',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
