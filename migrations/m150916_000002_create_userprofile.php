<?php

use yii\db\Migration;
use yii\db\Expression;

class m150916_000002_create_userprofile extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%userProfile}}', [
            'userId' => $this->integer()->notNull(),
            'name' => $this->string(255),
            'gender' => $this->char(1)->comment('Gender Male or Female'),
            'birthDate' => $this->date(),
            'avatar' => $this->string(255),
            'location' => $this->string(255),
            'info' => $this->text()
        ], $tableOptions);

        $this->addPrimaryKey('pk_userProfile', '{{%userProfile}}', 'userId');
        $this->addForeignKey('fk_userProfile_user', '{{%userProfile}}', 'userId', '{{%user}}', 'id');

        $this->insert('{{%userProfile}}', [
            'userId' => 1,
            'name' => 'Administrator'
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%userProfile}}');
    }

}
