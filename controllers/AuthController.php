<?php

namespace app\controllers;

use app\models\LoginForm;
use app\models\SignupForm;
use app\services\AuthService;
use Yii;
use yii\rest\Controller;

class AuthController extends Controller
{
  private AuthService $authService;

  public function init()
  {
    parent::init();
    $this->authService = new AuthService();
  }

  public function verbs()
  {
    return [
      'signup' => ['POST'],
      'login' => ['POST'],
    ];
  }

  public function actionSignup()
  {
    $form = new SignupForm();
    $form->load(Yii::$app->request->post(), '');

    if (!$form->validate()) {
      Yii::$app->response->statusCode = 422;
      return $form->errors;
    }

    $user = $this->authService->register($form);

    Yii::$app->response->statusCode = 201;
    return [
      'id' => $user->id,
      'email' => $user->email,
    ];
  }

  public function actionLogin()
  {
    $form = new LoginForm();
    $form->load(Yii::$app->request->post(), '');

    if (!$form->validate()) {
      Yii::$app->response->statusCode = 422;
      return $form->errors;
    }

    $result = $this->authService->login($form);

    return [
      'token' => $result['token'],
      'user' => [
        'id' => $result['user']->id,
        'email' => $result['user']->email,
      ],
    ];
  }
}
