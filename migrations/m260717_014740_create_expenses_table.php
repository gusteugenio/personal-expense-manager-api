<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%expenses}}`.
 */
class m260717_014740_create_expenses_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%expenses}}', [
            'id' => $this->bigPrimaryKey(),
            'user_id' => $this->bigInteger()->notNull(),
            'description' => $this->string(255)->notNull(),
            'category' => $this->string(20)->notNull(),
            'amount' => $this->decimal(10, 2)->notNull(),
            'expense_date' => $this->date()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ]);

        $this->createIndex('idx_expenses_user_id', '{{%expenses}}', 'user_id');
        $this->createIndex('idx_expenses_category', '{{%expenses}}', 'category');
        $this->createIndex('idx_expenses_expense_date', '{{%expenses}}', 'expense_date');

        $this->addForeignKey(
            'fk_expenses_user_id',
            '{{%expenses}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_expenses_user_id', '{{%expenses}}');
        $this->dropTable('{{%expenses}}');
    }
}
