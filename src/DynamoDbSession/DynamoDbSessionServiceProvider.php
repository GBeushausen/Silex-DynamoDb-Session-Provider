<?php

namespace DynamoDbSession;


use Aws\DynamoDb\SessionHandler;
use Aws\Sdk;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DynamoDbSessionServiceProvider implements ServiceProviderInterface
{
	public function register(Container $app)
	{
		/** @noinspection PhpParamsInspection */
		$app['session.storage.handler'] = function () use ($app) {
			$config = $app['session.dynamodb.options'];
			if (!array_key_exists('dynamodb_client', $config)) {
				$config['dynamodb_client'] = $this->getDynamoDbClient($app['AwsSdk']);
			}

			$sessionHandler = SessionHandler::fromClient($config['dynamodb_client'], [
				'table_name' => $app['session.dynamodb.options']['table_name'],
			]);
			$sessionHandler->register();

			return $sessionHandler;
		};
	}
	/**
	 * @param Sdk $aws
	 * @return DynamoDbClient
	 */
	private function getDynamoDbClient(Sdk $aws)
	{
		return $aws->createDynamoDb();
	}
}