<?php

namespace Composer\Application\Push\Email;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

trait JobTrait
{
    public $mail;
    /**
     * $to [email] 收件人
     * $subject 标题
     * $content 内容
     * $cc [email] 抄送
     */
    public function sendMailHandle($config, $to, $subject, $body, $cc = [])
    {
        $this->init($config);
        $this->setFrom($config);
        $this->addAddress($to);
        $this->addCC($cc);
        $this->message($subject, $body);
        $this->mail->send();
    }

    private function init($config)
    {
        $this->mail = new PHPMailer(true);
        $this->mail->SMTPDebug = SMTP::DEBUG_SERVER; //Enable verbose debug output
        $this->mail->isSMTP(); //Send using SMTP
        $this->mail->Host = $config['host']; //Set the SMTP server to send through
        $this->mail->SMTPAuth = true; //Enable SMTP authentication
        $this->mail->Username = $config['username']; //SMTP username
        $this->mail->Password = $config['password']; //SMTP password
        $this->mail->SMTPSecure = $config['verify_type']; //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $this->mail->Port = $config['port'];
        $this->mail->CharSet = 'utf8';
    }

    private function setFrom($config)
    {
        if (empty($config['username_from'])) {
            $this->mail->setFrom($config['username']);
        } else {
            $this->mail->setFrom($config['username'], $config['username_from']);
        }
    }

    private function addAddress($to)
    {
        if (is_string($to)) {
            $this->mail->addAddress($to); //Add a recipient
        } elseif (is_array($to)) {
            foreach ($to as $t) {
                $this->mail->addAddress($t); //Add a recipient
            }
        }
    }

    private function addCC($cc)
    {
        if (is_string($cc)) {
            $this->mail->addCC($cc); //Add a recipient
        } elseif (is_array($cc)) {
            foreach ($cc as $t) {
                $this->mail->addCC($t); //Add a recipient
            }
        }
    }

    private function message($subject, $body)
    {
        $this->mail->isHTML(true);
        $this->mail->Subject = $subject;
        $this->mail->Body = $body;
    }
}
