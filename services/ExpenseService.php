<?php

namespace app\services;

use app\models\Expense;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class ExpenseService
{
  public function create(int $userId, array $attributes): Expense
  {
    $expense = new Expense();
    $expense->setAttributes($attributes);
    $expense->user_id = $userId;
    $expense->save();

    return $expense;
  }

  public function listByUser(int $userId): array
  {
    return Expense::find()->where(['user_id' => $userId])->all();
  }

  public function findOwned(int $id, int $userId): Expense
  {
    $expense = Expense::findOne($id);

    if (!$expense) {
      throw new NotFoundHttpException('Despesa não encontrada.');
    }

    if ((int) $expense->user_id !== $userId) {
      throw new ForbiddenHttpException('Você não tem acesso a esta despesa.');
    }

    return $expense;
  }

  public function update(Expense $expense, array $attributes): Expense
  {
    $expense->setAttributes($attributes);
    $expense->save();

    return $expense;
  }

  public function delete(Expense $expense): void
  {
    $expense->delete();
  }
}
