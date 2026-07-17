<?php

use Tests\Support\FunctionalTester;

class AuthCest
{
  public function signupWithValidData(FunctionalTester $I)
  {
    $I->haveHttpHeader('Content-Type', 'application/json');
    $I->sendPost('/auth/signup', [
      'email' => 'newuser' . uniqid() . '@example.com',
      'password' => 'Valid@12345',
    ]);
    $I->seeResponseCodeIs(201);
    $I->seeResponseIsJson();
  }

  public function signupMissingEmail(FunctionalTester $I)
  {
    $I->haveHttpHeader('Content-Type', 'application/json');
    $I->sendPost('/auth/signup', ['password' => 'Valid@12345']);
    $I->seeResponseCodeIs(422);
  }

  public function signupMissingPassword(FunctionalTester $I)
  {
    $I->haveHttpHeader('Content-Type', 'application/json');
    $I->sendPost('/auth/signup', ['email' => 'missingpassword@example.com']);
    $I->seeResponseCodeIs(422);
  }

  public function signupInvalidEmail(FunctionalTester $I)
  {
    $I->haveHttpHeader('Content-Type', 'application/json');
    $I->sendPost('/auth/signup', ['email' => 'not-an-email', 'password' => 'Valid@12345']);
    $I->seeResponseCodeIs(422);
  }

  public function signupPasswordTooShort(FunctionalTester $I)
  {
    $I->haveHttpHeader('Content-Type', 'application/json');
    $I->sendPost('/auth/signup', ['email' => 'shortpass@example.com', 'password' => 'Ab1@567']);
    $I->seeResponseCodeIs(422);
  }

  public function signupPasswordMissingUppercase(FunctionalTester $I)
  {
    $I->haveHttpHeader('Content-Type', 'application/json');
    $I->sendPost('/auth/signup', ['email' => 'noupper@example.com', 'password' => 'valid@12345']);
    $I->seeResponseCodeIs(422);
  }

  public function signupPasswordMissingLowercase(FunctionalTester $I)
  {
    $I->haveHttpHeader('Content-Type', 'application/json');
    $I->sendPost('/auth/signup', ['email' => 'nolower@example.com', 'password' => 'VALID@12345']);
    $I->seeResponseCodeIs(422);
  }

  public function signupPasswordMissingDigit(FunctionalTester $I)
  {
    $I->haveHttpHeader('Content-Type', 'application/json');
    $I->sendPost('/auth/signup', ['email' => 'nodigit@example.com', 'password' => 'Valid@abcde']);
    $I->seeResponseCodeIs(422);
  }

  public function signupPasswordMissingSpecialChar(FunctionalTester $I)
  {
    $I->haveHttpHeader('Content-Type', 'application/json');
    $I->sendPost('/auth/signup', ['email' => 'nospecial@example.com', 'password' => 'Valid12345']);
    $I->seeResponseCodeIs(422);
  }

  public function signupDuplicateEmail(FunctionalTester $I)
  {
    $I->haveHttpHeader('Content-Type', 'application/json');
    $I->sendPost('/auth/signup', ['email' => 'admin@example.com', 'password' => 'Valid@12345']);
    $I->seeResponseCodeIs(422);
  }

  public function loginWithValidCredentials(FunctionalTester $I)
  {
    $I->haveHttpHeader('Content-Type', 'application/json');
    $I->sendPost('/auth/login', ['email' => 'admin@example.com', 'password' => 'Admin@12345']);
    $I->seeResponseCodeIs(200);
    $I->seeResponseJsonMatchesJsonPath('$.token');
  }

  public function loginWithWrongPassword(FunctionalTester $I)
  {
    $I->haveHttpHeader('Content-Type', 'application/json');
    $I->sendPost('/auth/login', ['email' => 'admin@example.com', 'password' => 'WrongPassword@1']);
    $I->seeResponseCodeIs(422);
  }

  public function loginWithNonExistentEmail(FunctionalTester $I)
  {
    $I->haveHttpHeader('Content-Type', 'application/json');
    $I->sendPost('/auth/login', ['email' => 'doesnotexist@example.com', 'password' => 'Valid@12345']);
    $I->seeResponseCodeIs(422);
  }

  public function loginMissingFields(FunctionalTester $I)
  {
    $I->haveHttpHeader('Content-Type', 'application/json');
    $I->sendPost('/auth/login', []);
    $I->seeResponseCodeIs(422);
  }

  public function accessProtectedEndpointWithoutToken(FunctionalTester $I)
  {
    $I->sendGet('/expenses');
    $I->seeResponseCodeIs(401);
  }

  public function accessProtectedEndpointWithInvalidToken(FunctionalTester $I)
  {
    $I->haveHttpHeader('Authorization', 'Bearer invalid.token.value');
    $I->sendGet('/expenses');
    $I->seeResponseCodeIs(401);
  }
}
