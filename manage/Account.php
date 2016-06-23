<?php

namespace yariksav\user\manage;

use yii;
use yariksav\actives\base\Exception;
use yariksav\actives\dialog\Dialog;
use yariksav\user\models\User;
use yariksav\user\helpers\Password;
use yariksav\user\models\RoleItem;

class Account extends Dialog {

    protected function _init(){
        parent::_init();

        $this->title = Yii::t('user', 'Account');
        $this->width = 600;

        $this->data = function() {
            $user = $this->isNewRecord ? new User() : User::findOne($this->id);
            if ($user) {
                $user->scenario = $this->isNewRecord ? 'create' : 'update';
            }
            return $user;
        };

        $this->actions = [
            'password' => [
                'type' => 'link',
                'visible' => function($data) {
                    return !$this->isNewRecord;
                },
                'on' => function() {
                    $this->redirect(['class'=>ChangePassword::className()]);
                }
            ],

            'delete' => [
                'type' => 'link',
                'visible' => function($data) {
                    return !$this->isNewRecord && $this->id !== Yii::$app->user->getId();
                },
                'before'=>function(){
                    if ($this->id == Yii::$app->user->getId()) {
                        throw new Exception(Yii::t('user', 'You can not delete yourself!'));
                    }
                },
                'on' => function(){
                    if ($this->confirm(Yii::t('user', 'Are you sure you want to delete this user?'))) {
                        $this->deleteInTransaction($this->model);
                        RoleItem::revokeAll($this->id);
                        $this->setAffect('user', $this->id, 'delete');
                    }
                }
            ],

            'save' => [
                'type' => 'button',
                'before' => [$this, 'prepare'],
                'after' => [$this, 'verify'],
                'on' => function(){
                    $this->saveModelInTransaction($this->model);
                    $this->id = $this->model->id;
                    $this->setAffect('user', $this->model->id, $this->isNewRecord ? 'insert' : 'update');

                },
                'icon'=>'fa fa-floppy-o'
            ],

            /*'activate' => [
                'type' => 'link',
                'visible' => function($data) {
                    return !$data->model->confirmed_at;
                },
                'on' => function($event){;
                    $this->model->confirmed_at = time();
                    $this->model->save();
                },
                'after' => [$this, 'verify']
            ],*/

        ];

        $this->controls = [
            'username'=>[
                'type' => 'text',
            ],
            'password'=>[
                'type' => 'password',
                'visible' => false,
                'save' => function($value, $model) {
                    $model->password = Password::hash($value);
                }
            ],
            'email'=> [
                'type' => 'text',
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
                'value' => function ($data) {
                    return RoleItem::getAssignmentRoles($data->id);
                },
                'save' => function ($value, $model) {
                    if ($model->isNewRecord) {
                        $GLOBALS['roles'] = $value;
                        $model->on(yii\db\ActiveRecord::EVENT_AFTER_INSERT, function ($event) {
                            RoleItem::assignUserRoles($event->sender->id, $GLOBALS['roles']);
                        });
                    } else {
                        RoleItem::assignUserRoles($model->id, $value);
                    }
                },
                'fields' => [
                    'name',
                    'description',
                    'info' => 'data',
                    'selected' => 'roleName'
                ],
            ],
            'users'=>[
                'type' => 'select',
                'label' => Yii::t('user', 'Users'),
                'collection' => function ($data) {
                    return User::find()->all();
                },
                'value' => function ($data) {
                    return $data->id;
                },
                'fields' => [
                    'id',
                    'username'
                ],
                'empty'=>true
            ],
            'status'=>[
                'type' => 'toggler',
                'label' => Yii::t('user', 'Status'),
                'value' => function ($data) {
                    return $data->blocked_at ? 'D' : 'A';
                },
                'collection' => [
                    'A' => ['text' => Yii::t('user', 'Active'), 'class' => 'success'],
                    'D' => ['text' => Yii::t('user', 'Blocked'), 'class' => 'danger'],
                ],
                'save' => function($value, $model) {
                    if ($value == 'A' && $model->blocked_at) {
                        $model->blocked_at = null;
                    } else if ($value == 'D' && !$model->blocked_at) {
                        $model->blocked_at = time();
                    }
                }
            ],
        ];
    }
}