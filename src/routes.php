<?php

use EscolaLms\Questionnaire\Http\Controllers\QuestionAdminApiController;
use EscolaLms\Questionnaire\Http\Controllers\QuestionnaireAdminApiController;
use EscolaLms\Questionnaire\Http\Controllers\QuestionnaireApiController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/admin', 'middleware' => ['auth:api']], function () {
    Route::group(['prefix' => 'questionnaire'], function () {
        Route::get('/', [QuestionnaireAdminApiController::class, 'list']);
        Route::get('/{id}', [QuestionnaireAdminApiController::class, 'read']);
        Route::post('/', [QuestionnaireAdminApiController::class, 'create']);
        Route::delete('/{id}', [QuestionnaireAdminApiController::class, 'delete']);
        Route::patch('/{id}', [QuestionnaireAdminApiController::class, 'update']);
        Route::group(['prefix' => 'report'], function () {
            Route::get('/{id}', [QuestionnaireAdminApiController::class, 'report']);
            Route::get('/{id}/{model_type_id}', [QuestionnaireAdminApiController::class, 'report']);
            Route::get('/{id}/{model_type_id}/{model_id}', [QuestionnaireAdminApiController::class, 'report']);
            Route::get('/{id}/{model_type_id}/{model_id}/{user_id}', [QuestionnaireAdminApiController::class, 'report']);
        });
    });
    Route::get('/questionnaire-models', [QuestionnaireAdminApiController::class, 'getModelsType']);

    Route::group(['prefix' => 'question'], function () {
        Route::get('/', [QuestionAdminApiController::class, 'list']);
        Route::get('/{id}', [QuestionAdminApiController::class, 'read']);
        Route::post('/', [QuestionAdminApiController::class, 'create']);
        Route::delete('/{id}', [QuestionAdminApiController::class, 'delete']);
        Route::patch('/{id}', [QuestionAdminApiController::class, 'update']);
    });
});

Route::group(['prefix' => 'api/questionnaire'], function () {
    Route::get('/{model_type_title}/{model_id}', [QuestionnaireApiController::class, 'list']);
    Route::get('/{model_type_title}/{model_id}/{id}', [QuestionnaireApiController::class, 'read']);
    Route::post('/{model_type_title}/{model_id}/{id}', [QuestionnaireApiController::class, 'answer']);
    Route::group(['prefix' => 'report'], function () {
        Route::get('/{id}', [QuestionnaireApiController::class, 'report']);
        Route::get('/{id}/{model_type_id}', [QuestionnaireApiController::class, 'report']);
        Route::get('/{id}/{model_type_id}/{model_id}', [QuestionnaireApiController::class, 'report']);
    });
});
