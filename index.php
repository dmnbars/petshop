<?php
namespace App;

use App\Extension\BaseExtension;
use App\Finder\AbstractFinder;
use App\Finder\ApiTablesFinder;
use App\Finder\ParticipantFinder;
use App\Finder\SessionFinder;
use App\Handler\PostNewsHandler;
use App\Handler\SessionSubscribeHandler;

require_once 'vendor/autoload.php';

$app = new Application();
/**
 * Можно было бы вынести DataBase в сервис, но как-то избыточно в данном случае
 */
$db = new DataBase('localhost', 'test_task', 'root', '');

/**
 * Теоретически, можно было бы извернуться и вынести обработку ошибок в фронт контроллер или
 * между ним и функцией-контроллером
 */
$app->get('/api/Table', function ($params, $attributes, $db) {
    /**
     * @var DataBase $db
     */

    $data = [
        'status' => 'ok',
        'payload' => [],
        'message' => '',
    ];

    try {
        if (empty($params['table'])) {
            throw new BaseExtension('Не указан параметр table');
        }

        $apiTablesFinder = new ApiTablesFinder($db);
        $tables = $apiTablesFinder->findAllNames();
        $table = $params['table'];

        if (!in_array($table, $tables)) {
            throw new BaseExtension('Нет доступа к этой таблице');
        }

        $finder = AbstractFinder::getFinder($params['table'], $db);

        if (!empty($params['id'])) {
            $data['payload'] = $finder->findOneById(intval($params['id']));
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
            'message' => 'Произошла внутренняя ошибка, попробуйте позже',
        ];
    }

    $response = new Response($data);
    $response->format('json');

    return $response;
});

$app->get('/api/SessionSubscribe', function ($params, $attributes, $db) {
    /**
     * @var DataBase $db
     */

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

        $userFinder = new ParticipantFinder($db);
        $user = $userFinder->findOneByEmail($params['userEmail']);

        if (empty($user)) {
            throw new BaseExtension('Пользователь не зарегестрирован');
        }

        $sessionFinder = new SessionFinder($db);
        $session = $sessionFinder->findOneById(intval($params['sessionId']));

        if (empty($session)) {
            throw new BaseExtension('Такой лекции не существует');
        }

        $handler = new SessionSubscribeHandler($db, $session['ID'], $user['ID']);
        $res = $handler->handle();

        $data['message'] = $res ? 'Спасибо, вы успешно записаны!' : 'Извините, все места заняты';
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
            'message' => 'Не удалось записаться, попробуйте, позже',
        ];
    }

    $response = new Response($data);
    $response->format('json');

    return $response;
});

$app->get('/api/PostNews', function ($params, $attributes, $db) {
    /**
     * @var DataBase $db
     */

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

            $handler = new PostNewsHandler(
                $db,
                $user['ID'],
                strip_tags($params['newsTitle']),
                strip_tags($params['newsMessage'])
            );
            $handler->handle();
        }
    } catch (BaseExtension $e) {
        $data = [
            'status' => 'error',
            'payload' => [],
            'message' => $e->getMessage(),
        ];
    } catch (\PDOException $e) {
        // В данном случае ошибка целостности означает, что мы пытаемся вставить данные с дублирующим unique_news
        $message = $e->getCode() != 23000 ?
            'Не удалось добавить новость, попробуйте позже' :
            'Вы уже добавляли эту новость';

        $data = [
            'status' => 'error',
            'payload' => [],
            'message' => $message,
        ];
    } catch (\Exception $e) {
        $data = [
            'status' => 'error',
            'payload' => [],
            'message' => 'Не удалось добавить новость, попробуйте позже',
        ];
    }

    $response = new Response($data);
    $response->format('json');

    return $response;
});

$app->run($db);
