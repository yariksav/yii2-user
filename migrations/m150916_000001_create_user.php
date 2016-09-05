<?php

use yii\db\Migration;
use yii\db\Expression;

class m150916_000001_create_user extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(255)->unique()->notNull(),
            'email' => $this->string(255)->unique()->notNull(),
            'authKey' => $this->string(32),
            'passwordHash' => $this->string(60)->notNull(),
            'createdAt' => $this->dateTime(),
            'updatedAt' => $this->dateTime(),
            'confirmedAt' => $this->dateTime(),
            'blockedAt' => $this->dateTime(),
            'registrationIp' => $this->string(45),
            'unconfirmedEmail' => $this->string(255),
//            'type' => $this->integer()->comment('Type of user'),
            'active' => $this->boolean()->notNull()->defaultValue(1)
        ], $tableOptions);

        $this->insert('{{%user}}', [
            'username' => 'admin',
            'email' => 'admin@site.com',
            'passwordHash' => '',
            'createdAt' => new Expression('NOW()'),
            'updatedAt' => new Expression('NOW()'),
            'confirmedAt' => new Expression('NOW()')
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }

}
