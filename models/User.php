<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
  public static function tableName()
  {
    return '{{%users}}';
  }

  public function rules()
  {
    return [
      [['email', 'password_hash'], 'required'],
      ['email', 'email'],
      ['email', 'unique'],
      ['email', 'string', 'max' => 255],
    ];
  }

  public static function findIdentity($id)
  {
    return static::findOne($id);
  }

  // Resolve a identidade a partir do token JWT enviado no header Authorization
  public static function findIdentityByAccessToken($token, $type = null)
  {
    $payload = Yii::$app->jwt->decode($token);

    if (!$payload || !isset($payload->sub)) {
      return null;
    }

    return static::findOne($payload->sub);
  }

  public static function findByEmail($email)
  {
    return static::findOne(['email' => $email]);
  }

  public function getId()
  {
    return $this->id;
  }

  public function getAuthKey()
  {
    throw new NotSupportedException('authKey não é utilizado, autenticação é feita via JWT.');
  }

  public function validateAuthKey($authKey)
  {
    throw new NotSupportedException('authKey não é utilizado, autenticação é feita via JWT.');
  }

  public function setPassword($password)
  {
    $this->password_hash = Yii::$app->security->generatePasswordHash($password);
  }

  public function validatePassword($password)
  {
    return Yii::$app->security->validatePassword($password, $this->password_hash);
  }
}
