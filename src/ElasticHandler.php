<?php

namespace Elklog;

use Elasticsearch\Client;
use Illuminate\Support\Facades\App;
use Monolog\Handler\ElasticsearchHandler;
use Monolog\Logger;

class Elastic extends ElasticsearchHandler
{
    /**
     * @param Client     $client  Elasticsearch Client object
     * @param array      $options Handler configuration
     * @param string|int $level   The minimum logging level at which this handler will be triggered
     * @param bool       $bubble  Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct(Client $client, array $options = [], $level = Logger::DEBUG, bool $bubble = true)
    {
        parent::__construct($client, $options, $level, $bubble);
    }

    public function isHandling(array $record): bool
    {
        return parent::isHandling($record) && app('config')->get(sprintf('elk.%s.enabled',$this->getEnvironmentName()));

    }

    protected function getEnvironmentName(): string
    {
        return $this->isLive() ? 'live' : 'local' ;
    }

    public function isLive() : bool
    {
        return ! App::environment(['local', 'staging']);
    }

}
