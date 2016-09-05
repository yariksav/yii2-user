<?php

namespace yariksav\user\models;

use Yii;

/**
 * This is the model class for table "c_userProfile".
 *
 * @property integer $userId
 * @property string $name
 * @property string $gender
 * @property string $birthDate
 * @property string $avatar
 * @property string $location
 * @property string $info
 *
 * @property User $user
 */
class Profile extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'c_userProfile';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId'], 'required'],
            [['userId'], 'integer'],
            [['birthDate'], 'safe'],
            [['info'], 'string'],
            [['name', 'avatar', 'location'], 'string', 'max' => 255],
            [['gender'], 'string', 'max' => 1],
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'userId' => Yii::t('app', 'User ID'),
            'name' => Yii::t('app', 'Name'),
            'gender' => Yii::t('app', 'Gender Male or Female'),
            'birthDate' => Yii::t('app', 'Birth Date'),
            'avatar' => Yii::t('app', 'Avatar'),
            'location' => Yii::t('app', 'Location'),
            'info' => Yii::t('app', 'Info'),
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
