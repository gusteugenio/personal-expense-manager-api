<?php

namespace app\models;

use yii\db\ActiveRecord;

class Expense extends ActiveRecord
{
  public const CATEGORIES = ['alimentação', 'transporte', 'lazer'];

  public static function tableName()
  {
    return '{{%expenses}}';
  }

  public function rules()
  {
    return [
      [['description', 'category', 'amount', 'expense_date'], 'required'],
      ['description', 'string', 'max' => 255],
      ['category', 'in', 'range' => self::CATEGORIES, 'message' => 'Categoria inválida. Use alimentação, transporte ou lazer.'],
      ['amount', 'number'],
      ['amount', 'compare', 'compareValue' => 0, 'operator' => '>', 'message' => 'O valor deve ser maior que zero.'],
      ['expense_date', 'date', 'format' => 'php:Y-m-d'],
    ];
  }

  public function beforeSave($insert)
  {
    if (!parent::beforeSave($insert)) {
      return false;
    }

    $now = date('Y-m-d H:i:s');

    if ($insert) {
      $this->created_at = $now;
    }

    $this->updated_at = $now;

    return true;
  }
}
