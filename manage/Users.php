<?php
namespace yariksav\user\manage;

use yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yariksav\actives\view\ManagerGrid;

use yariksav\user\models\User;
use yariksav\user\models\RoleItem;


class Users extends ManagerGrid {

    function init(){
        parent::init();
        $this->affect = ['user', 'profile'];

        $this->columns = [
            'status' => [
                'type'=>'status',
                'filter' => 'select',
                'value' => function($data) {
                    return $data->blocked_at ? 'D' : 'A';
                },
                'values' => [
                    'A' => ['class'=>'enabled', 'title'=>Yii::t('app', 'Enabled')],
                    'D' => ['class'=>'disabled', 'title'=>Yii::t('app', 'Disabled')]
                ],
            ],
            'username',
            'profile.name',
            'email' => [
                'hidden'=>true,
            ],
            'roles' => [
                'header' => Yii::t('user', 'Roles'),
                'value' => function($data) {
                    $roles = RoleItem::getRolesByUser($data->id);
                    if ($roles) foreach ($roles as $item) {
                        $item->description = Yii::t('app.roles', $item->description);
                    }
                    return implode(', ', ArrayHelper::map($roles, 'name', 'description'));
                },
                'filter'=>[
                    'type'=>'checklist',
                    'data'=>function($data){
                        return ArrayHelper::map(RoleItem::findAll(), 'name', 'name');
                    }
                ]
            ],
        ];

        $this->buttons = [
            'new'=>[
                'icon'=>'fa fa-user-plus',
                'text'=>Yii::t('user', 'New user'),
                'data'=>['class'=>UserDialog::className()],
            ],
            'edit'=>[
                'type'=>'row',
                'icon'=>'fa fa-pencil',
                'text'=>Yii::t('user', 'Edit'),
                'data'=>['class'=>UserDialog::className()],
            ],
//            'password'=>[
//                'type'=>'row',
//                'in'=>'context',
//                'icon'=>'fa fa-lock',
//                'text'=>Yii::t('user', 'Change user password'),
//                'data'=>['class'=>ChangePassword::className()],
//            ],
            'delete'=>[
                'type'=>'row',
                'in'=>'context',
                'icon'=>'fa fa-user-times',
                'text'=>Yii::t('user', 'Delete'),
                'data'=>['class'=>UserDialog::className(), 'action'=>'delete']
            ],
        ];

        $this->data = function () {
            $query = User::find()
                ->with(['profile']);

            if ($this->searchPhrase)
                $query->andFilterWhere(['or',
                    ['like','username',$this->searchPhrase],
                    ['like','email',$this->searchPhrase],
                ]);

            if ($this->filter('roles')) {
                $ids = [];
                foreach ($this->filter('roles') as $role) {
                    $ids = array_merge($ids, RoleItem::getUserIdsByRole($role));
                }
                $query->andWhere(['id' => $ids]);
            }

            if ($this->filter('status') == 'A')
                $query->andWhere(['IS', 'blocked_at', null]);
            return new ActiveDataProvider(['query' => $query]);
        };

        $this->filters = [
            'status'=>[
                'type'=>'select',
                'collection'=>[
                    'A'=>Yii::t('app', 'Enabled'),
                    'D'=>Yii::t('app', 'Disabled')
                ],
                'empty'=>true
            ],
            'roles'=>[
                'type' => 'checklist',
                'label' => Yii::t('user', 'Roles'),
                'collection' => function ($data) {
                    $collection = array_values(RoleItem::findAll());
                    if ($collection) {
                        foreach ($collection as $item) {
                            $item->description = Yii::t('app.roles', $item->description);
                        }
                    }
                    return $collection;
                },
//                'save' => function ($value, $model) {
//                    if ($model->isNewRecord) {
//                        $GLOBALS['roles'] = $value;
//                        $model->on(yii\db\ActiveRecord::EVENT_AFTER_INSERT, function ($event) {
//                            RoleItem::assignUserRoles($event->sender->id, $GLOBALS['roles']);
//                        });
//                    } else {
//                        RoleItem::assignUserRoles($model->id, $value);
//                    }
//                },
                'fields' => [
                    'name',
                    'description',
                    'info' => 'data',
                    'selected' => 'roleName'
                ],
            ],
        ];
    }
}
?>