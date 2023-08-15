<?php

namespace ElasticLog;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use ElasticLog\Commands\InstallCommand;
use Illuminate\Support\ServiceProvider;
use ElasticLog\Formatter\KibanaFormatter;
use Illuminate\Support\Facades\App;

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

        $this->app->bind(KibanaFormatter::class, function ($app) {
            return new KibanaFormatter($this->getIndexName(), $this->getIndexType(), $this->getApplicationName());
        });

    }

    /**
     * @return string
     */
    protected function getApplicationName(): string
    {
        return env('APP_NAME');
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
