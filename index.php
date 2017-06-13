<?php
namespace App;

use App\Extension\BaseExtension;
use App\Finder\AbstractFinder;

require_once 'vendor/autoload.php';

$app = new Application();
$connection = new DataBase('localhost', 'test_task', 'root', '');

$app->get('/api/Table', function ($meta, $params, $attributes, $cookies, $db) {
    $tables = [
        'News',
        'Session',
    ];

    $data = [
        'status' => 'ok',
        'payload' => [],
        'message' => '',
    ];

    try {
        if (empty($params['table'])) {
            throw new BaseExtension('Не указан параметр table');
        }

        $table = $params['table'];

        if (!in_array($table, $tables)) {
            throw new BaseExtension('Нет доступа к этой таблице');
        }

        $finder = AbstractFinder::getFinder($params['table'], $db);

        if (!empty($params['id'])) {
            $data['payload'] = $finder->findById(intval($params['id']));
        } else {
            $data['payload'] = $finder->findAll();
        }
    } catch (BaseExtension $e) {
        $data = [
            'status' => 'error',
            'payload' => [],
            'message' => $e->getMessage(),
        ];
    } catch (\Exception $e) {
        $data = [
            'status' => 'error',
            'payload' => [],
            'message' => 'Что-то пошло не так',
        ];
    }

    $response = new Response($data);
    $response->format('json');

    return $response;
});

$app->run($connection);
