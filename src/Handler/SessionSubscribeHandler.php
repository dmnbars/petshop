<?php

namespace App\Handler;

use App\DataBase;

class SessionSubscribeHandler implements HandlerInterface
{
    /**
     * @var DataBase $db
     */
    private $db;

    /**
     * @var int $sessionId
     */
    private $sessionId;

    /**
     * @var int $participantId
     */
    private $participantId;

    public function __construct(DataBase $db, $sessionId, $participantId)
    {
        $this->db = $db;
        $this->sessionId = intval($sessionId);
        $this->participantId = intval($participantId);
    }

    public function handle()
    {
        // Очень смущает этот монстр
        $this->db->insert(
            'INSERT INTO `SessionHasParticipant` (`SessionId`, `ParticipantId`) ' .
            'SELECT ?, ? FROM `Session` WHERE ' .
            '(SELECT COUNT(SessionId) FROM `SessionHasParticipant` WHERE `SessionId` = ?) + 1 < ' .
            '(SELECT `Limit` FROM `Session` WHERE `ID` = ?)',
            [
                $this->sessionId,
                $this->participantId,
                $this->sessionId,
                $this->sessionId,
            ]
        );

        $res = $this->db->select(
            'SELECT `SessionId` FROM `SessionHasParticipant` WHERE `SessionId` = ? AND `ParticipantId` = ? LIMIT 1',
            [
                $this->sessionId,
                $this->participantId
            ]
        );

        return !empty($res);
    }
}
