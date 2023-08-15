<?php declare(strict_types=1);

namespace Elklog\Formatter;

use Monolog\Formatter\ElasticsearchFormatter;

/**
 * Format a log message into an Elasticsearch record  for kibana logs
 *
 * @author Pavel Marachkov
 */
class KibanaFormatter extends ElasticsearchFormatter
{
    /**
     * @var string Application name for log record
     */
    protected $applicationName;

	/**
	 * @param string $index Elasticsearch index name
	 * @param string $type  Elasticsearch record type
     * @param string $applicationName  Current application name for log
	 */
	public function __construct(string $index, string $type, string $applicationName)
	{
		parent::__construct($index, $type);
        $this->applicationName = $applicationName;
	}

	/**
	 * {@inheritdoc}
	 */
	public function format(array $record)
	{
		$record = parent::format($record);

		return $this->getDocumentForKibana($record);
	}

	/**
	 * Convert a log message into an Elasticsearch record for kibana logs
	 *
	 * @param  array $record Log message
	 * @return array
	 */
	protected function getDocumentForKibana(array $record): array
    	{
    		$record['@timestamp'] = $record['datetime'];
    		$record['application_name'] = $this->applicationName;

    		return $record;
    	}
}