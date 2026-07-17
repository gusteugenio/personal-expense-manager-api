<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
  'id' => 'expense-manager',
  'basePath' => dirname(__DIR__),
  'bootstrap' => ['log'],
  'container' => [
    'definitions' => [
      \app\interfaces\AuthServiceInterface::class => \app\services\AuthService::class,
      \app\interfaces\ExpenseServiceInterface::class => \app\services\ExpenseService::class,
    ],
  ],
  'components' => [
    'request' => [
      'cookieValidationKey' => getenv('COOKIE_VALIDATION_KEY'),
      'enableCsrfValidation' => false,
      'parsers' => [
        'application/json' => 'yii\web\JsonParser',
      ],
    ],
    'response' => [
      'format' => yii\web\Response::FORMAT_JSON,
    ],
    'user' => [
      'identityClass' => 'app\models\User',
      'enableSession' => false,
      'loginUrl' => null,
    ],
    'cache' => [
      'class' => 'yii\caching\FileCache',
    ],
    'jwt' => [
      'class' => 'app\components\JwtService',
      'secret' => getenv('JWT_SECRET'),
    ],
    'log' => [
      'traceLevel' => YII_DEBUG ? 3 : 0,
      'targets' => [
        [
          'class' => 'yii\log\FileTarget',
          'levels' => ['error', 'warning'],
        ],
      ],
    ],
    'db' => $db,
    'urlManager' => [
      'enablePrettyUrl' => true,
      'showScriptName' => false,
      'rules' => [
        ['class' => 'yii\rest\UrlRule', 'controller' => 'expense'],
      ],
    ],
  ],
  'params' => $params,
];

if (YII_ENV_DEV) {
  $config['bootstrap'][] = 'gii';
  $config['modules']['gii'] = [
    'class' => 'yii\gii\Module',
  ];
}

return $config;
