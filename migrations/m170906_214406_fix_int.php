<?php

use yii\db\Migration;

/**
 * Class m170906_214406_fix_int.
 */
class m170906_214406_fix_int extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('wallet', 'sum', 'bigint(20) NULL');
        $this->alterColumn('wallet', 'deposit', 'bigint(20) NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
