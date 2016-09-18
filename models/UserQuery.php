<?php

namespace yariksav\user\models;

/**
 * This is the ActiveQuery class for [[Objects]].
 *
 * @see Objects
 */
class UserQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Objects[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Objects|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function byPhrase($phrase) {
        $this->joinWith('profile');
        $this->andFilterWhere(['or',
            ['like', 'username', $phrase],
            ['like', 'email', $phrase],
            ['like', Profile::tableName().'.name', $phrase],
        ]);
        return $this;
    }
}
