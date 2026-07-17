<?php

namespace app\models;

class LoginForm extends \yii\base\Model
{
  public $email;
  public $password;

  private $_user;

  public function rules()
  {
    return [
      [['email', 'password'], 'required'],
      ['email', 'email'],
      ['password', 'validatePassword'],
    ];
  }

  public function validatePassword($attribute)
  {
    if (!$this->hasErrors()) {
      $user = $this->getUser();

      if (!$user || !$user->validatePassword($this->password)) {
        $this->addError($attribute, 'E-mail ou senha inválidos.');
      }
    }
  }

  public function getUser()
  {
    if ($this->_user === null) {
      $this->_user = User::findByEmail($this->email);
    }

    return $this->_user;
  }
}
