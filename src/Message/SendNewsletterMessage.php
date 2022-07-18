<?php

namespace App\Message;

final class SendNewsletterMessage
{
    private $userId;
    private $newsId;

    public function __construct($userId, $newsId)
    {
        $this->userId = $userId;
        $this->newsId = $newsId;
    }

    /**
     * Get the value of userId
     */ 
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Get the value of newsId
     */ 
    public function getNewsId()
    {
        return $this->newsId;
    }
}
