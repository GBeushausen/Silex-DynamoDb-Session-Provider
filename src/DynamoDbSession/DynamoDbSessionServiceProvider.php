<?php

namespace DynamoDbSession;

use Aws\Common\Aws;
use Aws\DynamoDb\DynamoDbClient;
use Silex\Application;
use Silex\Provider\SessionServiceProvider;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

class DynamoDbSessionServiceProvider extends SessionServiceProvider
{
    public function register(Application $app)
    {
        parent::register($app);

        /** @noinspection PhpParamsInspection */
        $app['session.storage'] = $app->share(function($app) {
            $handler = new DynamoDbSessionHandler($this->getDynamoDbClient($app['aws']), $app['session.dynamodb.options']);
            return new NativeSessionStorage($app['session.storage.options'], $handler);
        });

        $app['session.dynamodb.options'] = array();
    }

    /**
     * @param Aws $aws
     * @return DynamoDbClient
     */
    private function getDynamoDbClient(Aws $aws)
    {
        return $aws->get('dynamodb');
    }
}