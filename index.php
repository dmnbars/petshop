<?php
namespace App;

use App\Extension\BaseExtension;
use App\Finder\AbstractFinder;
use App\Finder\ParticipantFinder;

require_once 'vendor/autoload.php';

$app = new Application();
/**
 * Можно было бы вынести DataBase в сервис, но как-то избыточно в данном случае
 */
$db = new DataBase('localhost', 'test_task', 'root', '');

$app->get('/api/Table', function ($meta, $params, $attributes, $cookies) use ($db) {
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

$app->get('/api/SessionSubscribe', function ($meta, $params, $attributes, $cookies, $db) {
    $data = [
        'status' => 'ok',
        'payload' => [],
        'message' => '',
    ];

    try {
        foreach (['sessionId', 'userEmail'] as $param) {
            if (empty($params[$param])) {
                throw new BaseExtension('Не указан параметр ' . $param);
            }
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

$app->get('/api/PostNews', function ($meta, $params, $attributes, $cookies, $db) {
    $data = [
        'status' => 'ok',
        'payload' => [],
        'message' => '',
    ];

    try {
        foreach (['userEmail', 'newsTitle', 'newsMessage'] as $param) {
            if (empty($params[$param])) {
                throw new BaseExtension('Не указан параметр ' . $param);
            }

            $finder = new ParticipantFinder($db);
            $user = $finder->findOneByEmail($params['userEmail']);

            if (empty($user)) {
                throw new BaseExtension('Данный пользователь не зарегистрирован');
            }

            /**
             * @var DataBase $db
             */
            $insertId = $db->insert(
                'INSERT INTO News (ParticipantId, NewsTitle, NewsMessage, LikesCounter) VALUES (?, ?, ?, 0)',
                [
                    $user['ID'],
                    strip_tags($params['newsTitle']),
                    strip_tags($params['newsMessage'])
                ]
            );

            if ($insertId < 0) {
                throw new BaseExtension('Что-то пошло не так');
            }
        }
    } catch (BaseExtension $e) {
        $data = [
            'status' => 'error',
            'payload' => [],
            'message' => $e->getMessage(),
        ];
    } catch (\PDOException $e) {
        // В данном случае ошибка целостности означает, что мы пытаемся вставить данные с дублирующим unique_news
        if ($e->getCode() != 23000) {
            throw $e;
        }

        $data = [
            'status' => 'error',
            'payload' => [],
            'message' => 'Вы уже добавляли эту новость',
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

$app->run($db);
