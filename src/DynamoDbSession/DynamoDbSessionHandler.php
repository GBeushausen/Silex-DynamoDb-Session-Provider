<?php

namespace DynamoDbSession;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Session\SessionHandler;

/** @noinspection PhpUndefinedClassInspection */
class DynamoDbSessionHandler implements \SessionHandlerInterface
{
    /**
     * @var \Aws\DynamoDb\Session\SessionHandler
     */
    private $handler;

    /**
     * @var \Aws\DynamoDb\DynamoDbClient
     */
    private $client;

    public function __construct(DynamoDbClient $client, array $config = array())
    {
        $this->client = $client;

        $config['dynamodb_client'] = $client;

        $this->handler = SessionHandler::factory($config);
    }

    public function close()
    {
        return $this->handler->close();
    }

    public function destroy($session_id)
    {
        return $this->handler->destroy($session_id);
    }

    public function gc($maxlifetime)
    {
        return $this->handler->gc($maxlifetime);
    }

    public function open($save_path, $session_id)
    {
        return $this->handler->open($save_path, $session_id);
    }

    public function read($session_id)
    {
        return $this->handler->read($session_id);
    }

    public function write($session_id, $session_data)
    {
        return $this->handler->write($session_id, $session_data);
    }
}