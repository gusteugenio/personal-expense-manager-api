<?php

namespace app\models;

use yii\base\Model;

class SignupForm extends Model
{
  public $email;
  public $password;

  public function rules()
  {
    return [
      [['email', 'password'], 'required'],
      ['email', 'email'],
      ['email', 'unique', 'targetClass' => User::class, 'message' => 'Este e-mail já está em uso.'],
      ['password', 'string', 'min' => 10],
      ['password', 'match',
        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
        'message' => 'A senha deve conter letra maiúscula, minúscula, número e caractere especial.',
      ],
    ];
  }
}
