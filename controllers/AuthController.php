<?php

namespace app\controllers;

use app\interfaces\AuthServiceInterface;
use app\models\LoginForm;
use app\models\SignupForm;
use Yii;
use yii\rest\Controller;

class AuthController extends Controller
{
  private AuthServiceInterface $authService;

  public function __construct($id, $module, AuthServiceInterface $authService, $config = [])
  {
    $this->authService = $authService;
    parent::__construct($id, $module, $config);
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
