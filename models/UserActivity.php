<?php

namespace yariksav\user\models;

use Yii;

/**
 * This is the model class for table "{{%userActivity}}".
 *
 * @property integer $id
 * @property integer $userId
 * @property string $table
 * @property integer $key
 * @property string $action
 * @property string $data
 * @property string $createdAt
 *
 * @property User $user
 */
class UserActivity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%userActivity}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId', 'table', 'createdAt'], 'required'],
            [['userId', 'key'], 'integer'],
            [['data'], 'string'],
            [['createdAt'], 'safe'],
            [['table'], 'string', 'max' => 32],
            [['action'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'userId' => Yii::t('app', 'User ID'),
            'table' => Yii::t('app', 'Table'),
            'key' => Yii::t('app', 'Key'),
            'action' => Yii::t('app', 'Action'),
            'data' => Yii::t('app', 'Data'),
            'createdAt' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }
}
