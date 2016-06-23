<?php

namespace yariksav\user\models;

use yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class RolePermission extends Model{

    public $name;
    public $description;
    public $data;
    protected $role;
    protected $isNewRecord = true;

    public function __construct($config = []){
        if (!Yii::$app->authManager)
            throw new \Exception('Auth manager is not configured in config file.');
        parent::__construct($config);
    }

    public function install(){
        $count = 0;
        $inserted = 0;
        $admin = RoleItem::find('admin');
        if (!$admin) {
            $admin = new RoleItem(['name'=>'admin', 'description'=>'Administrator']);
            $admin->save();
        };

        $assignPermissions = [];

        $path = Yii::getAlias('@app/config/rbac/permissions.php');
        $permissions = include($path);
        if ($permissions) foreach($permissions as $name=>$description){
            $assignPermissions[] = $name;
            $count++;
            $permission = Yii::$app->authManager->getPermission($name);
            $isNew = !$permission;

            if ($isNew)
                $permission = Yii::$app->authManager->createPermission($name);

            if (is_array($description)){
                $permission->description = ArrayHelper::getValue($description, 0);
                $permission->data = ArrayHelper::getValue($description, 'data');
            }
            else
                $permission->description = $description;

            if ($isNew){
                Yii::$app->authManager->add($permission);
                $inserted++;
            }
            else {
                Yii::$app->authManager->update($name, $permission);
            }
        }
        $admin->setPermissions($assignPermissions);
        return ['count'=>$count, 'inserted'=>$inserted];
    }
}
