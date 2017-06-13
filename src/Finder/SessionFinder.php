<?php


namespace App\Finder;

class SessionFinder extends AbstractFinder
{
    /**
     * {@inheritdoc}
     */
    public function getTableName()
    {
        return 'Session';
    }
}
