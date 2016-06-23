<?php

namespace yariksav\user\models;

use yii;
use yii\base\Model;

class RoleItem extends Model{

    public $name;
    public $description;
    public $data;
    public $permissions;
    protected $role;
    protected $isNewRecord = true;


    public function __construct($config = []){
        if (!Yii::$app->authManager)
            throw new \Exception('Auth manager is not configured in config file.');
        parent::__construct($config);
    }

    public function rules(){
        return [
            [['name', 'description'], 'required'],
            [['name', 'description'], 'string', 'min'=>4, 'max'=>64],
            ['data', 'save']
        ];
    }

    public function attributeLabels(){
        return [
            'name' => Yii::t('app', 'Code'),
            'description' => Yii::t('app', 'Name'),
        ];
    }

    public function setRole($role){
        $this->isNewRecord = empty($this->role);
        if ($role) {
            $this->attributes = (array)$role;
            $this->role = $role;
        }
    }

    public function getAllPermissions(){
        return Yii::$app->authManager->getPermissions();
    }

    public function getPermissions(){
        return $this->role ? Yii::$app->authManager->getPermissionsByRole($this->name) : [];
    }

    public function setPermissions($permissions){
        if (!$this->role)
            return;
        Yii::$app->authManager->removeChildren($this->role);
        if ($permissions){
            foreach ($permissions as $item) {
                $permission = Yii::$app->authManager->getPermission($item);
                Yii::$app->authManager->addChild($this->role, $permission);
            }
        }
    }

    public function getRole(){
        return $this->role;
    }

    public static function find($name){
        $role = Yii::$app->authManager->getRole($name);
        if ($role) {
            $instance = new RoleItem();
            $instance->setRole($role);
            return $instance;
        }
    }

    public static function findAll(){
        return Yii::$app->authManager->getRoles();
    }

    public function validate($attributeNames = null, $clearErrors = true){
        parent::validate($attributeNames = null, $clearErrors = true);
        if ($this->isNewRecord) {
            $roleExist = Yii::$app->authManager->getRole($this->name);
            if ($roleExist && $roleExist->createdAt != $this->role->createdAt) {
                $this->addError('name', Yii::t('yii', '{attribute} "{value}" has already been taken.', ['attribute' => '', 'value' => $this->name]));
                return;
            }
        }
    }

    public function save(){
        if ($this->isNewRecord) {
            $this->role = Yii::$app->authManager->createRole($this->name);
            $this->role->description = $this->description;
            $this->role->data = $this->data;
            Yii::$app->authManager->add($this->role);
        }
        else{
            $this->role->description = $this->description;
            $this->role->data = $this->data;
            Yii::$app->authManager->update($this->name, $this->role);
        }
        return $this->role;
    }

    public function delete(){
        if ($this->role) {
            Yii::$app->authManager->removeChildren($this->role);
            Yii::$app->authManager->remove($this->role);
        }
    }

    public function assign($userId){
        Yii::$app->authManager->revokeAll($userId);
        Yii::$app->authManager->assign($this->role, $userId);
    }

    public static function revokeAll($userId) {
        Yii::$app->authManager->revokeAll($userId);
    }

    public static function assignUserRoles($userId, $roles){
        if ($userId) {
            Yii::$app->authManager->revokeAll($userId);
            if ($roles) foreach ($roles as $roleName) {
                $role = Yii::$app->authManager->getRole($roleName);
                if ($role)
                Yii::$app->authManager->assign($role, $userId);
            }
        }
    }

    public static function getAssignmentRoles($userId){
        return Yii::$app->authManager->getAssignments($userId);
    }

    public static function getRolesByUser($roleName) {
        return Yii::$app->authManager->getRolesByUser($roleName);
    }

    public static function getUserIdsByRole($roleName) {
        return Yii::$app->authManager->getUserIdsByRole($roleName);
    }
}
