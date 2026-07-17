<?php

namespace app\controllers;

use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;

abstract class SecuredController extends Controller
{
  public function behaviors()
  {
    $behaviors = parent::behaviors();
    $behaviors['authenticator'] = [
      'class' => HttpBearerAuth::class,
    ];

    return $behaviors;
  }
}
