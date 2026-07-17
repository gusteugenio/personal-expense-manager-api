<?php

namespace app\services;

use app\models\LoginForm;
use app\models\SignupForm;
use app\models\User;
use Yii;

class AuthService
{
  public function register(SignupForm $form): User
  {
    $user = new User();
    $user->email = $form->email;
    $user->setPassword($form->password);
    $user->created_at = date('Y-m-d H:i:s');
    $user->updated_at = date('Y-m-d H:i:s');
    $user->save(false);

    return $user;
  }

  public function login(LoginForm $form): array
  {
    $user = $form->getUser();
    $token = Yii::$app->jwt->generate($user->id);

    return [
      'user' => $user,
      'token' => $token,
    ];
  }
}
