<?php

namespace ElasticLog;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use ElasticLog\Commands\InstallCommand;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Monolog\Formatter\ElasticsearchFormatter;

class ElasticServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/resources/config/elastic-log.php' => config_path('elastic.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->singleton(Client::class, function ($app) {
            $url = $this->getHost();

            return ClientBuilder::create()->setHosts([$url])->build();
        });

        $this->app->bind(ElasticsearchFormatter::class, function ($app) {
            return new ElasticsearchFormatter( $this->getIndexName(), $this->getIndexType() );
        });

    }

    /**
     * check if live environment
     *
     * @return bool
     */
    public function isLiveEnvironment()
    {
        return ! App::environment(['local', 'staging']);
    }

    /**
     * @return string
     */
    protected function getEnvironmentName(): string
    {
        return $this->isLiveEnvironment() ? 'live' : 'local' ;
    }

    /**
     * get index type
     *
     * @return string
     */
    public function getIndexType(): string
    {
        $env = $this->getEnvironmentName() ;

        return config( sprintf( 'elastic.%s.type', $env ) );
    }

    /**
     * get index name
     *
     * @return string
     */
    public function getIndexName(): string
    {
        $env = $this->getEnvironmentName() ;

        return config( sprintf( 'elastic.%s.index', $env ) );
    }

    /**
     * get index host
     *
     * @return string
     */
    public function getHost(): string
    {
        $env = $this->getEnvironmentName() ;

        $schema = config( sprintf('elastic.%s.schema', $env ) );
        $domain = config( sprintf('elastic.%s.domain', $env ) );
        $port   = config( sprintf('elastic.%s.port', $env ) );

        return $this->buildUrl( $schema, $domain, $port );
    }

    /**
     * @param string $schema
     * @param string $domain
     * @param string $port
     * @return string
     */
    protected function buildUrl( string $schema, string $domain, string $port ): string
    {
        return sprintf( '%s://%s:%s', $schema, $domain, $port );
    }
}
