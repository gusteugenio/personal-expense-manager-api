<?php

namespace app\services;

use app\interfaces\ExpenseServiceInterface;
use app\models\Expense;
use app\models\ExpenseFilter;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class ExpenseService implements ExpenseServiceInterface
{
  public function create(int $userId, array $attributes): Expense
  {
    $expense = new Expense();
    $expense->setAttributes($attributes);
    $expense->user_id = $userId;
    $expense->save();

    return $expense;
  }

  public function listByUser(int $userId, ExpenseFilter $filter): ActiveDataProvider
  {
    $query = Expense::find()->where(['user_id' => $userId]);

    if ($filter->category) {
      $query->andWhere(['category' => $filter->category]);
    }

    if ($filter->hasPeriod()) {
      $query->andWhere(['between', 'expense_date', $filter->periodStart(), $filter->periodEnd()]);
    }

    $query->orderBy(['expense_date' => $filter->sort === 'asc' ? SORT_ASC : SORT_DESC]);

    return new ActiveDataProvider([
      'query' => $query,
      'sort' => false,
      'pagination' => [
        'page' => $filter->page - 1,
        'pageSize' => $filter->per_page,
      ],
    ]);
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
