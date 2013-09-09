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

        $app['session.storage.handler'] = $app->share(function($app) {
            return new DynamoDbSessionHandler($this->getDynamoDbClient($app['aws']), $app['session.dynamodb.options']);
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