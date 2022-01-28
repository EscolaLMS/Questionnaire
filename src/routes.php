<?php

use EscolaLms\Questionnaire\Http\Controllers\QuestionAdminApiController;
use EscolaLms\Questionnaire\Http\Controllers\QuestionnaireAdminApiController;
use EscolaLms\Questionnaire\Http\Controllers\QuestionnaireApiController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/admin/questionnaire', 'middleware' => ['auth:api']], function () {
    Route::get('/', [QuestionnaireAdminApiController::class, 'list']);
    Route::get('/{id}', [QuestionnaireAdminApiController::class, 'read']);
    Route::post('/', [QuestionnaireAdminApiController::class, 'create']);
    Route::delete('/{id}', [QuestionnaireAdminApiController::class, 'delete']);
    Route::patch('/{id}', [QuestionnaireAdminApiController::class, 'update']);
});

Route::group(['prefix' => 'api/admin/question', 'middleware' => ['auth:api']], function () {
    Route::get('/', [QuestionAdminApiController::class, 'list']);
    Route::get('/{id}', [QuestionAdminApiController::class, 'read']);
    Route::post('/', [QuestionAdminApiController::class, 'create']);
    Route::delete('/{id}', [QuestioAdminApiController::class, 'delete']);
    Route::patch('/{id}', [QuestionAdminApiController::class, 'update']);
});


Route::group(['prefix' => 'api/questionnaire'], function () {
    Route::get('/', [QuestionnaireApiController::class, 'list']);
    Route::get('/{model}/{model_id}', [QuestionnaireApiController::class, 'list']);
    Route::get('/{id}', [QuestionnaireApiController::class, 'read']);
    Route::post('/{id}', [QuestionnaireApiController::class, 'answer']);
});
