<?php

namespace App\Services;

use App\Entity\Newsletter\Newsletter;
use App\Entity\Newsletter\Users;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class NewsletterService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send(Users $user, Newsletter $newsletter)
    {
        $email = (new TemplatedEmail())
            ->from("Newsletter@site.fr")
            ->to($user->getEmail())
            ->subject($newsletter->getName())
            ->htmlTemplate('emails/newsletters.html.twig')
            ->context([
                'user' => $user,
                'newsletter' => $newsletter
            ]);

        $this->mailer->send($email);
    }
}
