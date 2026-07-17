<?php

namespace app\interfaces;

use app\models\LoginForm;
use app\models\SignupForm;
use app\models\User;

interface AuthServiceInterface
{
  public function register(SignupForm $form): User;

  public function login(LoginForm $form): array;
}
