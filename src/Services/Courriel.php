<?php

namespace App\Services;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class Courriel
{
    // Pour injecter un service dans un service, on utilise le constructeur
    public function __construct(private MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function envoi()
    {
        $email = (new Email())
            ->from('admin@eni.fr')
            ->to('moi@eni.fr')
            ->subject('New Wish : ')
            ->text('A new wish has been made !');

        $this->mailer->send($email);
    }


}