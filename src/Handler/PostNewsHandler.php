<?php

namespace App\Handler;

use App\DataBase;

class PostNewsHandler implements HandlerInterface
{
    /**
     * @var DataBase $db
     */
    private $db;

    /**
     * @var int $participantId
     */
    private $participantId;

    /**
     * @var string $title
     */
    private $title;

    /**
     * @var string $message
     */
    private $message;

    public function __construct(DataBase $db, $participantId, $title, $message)
    {
        $this->db = $db;
        $this->participantId = intval($participantId);
        $this->title = $title;
        $this->message = $message;
    }

    public function handle()
    {
        $this->db->insert(
            'INSERT INTO News (`ParticipantId`, `NewsTitle`, `NewsMessage`, `LikesCounter`) VALUES (?, ?, ?, 0)',
            [
                $this->participantId,
                $this->title,
                $this->message,
            ]
        );

        return !empty($res);
    }
}
