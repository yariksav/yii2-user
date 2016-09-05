<?php

use yii\db\Migration;
use yii\db\Expression;

class m150916_000003_create_socialaccount extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%socialAccount}}', [
            'id' => $this->primaryKey(),
            'userId' => $this->integer()->notNull(),
            'provider' => $this->string(255)->notNull(),
            'uid' => $this->string(255)->notNull(),
            'data' => $this->text(),
            'createdAt' => $this->dateTime()->notNull(),
            'email' => $this->string(255),
            'username' => $this->string(255)
        ], $tableOptions);

        $this->addForeignKey('fk_socialAccount_user', '{{%socialAccount}}', 'userId', '{{%user}}', 'id');


    }

    public function down()
    {
        $this->dropTable('{{%socialAccount}}');
    }

}
