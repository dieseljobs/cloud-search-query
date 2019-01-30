<?php

namespace TheLHC\CloudSearchQuery;

use Aws\CloudSearchDomain\CloudSearchDomainClient;

class CloudSearchQuery
{
    /**
     * Configuration array
     *
     * @var Array
     */
    public $config;

    /**
     * CloudSearchDomainClient factory instance
     *
     * @var Aws\CloudSearchDomain\CloudSearchDomainClient
     */
    private $client;

    /**
     * Our default builder object instance
     *
     * @var StructuredQueryBuilder
     */
    private $builder;

    /**
     * Query results object instance
     *
     * @var CloudSearchQueryResults
     */
    private $results;

    /**
     * Store encountered error
     *
     * @var string
     */
    public $error;

    /**
     * Create new instance and CloudSearchDomainClient from endpoint
     * constructs new StructuredQueryBuilder instance with client
     *
     * @param array $config
     * @param mixed $query
     */
    public function __construct($config, $query = null)
    {
        if (!isset($config["endpoint"])) {
            throw new \Exception(
                "Missing parameter 'endpoint' passed to TheLHC\CloudSearchQuery\CloudSearchQuery"
            );
        }
        $this->config = $config;

        $csConfig = [
            'version'  => '2013-01-01',
            'endpoint' => $config["endpoint"]
        ];

        if (!empty($config['key']) && !empty($config['secret'])) {
            $csConfig['credentials'] = [
                'key' => $config['key'],
                'secret' => $config['secret']
            ];
        }
        $client = CloudSearchDomainClient::factory($csConfig);
        $this->client = $client;
        $this->builder = ($query) ? $query : new StructuredQueryBuilder($this);
    }

    /**
     * Alias to cursor method
     *
     * @param  string $cursor
     * @return CloudSearchQuery
     */
    public function cursor($cursor = 'initial')
    {
        $this->builder->cursor($cursor);
        return $this;
    }

    /**
     * Alias to set builder expression
     *
     * @param  string $accessor
     * @param  string $expression
     * @return CloudSearchQuery
     */
    public function expr($accessor, $expression)
    {
        $this->builder->expr($accessor, $expression);
        return $this;
    }

    /**
     * Alias function to build return facets array
     *
     * @param  string  $field
     * @param  string  $sort
     * @param  integer $size
     * @return CloudSearchQuery
     */
    public function facet($field, $sort = "bucket", $size = 10)
    {
        $this->builder->facet($field, $sort, $size);
        return $this;
    }

    /**
     * Alias function to build return facets with explicit buckets
     *
     * @param  string $field
     * @param  Array $buckets
     * @return CloudSearchQuery
     */
    public function facetBuckets($field, $buckets, $method = "filter")
    {
        $this->builder->facetBuckets($field, $buckets, $method);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create an 'and' wrapped query block
     *
     * @param  function|string $block
     * @return CloudSearchQuery
     */
    public function qAnd($block)
    {
        $this->builder->q->qAnd($block);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create an 'and' wrapped filter query block
     *
     * @param  function|string $block
     * @return CloudSearchQuery
     */
    public function filterAnd($block)
    {
        $this->builder->fq->qAnd($block);
        return $this;
    }

    /**
     * Alias to build matchall query
     *
     * @return CloudSearchQuery
     */
    public function matchall()
    {
        $this->builder->q->matchall();
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create a near (sloppy) query
     *
     * @param  string $value
     * @param  string $field
     * @param  int    $distance
     * @param  int    $boost
     * @return CloudSearchQuery
     */
    public function near($value, $field = null, $distance = 3, $boost = null)
    {
        $this->builder->q->near($value, $field, $distance, $boost);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create a near (sloppy) query
     *
     * @param  string $value
     * @param  string $field
     * @param  int    $distance
     * @param  int    $boost
     * @return CloudSearchQuery
     */
    public function filterNear($value, $field, $distance = 3, $boost = null)
    {
        $this->builder->fq->near($value, $field, $distance, $boost);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create a 'not' wrapped query block
     *
     * @param  function|string $block
     * @return CloudSearchQuery
     */
    public function qNot($block)
    {
        $this->builder->q->qNot($block);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create a 'not' wrapped query block
     *
     * @param  function|string $block
     * @return CloudSearchQuery
     */
    public function filterNot($block)
    {
        $this->builder->fq->qNot($block);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create an 'or' wrapped query block
     *
     * @param  function|string $block
     * @return CloudSearchQuery
     */
    public function qOr($block)
    {
        $this->builder->q->qOr($block);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create an 'or' wrapped query block
     *
     * @param  function|string $block
     * @return CloudSearchQuery
     */
    public function filterOr($block)
    {
        $this->builder->fq->qOr($block);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create a phrase query
     *
     * @param  string $value
     * @param  string $field
     * @param  int    $boost
     * @return CloudSearchQuery
     */
    public function phrase($value, $field = null, $boost = null)
    {
        $this->builder->q->phrase($value, $field, $boost);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create a phrase query
     *
     * @param  string $value
     * @param  string $field
     * @param  int    $boost
     * @return CloudSearchQuery
     */
    public function filterPhrase($value, $field, $boost = null)
    {
        $this->builder->fq->phrase($value, $field, $boost);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create a prefix query
     *
     * @param  string $value
     * @param  string $field
     * @param  int    $boost
     * @return CloudSearchQuery
     */
    public function prefix($value, $field = null, $boost = null)
    {
        $this->builder->q->prefix($value, $field, $boost);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create a prefix query
     *
     * @param  string $value
     * @param  string $field
     * @param  int    $boost
     * @return CloudSearchQuery
     */
    public function filterPrefix($value, $field, $boost = null)
    {
        $this->builder->fq->prefix($value, $field, $boost);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create a range query
     *
     * @param  string $value
     * @param  string|int $min
     * @param  string|int $max
     * @return CloudSearchQuery
     */
    public function range($field, $min, $max = null)
    {
        $this->builder->q->range($field, $min, $max);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create a range query
     *
     * @param  string $value
     * @param  string|int $min
     * @param  string|int $max
     * @return CloudSearchQuery
     */
    public function filterRange($field, $min, $max = null)
    {
        $this->builder->fq->range($field, $min, $max);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create a term query
     *
     * @param  string $value
     * @param  string $field
     * @param  int    $boost
     * @return CloudSearchQuery
     */
    public function term($value, $field = null, $boost = null)
    {
        $this->builder->q->term($value, $field, $boost);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create a term query
     *
     * @param  string $value
     * @param  string $field
     * @param  int    $boost
     * @return CloudSearchQuery
     */
    public function filterTerm($value, $field, $boost = null)
    {
        $this->builder->fq->term($value, $field, $boost);
        return $this;
    }



    /**
     * Alias function to our builder object
     * Set return fields property of query
     *
     * @param  string $value
     * @return CloudSearchQuery
     */
    public function returnFields($value)
    {
        $this->builder->returnFields($value);
        return $this;
    }

    /**
     * Alias to pretty method
     *
     * @return CloudSearchQuery
     */
    public function pretty()
    {
        $this->builder->pretty();
        return $this;
    }

    /**
     * Alias function to our builder object
     * Set options property of query
     *
     * @param  string $key
     * @param  string $value
     * @return CloudSearchQuery
     */
    public function options($key, $value)
    {
        $this->builder->options($key, $value);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Set size property of query
     *
     * @param  int $value
     * @return CloudSearchQuery
     */
    public function size($value)
    {
        $this->builder->size($value);
        return $this;
    }

    /**
     * Alias function to sort query
     *
     * @param  string $field
     * @param  string $direction
     * @return CloudSearchQuery
     */
    public function sort($field, $direction = 'asc')
    {
        $this->builder->sort($field, $direction);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Set start property of query
     *
     * @param  int $value
     * @return CloudSearchQuery
     */
    public function start($value)
    {
        $this->builder->start($value);
        return $this;
    }

    /**
     * Alias to build field statistics
     *
     * @param  string $field
     * @return CloudSearchQuery
     */
    public function stats($field)
    {
        $this->builder->stats($field);
        return $this;
    }

    /**
     * Alias function to build a location range filter
     *
     * @param  string  $field
     * @param  string  $lat
     * @param  string  $lon
     * @param  integer $radius
     * @param  bool    $addExpr
     * @param  string  $units
     * @return CloudSearchQuery
     */
    public function latlon($field, $lat, $lon, $radius = 50, $addExpr = false, $units = 'mi')
    {
        $this->builder->latlon($field, $lat, $lon, $radius, $addExpr, $units);
        return $this;
    }

    /**
     * Alias to build distance expression
     *
     * @param string $field the cloudsearch latlon field name
     * @param string $lat
     * @param string $lon
     * @return CloudSearchQuery
     */
    public function addDistanceExpr($field, $lat, $lon)
    {
        $this->builder->addDistanceExpr($field, $lat, $lon);
        return $this;
    }







    public function getSize()
    {
        return $this->builder->size;
    }

    public function getStart()
    {
        return $this->builder->start;
    }

    public function getQuery()
    {
        return $this->builder->getQuery();
    }

    public function addRawQuery($val)
    {
        $this->builder->addRawQuery($val);

        return $this;
    }

    public function getFilterQuery()
    {
        return $this->builder->getFilterQuery();
    }

    public function addRawFilterQuery($val)
    {
        $this->builder->addRawFilterQuery($val);

        return $this;
    }

    public function getFacets()
    {
        return $this->builder->facets;
    }

    public function getExpressions()
    {
        return $this->builder->expressions;
    }

    public function getReturnFields()
    {
        return $this->builder->returnFields;
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
