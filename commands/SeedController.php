<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class SeedController extends Controller
{
  public function actionIndex()
  {
    $this->clear();
    $userIds = $this->seedUsers();
    $this->seedExpenses($userIds);

    $this->stdout("Seed executada com sucesso.\n", Console::FG_GREEN);
  }

  private function clear()
  {
    Yii::$app->db->createCommand()->delete('expenses')->execute();
    Yii::$app->db->createCommand()->delete('users')->execute();
  }

  private function seedUsers()
  {
    $db = Yii::$app->db;
    $now = date('Y-m-d H:i:s');

    $users = [
      ['email' => 'admin@example.com', 'password' => 'Admin@12345'],
      ['email' => 'user@example.com', 'password' => 'User@12345'],
    ];

    $ids = [];

    foreach ($users as $user) {
      $db->createCommand()->insert('users', [
        'email' => $user['email'],
        'password_hash' => Yii::$app->security->generatePasswordHash($user['password']),
        'created_at' => $now,
        'updated_at' => $now,
      ])->execute();

      $ids[] = (int) $db->getLastInsertID();
    }

    return $ids;
  }

  private function seedExpenses(array $userIds)
  {
    $db = Yii::$app->db;
    $now = date('Y-m-d H:i:s');

    $descriptions = [
      'alimentação' => ['Supermercado', 'Restaurante', 'Ifood'],
      'transporte' => ['Uber', 'Combustivel', 'Estacionamento'],
      'lazer' => ['Cinema', 'Streaming', 'Show'],
    ];

    foreach ($userIds as $userId) {
      foreach ($descriptions as $category => $items) {
        foreach ($items as $i => $description) {
          $db->createCommand()->insert('expenses', [
            'user_id' => $userId,
            'description' => $description,
            'category' => $category,
            'amount' => rand(2000, 25000) / 100,
            'expense_date' => date('Y-m-d', strtotime("-{$i} months")),
            'created_at' => $now,
            'updated_at' => $now,
          ])->execute();
        }
      }
    }
  }
}
