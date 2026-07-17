<?php

use Tests\Support\FunctionalTester;

class ExpenseCest
{
  private $adminToken;
  private $userToken;
  private $adminExpenseId;

  public function _before(FunctionalTester $I)
  {
    $I->haveHttpHeader('Content-Type', 'application/json');

    $I->sendPost('/auth/login', ['email' => 'admin@example.com', 'password' => 'Admin@12345']);
    $this->adminToken = $I->grabDataFromResponseByJsonPath('$.token')[0];

    $I->sendPost('/auth/login', ['email' => 'user@example.com', 'password' => 'User@12345']);
    $this->userToken = $I->grabDataFromResponseByJsonPath('$.token')[0];
  }

  private function asAdmin(FunctionalTester $I)
  {
    $I->haveHttpHeader('Content-Type', 'application/json');
    $I->haveHttpHeader('Authorization', 'Bearer ' . $this->adminToken);
  }

  private function asUser(FunctionalTester $I)
  {
    $I->haveHttpHeader('Content-Type', 'application/json');
    $I->haveHttpHeader('Authorization', 'Bearer ' . $this->userToken);
  }

  public function createExpenseWithValidData(FunctionalTester $I)
  {
    $this->asAdmin($I);
    $I->sendPost('/expenses', [
      'description' => 'Teste de criacao',
      'category' => 'alimentação',
      'amount' => 50.5,
      'expense_date' => '2026-07-01',
    ]);
    $I->seeResponseCodeIs(201);
    $this->adminExpenseId = $I->grabDataFromResponseByJsonPath('$.id')[0];
  }

  public function createExpenseMissingDescription(FunctionalTester $I)
  {
    $this->asAdmin($I);
    $I->sendPost('/expenses', ['category' => 'alimentação', 'amount' => 50.5, 'expense_date' => '2026-07-01']);
    $I->seeResponseCodeIs(422);
  }

  public function createExpenseMissingCategory(FunctionalTester $I)
  {
    $this->asAdmin($I);
    $I->sendPost('/expenses', ['description' => 'Sem categoria', 'amount' => 50.5, 'expense_date' => '2026-07-01']);
    $I->seeResponseCodeIs(422);
  }

  public function createExpenseInvalidCategory(FunctionalTester $I)
  {
    $this->asAdmin($I);
    $I->sendPost('/expenses', ['description' => 'Categoria invalida', 'category' => 'viagem', 'amount' => 50.5, 'expense_date' => '2026-07-01']);
    $I->seeResponseCodeIs(422);
  }

  public function createExpenseMissingAmount(FunctionalTester $I)
  {
    $this->asAdmin($I);
    $I->sendPost('/expenses', ['description' => 'Sem valor', 'category' => 'lazer', 'expense_date' => '2026-07-01']);
    $I->seeResponseCodeIs(422);
  }

  public function createExpenseAmountZeroOrNegative(FunctionalTester $I)
  {
    $this->asAdmin($I);
    $I->sendPost('/expenses', ['description' => 'Valor invalido', 'category' => 'lazer', 'amount' => 0, 'expense_date' => '2026-07-01']);
    $I->seeResponseCodeIs(422);
  }

  public function createExpenseMissingDate(FunctionalTester $I)
  {
    $this->asAdmin($I);
    $I->sendPost('/expenses', ['description' => 'Sem data', 'category' => 'transporte', 'amount' => 20]);
    $I->seeResponseCodeIs(422);
  }

  public function createExpenseInvalidDateFormat(FunctionalTester $I)
  {
    $this->asAdmin($I);
    $I->sendPost('/expenses', ['description' => 'Data invalida', 'category' => 'transporte', 'amount' => 20, 'expense_date' => '01/07/2026']);
    $I->seeResponseCodeIs(422);
  }

  public function createExpenseWithoutToken(FunctionalTester $I)
  {
    $I->haveHttpHeader('Content-Type', 'application/json');
    $I->sendPost('/expenses', ['description' => 'Sem token', 'category' => 'lazer', 'amount' => 20, 'expense_date' => '2026-07-01']);
    $I->seeResponseCodeIs(401);
  }

  public function listExpensesReturnsOwnedItemsWithPagination(FunctionalTester $I)
  {
    $this->asAdmin($I);
    $I->sendGet('/expenses');
    $I->seeResponseCodeIs(200);
    $I->seeResponseJsonMatchesJsonPath('$.items');
    $I->seeResponseJsonMatchesJsonPath('$.pagination.total');
  }

  public function listExpensesFilteredByCategory(FunctionalTester $I)
  {
    $this->asAdmin($I);
    $I->sendGet('/expenses', ['category' => 'lazer']);
    $I->seeResponseCodeIs(200);
  }

  public function listExpensesInvalidCategory(FunctionalTester $I)
  {
    $this->asAdmin($I);
    $I->sendGet('/expenses', ['category' => 'viagem']);
    $I->seeResponseCodeIs(422);
  }

  public function listExpensesFilteredByPeriod(FunctionalTester $I)
  {
    $this->asAdmin($I);
    $I->sendGet('/expenses', ['month' => 7, 'year' => 2026]);
    $I->seeResponseCodeIs(200);
  }

  public function listExpensesWithOnlyMonthFails(FunctionalTester $I)
  {
    $this->asAdmin($I);
    $I->sendGet('/expenses', ['month' => 7]);
    $I->seeResponseCodeIs(422);
  }

  public function listExpensesWithOnlyYearFails(FunctionalTester $I)
  {
    $this->asAdmin($I);
    $I->sendGet('/expenses', ['year' => 2026]);
    $I->seeResponseCodeIs(422);
  }

  public function listExpensesSortedAscending(FunctionalTester $I)
  {
    $this->asAdmin($I);
    $I->sendGet('/expenses', ['sort' => 'asc']);
    $I->seeResponseCodeIs(200);
  }

  public function listExpensesRespectsPagination(FunctionalTester $I)
  {
    $this->asAdmin($I);
    $I->sendGet('/expenses', ['per_page' => 2, 'page' => 1]);
    $I->seeResponseCodeIs(200);
  }

  public function listExpensesWithoutToken(FunctionalTester $I)
  {
    $I->sendGet('/expenses');
    $I->seeResponseCodeIs(401);
  }

  public function viewOwnExpense(FunctionalTester $I)
  {
    $this->createExpenseWithValidData($I);
    $this->asAdmin($I);
    $I->sendGet('/expenses/' . $this->adminExpenseId);
    $I->seeResponseCodeIs(200);
  }

  public function viewOtherUsersExpenseIsForbidden(FunctionalTester $I)
  {
    $this->createExpenseWithValidData($I);
    $this->asUser($I);
    $I->sendGet('/expenses/' . $this->adminExpenseId);
    $I->seeResponseCodeIs(403);
  }

  public function viewNonExistentExpense(FunctionalTester $I)
  {
    $this->asAdmin($I);
    $I->sendGet('/expenses/999999');
    $I->seeResponseCodeIs(404);
  }

  public function updateOwnExpense(FunctionalTester $I)
  {
    $this->createExpenseWithValidData($I);
    $this->asAdmin($I);
    $I->sendPut('/expenses/' . $this->adminExpenseId, ['description' => 'Atualizada']);
    $I->seeResponseCodeIs(200);
  }

  public function updateExpenseWithInvalidData(FunctionalTester $I)
  {
    $this->createExpenseWithValidData($I);
    $this->asAdmin($I);
    $I->sendPut('/expenses/' . $this->adminExpenseId, ['category' => 'viagem']);
    $I->seeResponseCodeIs(422);
  }

  public function updateOtherUsersExpenseIsForbidden(FunctionalTester $I)
  {
    $this->createExpenseWithValidData($I);
    $this->asUser($I);
    $I->sendPut('/expenses/' . $this->adminExpenseId, ['description' => 'Tentativa indevida']);
    $I->seeResponseCodeIs(403);
  }

  public function deleteOwnExpense(FunctionalTester $I)
  {
    $this->createExpenseWithValidData($I);
    $this->asAdmin($I);
    $I->sendDelete('/expenses/' . $this->adminExpenseId);
    $I->seeResponseCodeIs(204);
  }

  public function deleteOtherUsersExpenseIsForbidden(FunctionalTester $I)
  {
    $this->createExpenseWithValidData($I);
    $this->asUser($I);
    $I->sendDelete('/expenses/' . $this->adminExpenseId);
    $I->seeResponseCodeIs(403);
  }

  public function deleteNonExistentExpense(FunctionalTester $I)
  {
    $this->asAdmin($I);
    $I->sendDelete('/expenses/999999');
    $I->seeResponseCodeIs(404);
  }
}
