<?php

namespace AaronKaz\CloudSearchQuery;

use Aws\CloudSearchDomain\CloudSearchDomainClient;

class CloudSearchQuery
{
    /**
     * Our builder object
     *
     * @var StructuredQueryBuilder
     */
    private $builder;

    /**
     * Query results
     *
     * @var CloudSearchQueryResults
     */
    private $results;

    /**
     * Create new instance and CloudSearchDomainClient from endpoint
     * constructs new StructuredQueryBuilder instance with client
     *
     * @param string $endpoint
     */
    public function __construct($endpoint)
    {
        $client = CloudSearchDomainClient::factory(
            [
                'version'  => '2013-01-01',
                'endpoint' => $endpoint
            ]
        );
        $this->builder = new StructuredQueryBuilder($client);
    }

    /**
     * Method to query for phrase
     *
     * @param  string $phrase
     * @return this
     */
    public function phrase($phrase)
    {
        $this->builder->addPhrase($phrase);
        return $this;
    }

    /**
     * Method to trigger request-response
     * Returns a new instance of CloudSearchQueryResults
     *
     * @return CloudSearchQueryResults
     */
    public function get()
    {
        $response = $this->builder->getResults();
        $this->results = new CloudSearchQueryResults($response);
        return $this->results->map();
    }

}
