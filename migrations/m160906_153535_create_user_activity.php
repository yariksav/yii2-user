<?php

use yii\db\Migration;

/**
 * Handles the creation for table `useractivity`.
 */
class m160906_153535_create_user_activity extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%userActivity}}', [
            'id'=>$this->primaryKey(),
            'userId'=>$this->integer()->notNull(),
            'table'=>$this->string(32)->notNull(),
            'key'=>$this->integer(),
            'action'=>$this->char(1),
            'data'=>$this->text(),
            'createdAt'=>$this->dateTime()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk_userActivity_user', '{{%userActivity}}', 'userId', '{{%user}}', 'id');

    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%userActivity}}');
    }
}
