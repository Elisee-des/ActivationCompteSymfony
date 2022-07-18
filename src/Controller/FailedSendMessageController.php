<?php

namespace App\Controller;

use App\Repository\FailedSendMessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class FailedSendMessageController extends AbstractController
{
    #[Route('/newsletters/failed', name: 'failed_send_message')]
    public function index(FailedSendMessageRepository $failedSendMessageRepository): Response
    {
        return $this->render('failed_send_message/index.html.twig', [
            'messages' => $failedSendMessageRepository->findAll(),
        ]);
    }

    #[Route('/newsletters/failed/resent/{id}', name: 'failed_send_message_resent')]
    public function resent(FailedSendMessageRepository $failedSendMessageRepository, $id, MessageBusInterface $messageBusInterface): Response
    {
        $message = $failedSendMessageRepository->find($id)->getMessage();

        $messageBusInterface->dispatch($message);

        $failedSendMessageRepository->delete($id);

        return $this->render('failed_send_message/index.html.twig', [
            'messages' => $failedSendMessageRepository->findAll(),
        ]);
    }

    #[Route('/newsletters/failed/delete/{id}', name: 'failed_send_message_delete')]
    public function delete($id, FailedSendMessageRepository $failedSendMessageRepository): Response
    {
        $failedSendMessageRepository->delete($id);

        return $this->redirectToRoute('failed_send_message');
    }
}
