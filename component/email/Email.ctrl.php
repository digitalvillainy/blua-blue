<?php
/* Generated by neoan3-cli */

namespace Neoan3\Components;

use Neoan3\Apps\Ops;
use Neoan3\Frame\Neoan;

/**
 * Class Email
 * @package Neoan3\Components
 */
class Email extends Neoan {
    public $mailer;

    /**
     * Email constructor.
     *
     * @param $subject
     * @param $title
     * @param $content
     */
    function __construct($subject = 'Test email', $title = '', $content = ''){
        parent::__construct();
        $this->mailer = $this->newMail();
        $this->mailer->isHTML(true);
        $this->mailer->Subject = $subject;
        $this->mailer->Body = Ops::embraceFromFile('component/email/email.view.html',[
            'subject' => $subject,
            'title' => $title,
            'content' => $content,
            'base'=>base
        ]);
    }

}
