<?php


namespace App\Extension;

/**
 * Можно было бы для каждого "вида" ошибки сделать свой exception
 * (но в рамках этой задачи это избыточно, так как все ошибки данного приложения обрабатываются одинаково)
 * Class BaseExtension
 * @package App\Extension
 */
class BaseExtension extends \Exception
{
}
