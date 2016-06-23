<?php
namespace yariksav\user;

use Yii;

class Module extends \yii\base\Module
{
    /** @var int The time you want the user will be remembered without asking for credentials. */
    public $rememberFor = 1209600; // two weeks

    /** @var int The time before a confirmation token becomes invalid. */
    public $confirmWithin = 86400; // 24 hours

    /** @var int The time before a recovery token becomes invalid. */
    public $recoverWithin = 21600; // 6 hours

    /** @var int Cost parameter used by the Blowfish hash algorithm. */
    public $cost = 10;
    /** @var array Mailer configuration */
    public $mailer = [];

    /** @var array Model map */
    public $modelMap = [];

    public $controllerNamespace = 'yariksav\user\controllers';

    function __construct($id, $parent = null, $config = []) {
        parent::__construct($id, $parent = null, $config = []);
    }
}
?>