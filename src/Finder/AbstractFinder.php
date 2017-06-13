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

    /**
     * @param $type
     * @param DataBase $db
     *
     * @return self
     *
     * @throws BaseExtension
     */
    public static function getFinder($type, DataBase $db)
    {
        $className = __NAMESPACE__ . '\\' . ucfirst($type) . 'Finder';
        if (class_exists($className)) {
            return new $className($db);
        }

        throw new BaseExtension('Таблица не существует');
    }

    /**
     * {@inheritdoc}
     */
    public function findOneById($id)
    {
        $sql = "SELECT * FROM `{$this->getTableName()}` WHERE `ID` = ? LIMIT 1";

        $data = $this->db->select($sql, [$id]);

        if (!isset($data[0])) {
            return [];
        }

        return $data[0];
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        $sql = "SELECT * FROM `{$this->getTableName()}`";

        return $this->db->select($sql);
    }
}
