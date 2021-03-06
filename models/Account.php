<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace yariksav\user\models;


use yariksav\user\clients\ClientInterface;
//use dektrium\user\Finder;
//use dektrium\user\models\query\AccountQuery;
//use dektrium\user\traits\ModuleTrait;
use yariksav\user\traits\ModuleTrait;
use yii\authclient\ClientInterface as BaseClientInterface;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * @property integer $id          Id
 * @property integer $userId      User id, null if account is not bind to user
 * @property string  $provider    Name of service
 * @property string  $uid         Account id
 * @property string  $data        Account properties returned by social network (json encoded)
 * @property string  $decodedData Json-decoded properties
 * @property string  $code
 * @property integer $createdAt
 * @property string  $email
 * @property string  $username
 *
 * @property User    $user        User that this account is connected for.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Account extends ActiveRecord
{
    use ModuleTrait;

    /** @var */
    private $_data;

    /** @inheritdoc */
    public static function tableName()
    {
        return '{{%socialAccount}}';
    }

    /**
     * @return User
     */
    public function getUser() {
        return $this->hasOne($this->module->modelMap['User'], ['id' => 'userId']);
    }

    /**
     * @return bool Whether this social account is connected to user.
     */
    public function getIsConnected() {
        return $this->userId != null;
    }

    public function connect(User $user)
    {
        return $this->updateAttributes([
            'username' => null,
            'email'    => null,
            'code'     => null,
            'userId'  => $user->id,
        ]);
    }

    /**
     * @return AccountQuery
     */
    public static function findOrCreate(BaseClientInterface $client) {
        $account = self::findOne([
            'provider'=>$client->id,
            'uid'=>$client->getUserAttributes()['id']
        ]);
        if (!$account) {
            $account = self::create($client);
        }
        if (!$account->user) {
            $user = User::findOrCreate($client);
            $account->userId = $user->id;
            $account->save();
        }

        return $account;
    }

    public static function create(BaseClientInterface $client) {
        /** @var Account $account */
        $account = \Yii::createObject([
            'class'      => static::className(),
            'provider'   => $client->getId(),
            'uid'  => $client->getUserAttributes()['id'],
            'data'       => Json::encode($client->getUserAttributes()),
        ]);

        if ($client instanceof ClientInterface) {
            $account->setAttributes([
                'username' => $client->getUsername(),
                'email'    => $client->getEmail(),
            ], false);
        }

        if (($user = static::fetchUser($account)) instanceof User) {
            $account->userId = $user->id;
        }

        $account->save(false);
        return $account;
    }

    /**
     * Tries to find an account and then connect that account with current user.
     *
     * @param BaseClientInterface $client
     */
    public static function connectWithUser(BaseClientInterface $client)
    {
        if (\Yii::$app->user->isGuest) {
            \Yii::$app->session->setFlash('danger', \Yii::t('user', 'Something went wrong'));

            return;
        }

        $account = static::fetchAccount($client);

        if ($account->user === null) {
            $account->link('user', \Yii::$app->user->identity);
            \Yii::$app->session->setFlash('success', \Yii::t('user', 'Your account has been connected'));
        } else {
            \Yii::$app->session->setFlash('danger', \Yii::t('user', 'This account has already been connected to another user'));
        }
    }

    /**
     * Tries to find account, otherwise creates new account.
     *
     * @param BaseClientInterface $client
     *
     * @return Account
     * @throws \yii\base\InvalidConfigException
     */
//    protected static function fetchAccount(BaseClientInterface $client)
//    {
//        $account = static::getFinder()->findAccount()->byClient($client)->one();
//
//        if (null === $account) {
//            $account = \Yii::createObject([
//                'class'      => static::className(),
//                'provider'   => $client->getId(),
//                'uid'  => $client->getUserAttributes()['id'],
//                'data'       => Json::encode($client->getUserAttributes()),
//            ]);
//            $account->save(false);
//        }
//
//        return $account;
//    }

    /**
     * Tries to find user or create a new one.
     *
     * @param Account $account
     *
     * @return User|bool False when can't create user.
     */
    protected static function fetchUser(Account $account)
    {
        //$user = static::getFinder()->findUserByEmail($account->email);
        $user = User::find(['email'=>$account->email])->one();
        if (null !== $user) {
            return $user;
        }

        $user = \Yii::createObject([
            'class'    => User::className(),
            'scenario' => 'connect',
            'username' => $account->username,
            'email'    => $account->email,
        ]);

        if (!$user->validate(['email'])) {
            $account->email = null;
        }

        if (!$user->validate(['username'])) {
            $account->username = null;
        }

        return $user->create() ? $user : false;
    }

    /**
     * @return Finder
     */
//    protected static function getFinder()
//    {
//        if (static::$finder === null) {
//            static::$finder = \Yii::$container->get(Finder::className());
//        }
//
//        return static::$finder;
//    }
}
