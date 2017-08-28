<?php

use yii\db\Migration;

/**
 * Class m170827_213256_init.
 */
class m170827_213256_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%wallet}}', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'to_user' => $this->integer()->notNull(),
            'from_user' => $this->integer()->notNull(),
            'sum' => $this->integer()->notNull()->defaultValue(0),
            'deposit' => $this->integer()->notNull()->defaultValue(0),
            'msg' => $this->string()->notNull(),
            'type' => $this->integer(2),
            'status' => $this->integer(2),
        ], $tableOptions);

        $this->addCommentOnColumn('wallet', 'created_at', 'Створено');
        $this->addCommentOnColumn('wallet', 'updated_at', 'Оновлено');
        $this->addCommentOnColumn('wallet', 'to_user', 'Одержувач');
        $this->addCommentOnColumn('wallet', 'from_user', 'Відправник');
        $this->addCommentOnColumn('wallet', 'sum', 'Сума');
        $this->addCommentOnColumn('wallet', 'deposit', 'Залишок на рахунку');
        $this->addCommentOnColumn('wallet', 'msg', 'Повідомлення');
        $this->addCommentOnColumn('wallet', 'type', 'Тип');
        $this->addCommentOnColumn('wallet', 'status', 'Статус');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%wallet}}');

        return false;
    }
}
