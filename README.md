# Laravel with ElasticSearch Implementation


This package is fork from [softel/elastic_logs](https://github.com/pageswebsite/ElasticLogs.git).
That package is a simple initializer of elasticsearch logs for Kibana with laravel. 

## Installation
Install the package via composer:

``` bash
composer require softel/elastic_logs
```

## Usage

Merge this with logging.php config file 
```php
use ElasticLog\CreateElasticsearchLogger;

... 

return [
    'channels' => [
        'elastic' => [
            'driver' => 'custom',
            'via' => CreateElasticsearchLogger::class,
        ],
    ],
];

```

Config in file .env

```.env

ELASTIC_HOST="http://elasticsearch-cluster.......internal"
ELASTIC_API_KEY="xxxxxxxx"
ELASTIC_INDEX=xxxxxxx
ELASTIC_TYPE=_doc

```