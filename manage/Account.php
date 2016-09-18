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
            $user = $this->isNewRecord ? new User() : User::findOne($this->key);
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
                    return !$this->isNewRecord && $this->key !== Yii::$app->user->getId();
                },
                'before'=>function(){
                    if ($this->key == Yii::$app->user->getId()) {
                        throw new Exception(Yii::t('user', 'You can not delete yourself!'));
                    }
                },
                'on' => function(){
                    if ($this->confirm(Yii::t('user', 'Are you sure you want to delete this user?'))) {
                        $this->deleteInTransaction($this->model);
                        RoleItem::revokeAll($this->key);
                        $this->emit('user');
                    }
                }
            ],

            'save' => [
                'type' => 'button',
                'before' => [$this, 'prepare'],
                'after' => [$this, 'verify'],
                'on' => function(){
                    $this->saveModelInTransaction($this->model);
                    $this->key = $this->model->id;
                    $this->emit('user');

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
                'save' => function($model, $value) {
                    $model->password = Password::hash($value);
                }
            ],
            'email'=> [
                'type' => 'text',
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
                    'description',
                    'info' => 'data',
                ],
            ],
            'status'=>[
                'type' => 'toggler',
                'text' => Yii::t('user', 'Status'),
                'collection' => [
                    true => ['text' => Yii::t('user', 'Active'), 'class' => 'success'],
                    false => ['text' => Yii::t('user', 'Blocked'), 'class' => 'danger'],
                ],
                'save' => function($value, $model) {
                    if ($value == 'A' && $model->blockedAt) {
                        $model->blockedAt = null;
                    } else if ($value == 'D' && !$model->blockedAt) {
                        $model->blockedAt = time();
                        $model->active = 0;
                    }
                }
            ],
        ];
    }
}