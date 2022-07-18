<?php

namespace App\Repository;

use App\Entity\FailedSendMessage;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;

class FailedSendMessageRepository
{
    private ListableReceiverInterface $receiver;

    public function __construct(ListableReceiverInterface $receiver)
    {
        return $this->receiver = $receiver;
    }

    public function findAll()
    {
        // return $this->receiver->all();

        return array_map(fn (Envelope $envelope) => new FailedSendMessage($envelope),
            iterator_to_array($this->receiver->all()));
    }

    public function find(int $id):FailedSendMessage
    {
        return new FailedSendMessage($this->receiver->find($id));
    }

    public function delete(int $id):void
    {
        $this->receiver->reject($this->receiver->find($id));
    }
}
