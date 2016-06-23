<?php

namespace yariksav\user\manage;


use yii;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yariksav\actives\view\ManagerGrid;
use yariksav\user\models\RoleItem;

class UserRoles extends ManagerGrid
{

    function init() {
        parent::init();
        $this->columns = [
            'name' => [
                'header' => Yii::t('user', 'Name'),
                'align' => 'center'
            ],
            'description' => [
                'header' => Yii::t('user', 'Description'),
                'align' => 'center'
            ],
        ];
        $this->buttons = [
            'new' => [
                'icon' => 'plus',
                'text' => Yii::t('user', 'New role'),
                'icon' => 'fa fa-plus',
                'data' => ['class' => UserRoleDialog::className()],
            ],
            'update' => [
                'icon' => 'fa fa-arrow-circle-o-down',
                'text' => Yii::t('user', 'Load for permissions update'),
                'data' => [
                    'class' => UserRoleDialog::className(),
                    'action' => 'install'
                ],
            ],
            'edit' => [
                'type' => 'row',
                'icon' => 'fa fa-pencil',
                'text' => Yii::t('user', 'Edit'),
                'data' => ['class' => UserRoleDialog::className()],
            ],
            'delete' => [
                'type' => 'row',
                'in' => 'context',
                'icon' => 'fa fa-times',
                'text' => Yii::t('user', 'Delete'),
                'data' => [
                    'class' => UserRoleDialog::className(),
                    'action' => 'delete'
                ],
                'visible' => function ($data) {
                    return $data['name'] !== 'admin';
                }
            ],
        ];

        $this->data = function () {
            $data = [];
            $roles = RoleItem::findAll();
            if ($this->searchPhrase) {
                $roles = array_filter($roles, function ($role) {
                    return strpos($role->name, $this->searchPhrase) !== false || strpos($role->description, $this->searchPhrase) !== false;
                });
            }
            foreach ($roles as $role) {
                $data[] = array_merge((array)$role, ['id' => $role->name]);
            }
            return new ArrayDataProvider([
                'allModels' => $data,
                'sort' => [
                    'attributes' => [
                        'name',
                        'description'
                    ]
                ]
            ]);
        };
    }
}
?>