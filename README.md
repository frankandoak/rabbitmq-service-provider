# RabbitMq Service Provider for Silex 2.x#

**NOTE** If you need support for Silex 1.x, consider using master branch instead.

## About ##

This Silex service provider incorporates the awesome [RabbitMqBundle](https://github.com/php-amqplib/RabbitMqBundle) into your Silex Application. Installing this bundle you can use [RabbitMQ](http://www.rabbitmq.com/) messaging features in your application, using the [php-amqplib](http://github.com/php-amqplib/php-amqplib) library.

After installing this service provider, sending messages from a controller would be something like

```php

$app->post('/message', function(Request $request) use ($app){
    $producer = $app['rabbit.producer']['my_exchange_name'];
    $producer->publish('Some message');

    return new Response($msg_body);
});
```

Later when you want to consume 50 messages out of the queue names 'my_queue', you just run on the CLI:

```bash

$ ./app/console rabbitmq:consumer -m 50 my_queue
```

To learn what you can do with the bundle, please read the bundle's [README](https://github.com/php-amqplib/RabbitMqBundle/blob/master/README.md).

## Installation ##

Require the library with Composer:

```
$ composer require fiunchinho/rabbitmq-service-provider@2.x-dev
```

Then, to activate the service, register the service provider after creating your Silex Application:

```php

use Silex\Application;
use fiunchinho\Silex\Provider\RabbitServiceProvider;

$app = new Application();
$app->register(new RabbitServiceProvider());
```

Start sending messages ;)

## Usage ##

In the [README](https://github.com/php-amqplib/RabbitMqBundle/blob/master/README.md) file from the Symfony bundle you can see all the available options. For example, to configure our service with two different connections and a couple of producers, and one consumer, we will pass the following configuration:

```php
$app->register(new RabbitServiceProvider(), [
    'rabbit.connections' => [
        'default' => [
            'host'      => 'localhost',
            'port'      => 5672,
            'user'      => 'guest',
            'password'  => 'guest',
            'vhost'     => '/'
        ],
        'another' => [
            'host'      => 'another_host',
            'port'      => 5672,
            'user'      => 'guest',
            'password'  => 'guest',
            'vhost'     => '/'
        ]
    ],
    'rabbit.producers' => [
        'first_producer' => [
            'connection'        => 'another',
            'exchange_options'  => ['name' => 'a_exchange', 'type' => 'topic']
        ],
        'second_producer' => [
            'connection'        => 'default',
            'exchange_options'  => ['name' => 'a_exchange', 'type' => 'topic']
        ],
    ],
    'rabbit.consumers' => [
        'a_consumer' => [
            'connection'        => 'default',
            'exchange_options'  => ['name' => 'a_exchange','type' => 'topic'],
            'queue_options'     => ['name' => 'a_queue', 'routing_keys' => ['foo.#']],
            'callback'          => 'your_consumer_service'
        ]
    ]
]);
```

Keep in mind that the callback that you choose in the consumer needs to be a service that has been registered in the Pimple container. Consumer services implement the ConsumerInterface, which has a execute() public method.

## Consumers in the command line
We recommend you to use the Consumer command to consume messages from the queues. To use this command, just create the executable for console (as in any console applicaiton)

```php
#!/usr/bin/env php
<?php

require_once 'vendor/autoload.php';

use Silex\Application;
use fiunchinho\Silex\Provider\RabbitServiceProvider;
use fiunchinho\Silex\Command\Consumer;
use Symfony\Component\Console\Application as ConsoleApplication;

$app = new Application();
require __DIR__.'/config/dev.php';
$app->register(new RabbitServiceProvider(), array(
    'rabbit.consumers' => [
        'my_consumer' => [
            'connection'        => 'default',
            'exchange_options'  => ['name' => 'my_exchange_name','type' => 'topic'],
            'queue_options'     => ['name' => 'a_queue', 'routing_keys' => ['foo.#']],
            'callback'          => 'my_service'
        ]
    ]
));

$application = new ConsoleApplication();

$consumerCommand = new \fiunchinho\Silex\Command\Consumer('rabbitmq:consumer');
$consumerCommand->setContainer(new \fiunchinho\Silex\PimpleInteropWrapper($app));

$application->add($consumerCommand);
$application->run();
```

Unlike for Silex 1.~, we do not rely anymore on KnpConsoleProvider. We instead inject the Kernel thanks to the PimpleInteropWrapper class.

## Credits ##

- [RabbitMqBundle](https://github.com/php-amqplib/RabbitMqBundle)
