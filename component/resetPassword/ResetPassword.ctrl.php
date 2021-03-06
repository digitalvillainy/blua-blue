<?php
/* Generated by neoan3-cli */

namespace Neoan3\Components;

use Neoan3\Apps\Ops;
use Neoan3\Apps\Db;
use Neoan3\Core\RouteException;
use Neoan3\Frame\Neoan;
use Neoan3\Model\IndexModel;
use Neoan3\Model\UserModel;
use PHPMailer\PHPMailer\Exception;
use Neoan3\Core\Unicore;

class ResetPassword extends Neoan
{
    private $valid;
    private $userId;

    function init()
    {
        $uni = new Unicore();
        $uni->uni('neoan')
            ->callback($this, 'validateHash')
            ->hook('main', 'resetPassword', ['valid' => $this->valid,'rand'=>Ops::randomString(5)])
            ->output();
    }

    /**
     * @param Neoan $uni
     *
     * @throws \Neoan3\Apps\DbException
     */
    function validateHash($uni)
    {

        $this->valid = 0;
        if (sub(1)) {
            $user = Db::easy('user_password.user_id', ['confirm_code' => sub(1), '^delete_date', '^confirm_date']);
            if (!empty($user)) {
                $this->valid = 1;
                $this->userId = $user[0]['user_id'];
            }
        }
        $uni->vueComponent('mixins');
        $uni->vueComponent('login');
        $uni->vueComponent('resetPassword',['hash'=>sub(1),'userId'=>$this->userId]);

    }

    /**
     * @param array $body
     *
     * @throws Exception
     * @throws RouteException
     * @throws \Neoan3\Apps\DbException
     */
    function postResetPassword(array $body)
    {
        $user = IndexModel::first(UserModel::find(['userName'=>$body['userName']]));

        if (!empty($user)) {
            $hash = Ops::randomString(36);
            // insert into password
            Db::user_password(['user_id' => '$' . $user['id'], 'confirm_code' => $hash]);

            $mailBody =
                Ops::embraceFromFile('component/resetPassword/mailContent.html', ['base' => base, 'hash' => $hash]);
            $mail = new Email('Password reset request', 'You requested a new password', $mailBody);
            $mail->mailer->addAddress($user['emails'][0]['email']);
            $mail->mailer->send();

        }
        throw new RouteException('If possible, it worked', 405);
    }

}
