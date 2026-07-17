<?php

namespace app\components;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use yii\base\Component;

class JwtService extends Component
{
  public $secret;
  public $algorithm = 'HS256';
  public $expire = 86400;

  public function generate(int $userId): string
  {
    $payload = [
      'sub' => $userId,
      'iat' => time(),
      'exp' => time() + $this->expire,
    ];

    return JWT::encode($payload, $this->secret, $this->algorithm);
  }

  public function decode(string $token)
  {
    try {
      return JWT::decode($token, new Key($this->secret, $this->algorithm));
    } catch (\Exception $e) {
      return null;
    }
  }
}
