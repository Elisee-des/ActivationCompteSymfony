<?php

namespace App\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Mime\Email;

class FailMessageSubscriber implements EventSubscriberInterface
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return [
            WorkerMessageFailedEvent::class => 'onMessageFailed'
        ];
    }

    public function onMessageFailed(WorkerMessageFailedEvent $event)
    {
        $error = get_class($event->getEnvelope()->getMessage());

        $trace = $event->getThrowable()->getTraceAsString();

        $email = (new Email())
        ->from('Newsalert@gmail.com')
        ->to('admin@gmail.com')
        ->subject('Echec d\'envoie')
        ->text("Un erreur est survenue: {$error} \r\n {$trace}")
        ;

        $this->mailer->send($email);
    }
}