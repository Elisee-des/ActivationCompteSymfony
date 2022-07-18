<?php

namespace App\Entity;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;

class FailedSendMessage
{

    private Envelope $envelope;

    public function __construct(Envelope $envelope)
    {
        $this->envelope = $envelope;
    }

    public function getTitre(): string
    {
        return get_class($this->envelope->getMessage());
    }

    public function getMessage():object
    {
        return $this->envelope->getMessage();
    }

    public function getId(): int
    {
        /**
         * @var TransportMessageIdStamp[] $stamp
         */
        $stamp = $this->envelope->all(TransportMessageIdStamp::class);
        return end($stamp)->getId();
    }

}