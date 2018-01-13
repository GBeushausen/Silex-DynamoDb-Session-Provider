<?php

/*
 * This file is part of Silex-DynamoDb-Session-Provider.
 *
 * Copyright (c) 2018 Gunnar Beushausen
 * https://www.gunnar-beushausen.de
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

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
			$ddbClient = $this->getDynamoDbClient($app);
			$sessionHandler = SessionHandler::fromClient($ddbClient, [
				'table_name' => $app['session.dynamodb.options']['table_name'],
			]);
			$sessionHandler->register();

			return $sessionHandler;
		};

		$app['session.dynamodb.garbagecollect'] = function() use ($app) {
			$ddbClient = $this->getDynamoDbClient($app);
			$sessionHandler = SessionHandler::fromClient($ddbClient, [
				'table_name' => $app['session.dynamodb.options']['table_name'],
				'batch_config' => [
					'before' => function ($command) use ($app) {
						//Deleting many small session rows from the DynamoDB table can take a huge amount of write capacity.
						//We can configure a delay between each delete operation to save write capacity Units
						$command['@http']['delay'] = isset($app['session.dynamodb.options']['delay']) ? $app['session.dynamodb.options']['delay'] : 500;
					}
				]
			]);

			$sessionHandler->garbageCollect();
		};
	}
	/**
	 * @param Container $app
	 * @return DynamoDbClient
	 */
	private function getDynamoDbClient(Container $app)
	{
		$config = $app['session.dynamodb.options'];
		if (array_key_exists('dynamodb_client', $config)) {
			return $config['dynamodb_client'];
		}
		else {
			return $app['AwsSdk']->createDynamoDb();
		}
	}
}
