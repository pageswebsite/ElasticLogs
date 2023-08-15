<?php

namespace ElasticLog;

use Elastic\Elasticsearch\ClientBuilder;
use Monolog\Handler\ElasticsearchHandler;
use Monolog\Logger;

class CreateElasticsearchLogger
{
    /**
     * Create a custom Monolog instance.
     *
     * @param  array  $config
     * @return \Monolog\Logger
     */
    public function __invoke(array $config)
    {
        $logger = new Logger('elasticsearch');
        $host = env('ELASTIC_HOST');
        $key = env('ELASTIC_API_KEY');
        //create the client
        $client = ClientBuilder::create()
                    ->setHosts([$host])
                    ->setApiKey($key)
                    ->build();

        //create the handler
        $options = [
            'index' => env('ELASTIC_INDEX'),
            'type' => env('ELASTIC_TYPE')
        ];
        $handler = new ElasticsearchHandler($client, $options);

        $logger->setHandlers(array($handler));

        return $logger;
    }
}