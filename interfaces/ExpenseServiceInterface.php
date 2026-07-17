<?php

namespace app\interfaces;

use app\models\Expense;

interface ExpenseServiceInterface
{
  public function create(int $userId, array $attributes): Expense;

  public function listByUser(int $userId): array;

  public function findOwned(int $id, int $userId): Expense;

  public function update(Expense $expense, array $attributes): Expense;

  public function delete(Expense $expense): void;
}
