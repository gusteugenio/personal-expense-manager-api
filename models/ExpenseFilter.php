<?php

namespace app\models;

use yii\base\Model;

class ExpenseFilter extends Model
{
  public $category;
  public $month;
  public $year;
  public $sort = 'desc';
  public $page = 1;
  public $per_page = 10;

  public function rules()
  {
    return [
      ['category', 'in', 'range' => Expense::CATEGORIES, 'message' => 'Categoria inválida. Use alimentação, transporte ou lazer.'],
      [['month'], 'integer', 'min' => 1, 'max' => 12],
      [['year'], 'integer', 'min' => 1900, 'max' => 2100],
      ['sort', 'in', 'range' => ['asc', 'desc']],
      ['page', 'integer', 'min' => 1],
      ['per_page', 'integer', 'min' => 1, 'max' => 100],
    ];
  }

  public function hasPeriod(): bool
  {
    return $this->month !== null && $this->year !== null;
  }

  public function periodStart(): ?string
  {
    if (!$this->hasPeriod()) {
      return null;
    }

    return sprintf('%04d-%02d-01', $this->year, $this->month);
  }

  public function periodEnd(): ?string
  {
    if (!$this->hasPeriod()) {
      return null;
    }

    $lastDay = (int) date('t', mktime(0, 0, 0, $this->month, 1, $this->year));

    return sprintf('%04d-%02d-%02d', $this->year, $this->month, $lastDay);
  }
}
