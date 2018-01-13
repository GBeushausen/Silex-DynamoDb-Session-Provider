Silex-DynamoDb-Session-Provider
===============================

Provides support in Silex 2.x projects for sessions that are stored on Amazon DynamoDB. 

Typically PHP Sessions are not scalable. As soon as a load balanced / multi server setup is involved, one needs to find
a better solution. Amazons DynamoDB NoSQL storage engine is the fast, easy and scalable solution for that problem.

Just implement it in your project ( see below ). You also need a solution to get rid of stale sessions from the table. Since
deleting many old sessions can take a lot of write capacity units, it's best to configure a cron job and run the Garbage
Collector over night. 

Gunnar Beushausen

https://www.gunnar-beushausen.de 

Basic Usage
-----------

    use Silex\Provider\SessionServiceProvider;
    use DynamoDbSession\DynamoDbSessionServiceProvider;

    //Don't keep your AWS credentials in your source code! Put it in an environment variable,
    //or if you're hosting on EC2, use IAM Roles!

	$this->application['AwsSdk'] = function() {
		return new Sdk([
			'region'   => 'eu-west-1', // EU West (Ireland) Region
			'version'  => 'latest'  // Use the latest version of the AWS SDK for PHP
		]);
	};

    $app['session.storage.options'] = [
      'cookie_httponly' => true,
      'hash_function' => 'sha256',
      'hash_bits_per_character' => 6,
    ];

    $app['session.dynamodb.options'] = [
      'table_name' => 'sessions',
    ];

    $app->register(new SessionServiceProvider());
    $app->register(new DynamoDbSessionServiceProvider());
    
    //To run the Garbage Collector in order to remove stale and old sessions from the table, run the following code:
    $app['session.dynamodb.garbagecollect'];
