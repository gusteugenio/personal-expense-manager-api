<?php

namespace app\controllers;

use app\services\ExpenseService;
use Yii;

class ExpenseController extends SecuredController
{
  private ExpenseService $expenseService;

  public function __construct($id, $module, ExpenseService $expenseService, $config = [])
  {
    $this->expenseService = $expenseService;
    parent::__construct($id, $module, $config);
  }

  public function actionIndex()
  {
    return $this->expenseService->listByUser((int) Yii::$app->user->id);
  }

  public function actionView($id)
  {
    return $this->expenseService->findOwned((int) $id, (int) Yii::$app->user->id);
  }

  public function actionCreate()
  {
    $expense = $this->expenseService->create((int) Yii::$app->user->id, Yii::$app->request->post());

    if ($expense->hasErrors()) {
      Yii::$app->response->statusCode = 422;
      return $expense->errors;
    }

    Yii::$app->response->statusCode = 201;
    return $expense;
  }

  public function actionUpdate($id)
  {
    $expense = $this->expenseService->findOwned((int) $id, (int) Yii::$app->user->id);
    $expense = $this->expenseService->update($expense, Yii::$app->request->post());

    if ($expense->hasErrors()) {
      Yii::$app->response->statusCode = 422;
      return $expense->errors;
    }

    return $expense;
  }

  public function actionDelete($id)
  {
    $expense = $this->expenseService->findOwned((int) $id, (int) Yii::$app->user->id);
    $this->expenseService->delete($expense);

    Yii::$app->response->statusCode = 204;
  }
}
