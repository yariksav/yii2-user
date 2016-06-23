<?php

namespace yariksav\user\manage;

use yii;
use yariksav\actives\dialog\Dialog;
use yariksav\user\models\RoleItem;

class UserRoleDialog extends Dialog {

    protected function _init(){
        $this->permissions = true;
        $this->title = Yii::t('user', 'User role');
        $this->actions = [
            'delete' => [
                'type' => 'link',
                'on' => function () {
                    if ($this->id === 'admin')
                        throw new \Exception('Cannot delete admin role');
                    $model = RoleItem::find($this->id);
                    if ($model && $this->confirm(Yii::t('user', 'Really delete role {0}?', [$this->model->description]))) {
                        $model->delete();
                        $this->waitIfNeed();
                    }
                }
            ],
            'save' => [
                'type' => 'button',
                'before' => [$this, 'prepare'],
                'after' => [$this, 'verify'],
                'on' => function () {
                    $this->model->save();
                    $this->model->setPermissions($this->inputs['permissions']);
                    $this->waitIfNeed();
                }
            ],
            'install' => [
                'on' => function () {
                    if ($this->confirm(Yii::t('user', 'Run for search and insert new permissions?'))) {
                        $res = (new RolePermission)->install();
                        $this->waitIfNeed();
                        $this->message(Yii::t('user', 'Processed {count} permissions, inserted new {inserted}', $res));
                    }
                }
            ]
        ];

        $this->controls = [
            'name' => [
                'type' => $this->isNewRecord ? 'text' : 'label',
            ],
            'description' => [
                'type' => 'text',
            ],
            'permissions' => [
                'label'=> Yii::t('user', 'Permissions'),
                'type' => 'checklist',
                'name' => '',
                'collection' => function($data) {
                    $collection = array_values($data->getAllPermissions());
                    if ($collection) {
                        foreach ($collection as $item) {
                            $item->description = Yii::t('roles', $item->description);
                            if (isset($item->data['group'])) {
                                $item->data['group'] = Yii::t('roles',  $item->data['group']);
                            }
                        }
                    }
                    return $collection;
                },
                'value' => function($data) {
                    return array_values($data->getPermissions());
                },
                'fields'=>[
                    'name',
                    'description',
                    'data.group',
                    'selected'=>'name'
                ],
            ],
        ];

        $this->data = function(){
            return $this->isNewRecord ? new RoleItem() : RoleItem::find($this->id);
        };
    }

    protected function waitIfNeed(){
        if (Yii::$app->authManager instanceof yii\rbac\PhpManager) {
            sleep(1);
        }
    }
}