<?php

use yii\db\Migration;
use yii\db\Expression;

class m150916_000004_create_usertoken extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%userToken}}', [
            'id' => $this->primaryKey(),
            'userId' => $this->integer()->notNull(),
            'code' => $this->string(32)->notNull(),
            'createdAt' => $this->dateTime(),
            'type' => $this->integer()->notNull()
        ], $tableOptions);

        $this->addForeignKey('fk_userToken_user', '{{%userToken}}', 'userId', '{{%user}}', 'id');
    }

    public function down()
    {
        $this->dropTable('{{%userToken}}');
    }

}
