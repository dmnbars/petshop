<?php


namespace App\Finder;

class NewsFinder extends AbstractFinder
{
    /**
     * {@inheritdoc}
     */
    public function getTableName()
    {
        return 'News';
    }

    /**
     * {@inheritdoc}
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->getTableName()} WHERE ID = ?";

        return $this->db->select($sql, [$id]);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        $sql = "SELECT * FROM {$this->getTableName()}";

        return $this->db->select($sql);
    }
}
