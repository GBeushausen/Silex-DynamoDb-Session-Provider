Silex-DynamoDb-Session-Provider
===============================

Provides support in Silex projects for sessions that are stored on Amazon DynamoDB

Basic Usage
===========

    use DynamoDbSession\DynamoDbSessionServiceProvider;

    $app['aws.config'] = array(
        'key'    => '',
        'secret' => '',
        'region' => 'us-east-1',
    );

    $app['aws'] = $app->share(function(Application $app) {
        return \Aws\Common\Aws::factory($app['aws.config']);
    });

    $app->register(new DynamoDbSessionServiceProvider());

    $app['session.storage.options'] = array(
      'cookie_httponly' => true,
      'hash_function' => 'sha256',
      'hash_bits_per_character' => 6,
    );

    $app['session.dynamodb.options'] = array(
      'table_name' => 'sessions',
    );
