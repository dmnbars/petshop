<?php


namespace App\Finder;

class ParticipantFinder extends AbstractFinder
{
    /**
     * {@inheritdoc}
     */
    public function getTableName()
    {
        return 'Participant';
    }

    /**
     * @param string $email
     *
     * @return array
     */
    public function findOneByEmail($email)
    {
        $sql = "SELECT * FROM {$this->getTableName()} WHERE Email = ?";

        $data = $this->db->select($sql, [$email]);

        if (!isset($data[0])) {
            return [];
        }

        return $data[0];
    }
}
