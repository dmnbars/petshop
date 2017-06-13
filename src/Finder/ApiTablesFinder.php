<?php


namespace App\Finder;

class ApiTablesFinder extends AbstractFinder
{
    /**
     * {@inheritdoc}
     */
    public function getTableName()
    {
        return 'ApiTables';
    }

    /**
     * {@inheritdoc}
     */
    public function findAllNames()
    {
        $data = $this->findAll();

        return array_column($data, 'Name');
    }
}
