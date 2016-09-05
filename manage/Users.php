<?php
namespace yariksav\user\manage;

use yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yariksav\actives\view\ManagerGrid;

use yariksav\user\models\User;
use yariksav\user\models\RoleItem;


class Users extends ManagerGrid {

    function beforeInit(){
        parent::beforeInit();
        $this->listens = ['user', 'profile'];

        $this->plugins = [
            'manage',
            'contextMenu',
            'columnMenu'=>[
                'buttons'=>[
                    'edit'
                ]
            ],
            'export' => [
                'collection'=> [
                    'csv'=>[
                        'file'=>'report.csv'
                    ],
                    'excel'
                ]
            ],
            'search' => [
                'apply' => function($provider, $value) {
                    $provider->query->andFilterWhere(['or',
                        ['like', 'username', $value],
                        ['like', 'email', $value],
                    ]);
                }
            ],
            'filter' => [
                'controls' => [
                    'status'=>[
                        'type'=>'select',
                        'text'=>Yii::t('app', 'Status'),
                        'collection'=>[
                            'A'=>Yii::t('app', 'Enabled'),
                            'D'=>Yii::t('app', 'Disabled')
                        ],
                        'empty'=>true,
                        'apply'=>function($provider, $value) {
                            $provider->query->andWhere(['active'=>($value === 'A' ? 1 : 0)]);
                        }
                    ],
                    'roles'=>[
                        'type' => 'checklist',
                        'text' => Yii::t('user', 'Roles'),
                        'collection' => function ($data) {
                            $collection = array_values(RoleItem::findAll());
                            if ($collection) {
                                foreach ($collection as $item) {
                                    $item->description = Yii::t('app.roles', $item->description);
                                }
                            }
                            return $collection;
                        },
                        'fields' => [
                            'name',
                            'description'
                        ],
                        'apply'=>function($provider, $value) {
                            if ($value) {
                                $ids = [];
                                foreach ($value as $role) {
                                    $ids = array_merge($ids, RoleItem::getUserIdsByRole($role));
                                }
                                $provider->query->andWhere(['id' => $ids]);
                            }
                        }
                    ]
                ]
            ]
        ];

        $this->columns = [
            'active' => [
                'type'=>'status',
                'filter' => 'select',
                'values' => [
                    true => ['class'=>'enabled', 'title'=>Yii::t('app', 'Enabled')],
                    false => ['class'=>'disabled', 'title'=>Yii::t('app', 'Disabled')]
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
            'new' => [
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
            'delete'=>[
                'type'=>'row',
                'in'=>'context',
                'icon'=>'fa fa-user-times',
                'text'=>Yii::t('user', 'Delete'),
                'data'=>[
                    'class'=>UserDialog::className(),
                    'action'=>'delete'
                ]
            ],
        ];

        $this->data = User::find()->with(['profile']);

    }
}
?>