<?php

namespace yariksav\user\manage;

use yii;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yariksav\actives\view\ManagerGrid;
use yariksav\user\models\RoleItem;

class UserRoles extends ManagerGrid
{
    function beforeInit(){
        parent::beforeInit();
        $this->listens = ['roles'];
        $this->identifier = 'name';
        $this->plugins = [
            'manage',
            'contextMenu',
            'columnMenu',
            'search' => [
                'apply' => function($provider, $value) {
                    if ($value) {
                        $value = strtolower($value);
                        $provider->allModels = array_filter($provider->allModels, function ($role) use ($value) {
                            return mb_substr_count(strtolower($role->name), $value) > 0 ||
                                mb_substr_count(strtolower($role->description), $value) > 0;
                        });
                    }
                }
            ],
        ];

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
                    return $data->name !== 'admin';
                }
            ],
        ];

        $this->data = RoleItem::findAll();
    }
}
?>