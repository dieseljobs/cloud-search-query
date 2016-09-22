<?php

namespace AaronKaz\CloudSearchQuery;

use Aws\CloudSearchDomain\CloudSearchDomainClient;

class CloudSearchQuery
{

    private $client;

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

    public $error;

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
        $this->client = $client;
        $this->builder = new StructuredQueryBuilder();
    }

    public function size($value)
    {
        $this->builder->setSize($value);
        return $this;
    }

    public function start($value)
    {
        $this->builder->setStart($value);
        return $this;
    }

    public function returnFields($value)
    {
        $this->builder->setReturnFields($value);
        return $this;
    }

    /**
     * Method to query for phrase
     *
     * @param  string $phrase
     * @return this
     */
    public function phrase($value, $field = null, $boost = null)
    {
        $this->builder->addPhrase($value, $field, $boost);
        return $this;
    }

    public function term($value, $field = null, $boost = null)
    {
        $this->builder->addTerm($value, $field, $boost);
        return $this;
    }

    public function prefix($value, $field = null, $boost = null)
    {
        $this->builder->addPrefix($value, $field, $boost);
        return $this;
    }

    public function range($field, $min, $max = null)
    {
      $this->builder->addRange($field, $min, $max);
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
        $request = $this->builder->buildStructuredQuery();
        var_dump($request);
        try {
            $response = $this->client->search($request);
        } catch (CloudSearchDomainException $e) {
            $this->error = $e->getMessage();
            return $this->error;
        }
        $results = new CloudSearchQueryResults($response);
        return $results->map();
    }

}
