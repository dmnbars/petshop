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
}
