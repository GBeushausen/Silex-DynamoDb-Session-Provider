<?php

namespace DynamoDbSession;

use Aws\Common\Aws;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Session\SessionHandler;
use Silex\Application;
use Silex\Provider\SessionServiceProvider;

class DynamoDbSessionServiceProvider extends SessionServiceProvider
{
    public function register(Application $app)
    {
        parent::register($app);

        /** @noinspection PhpParamsInspection */
        $app['session.storage.handler'] = $app->share(function($app) {
            $config = $app['session.dynamodb.options'];
            if (!array_key_exists('dynamodb_client', $config)) {
                $config['dynamodb_client'] = $this->getDynamoDbClient($app['aws']);
            }

            return SessionHandler::factory($config);
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