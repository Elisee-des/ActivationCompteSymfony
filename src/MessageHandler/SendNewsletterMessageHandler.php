<?php

namespace App\MessageHandler;

use App\Entity\Newsletter\Newsletter;
use App\Entity\Newsletter\Users;
use App\Message\SendNewsletterMessage;
use App\Services\NewsletterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendNewsletterMessageHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;
    private NewsletterService $newsletterService;

    public function __construct(EntityManagerInterface $em, NewsletterService $newsletterService)
    {
        $this->em = $em;
        $this->newsletterService = $newsletterService;
    }

    public function __invoke(SendNewsletterMessage $sendNewsletterMessage)
    {
        $user = $this->em->find(Users::class, $sendNewsletterMessage->getUserId());
        $newsletter = $this->em->find(Newsletter::class, $sendNewsletterMessage->getNewsId());

        //on veriie si on a tous les information
        if(isset($user) && isset($newsletter))
        {
            $this->newsletterService->send($user, $newsletter);
        }
    }
}
