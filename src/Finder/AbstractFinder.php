<?php

namespace App\Finder;

use App\DataBase;
use App\Extension\BaseExtension;

abstract class AbstractFinder implements FinderInterface
{
    /**
     * @var DataBase $db
     */
    protected $db;

    public function __construct(DataBase $db)
    {
        $this->db = $db;
    }

    public static function getFinder($type, DataBase $db)
    {
        $className = __NAMESPACE__ . '\\' . ucfirst($type) . 'Finder';
        if (class_exists($className)) {
            return new $className($db);
        }

        throw new BaseExtension('Таблица не существует');
    }
}
