<?php

namespace Kazak\CloudSearchQuery;

use Aws\CloudSearchDomain\CloudSearchDomainClient;

class CloudSearchQuery
{

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

    /**
     * Alias function to our builder object
     * Set size property of query
     *
     * @param  int $value
     * @return this
     */
    public function size($value)
    {
        $this->builder->size($value);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Set start property of query
     *
     * @param  int $value
     * @return this
     */
    public function start($value)
    {
        $this->builder->start($value);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Set return fields property of query
     *
     * @param  string $value
     * @return this
     */
    public function returnFields($value)
    {
        $this->builder->returnFields($value);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create a phrase query
     *
     * @param  string $value
     * @param  string $field
     * @param  int    $boost
     * @return this
     */
    public function phrase($value, $field = null, $boost = null)
    {
        $this->builder->phrase($value, $field, $boost);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create a term query
     *
     * @param  string $value
     * @param  string $field
     * @param  int    $boost
     * @return this
     */
    public function term($value, $field = null, $boost = null)
    {
        $this->builder->term($value, $field, $boost);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create a prefix query
     *
     * @param  string $value
     * @param  string $field
     * @param  int    $boost
     * @return this
     */
    public function prefix($value, $field = null, $boost = null)
    {
        $this->builder->prefix($value, $field, $boost);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create a range query
     *
     * @param  string $value
     * @param  string|int $min
     * @param  string|int $max
     * @return this
     */
    public function range($field, $min, $max = null)
    {
        $this->builder->range($field, $min, $max);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create an 'and' wrapped query block
     *
     * @param  func $block
     * @return this
     */
    public function qAnd($block)
    {
        $this->builder->qAnd($block);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create an 'or' wrapped query block
     *
     * @param  func $block
     * @return this
     */
    public function qOr($block)
    {
        $this->builder->qOr($block);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create a 'not' wrapped query block
     *
     * @param  func $block
     * @return this
     */
    public function qNot($block)
    {
        $this->builder->qNot($block);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create a phrase filter query
     *
     * @param  string $value
     * @param  string $field
     * @param  int    $boost
     * @return this
     */
    public function filterPhrase($value, $field = null, $boost = null)
    {
        $this->builder->filterPhrase($value, $field, $boost);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create a term filter query
     *
     * @param  string $value
     * @param  string $field
     * @param  int    $boost
     * @return this
     */
    public function filterTerm($value, $field = null, $boost = null)
    {
        $this->builder->filterTerm($value, $field, $boost);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create a prefix filter query
     *
     * @param  string $value
     * @param  string $field
     * @param  int    $boost
     * @return this
     */
    public function filterPrefix($value, $field = null, $boost = null)
    {
        $this->builder->filterPrefix($value, $field, $boost);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create a range filter query
     *
     * @param  string $value
     * @param  string|int $min
     * @param  string|int $max
     * @return this
     */
    public function filterRange($field, $min, $max = null)
    {
        $this->builder->filterRange($field, $min, $max);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create an 'and' wrapped filter query block
     *
     * @param  func $block
     * @return this
     */
    public function filterAnd($block)
    {
        $this->builder->filterAnd($block);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create an 'or' wrapped filter query block
     *
     * @param  func $block
     * @return this
     */
    public function filterOr($block)
    {
        $this->builder->filterOr($block);
        return $this;
    }

    /**
     * Alias function to our builder object
     * Create a 'not' wrapped filter query block
     *
     * @param  func $block
     * @return this
     */
    public function filterNot($block)
    {
        $this->builder->filterNot($block);
        return $this;
    }

    /**
     * Alias function to build a location range filter
     *
     * @param  string  $field
     * @param  string  $lat
     * @param  string  $lon
     * @param  integer $radius
     * @return this
     */
    public function latlon($field, $lat, $lon, $radius = 50, $addExpr = false)
    {
        $this->builder->latlon($field, $lat, $lon, $radius, $addExpr);
        return $this;
    }

    /**
     * Alias to set builder expression
     *
     * @param  string $accessor
     * @param  string $expression
     * @return this
     */
    public function expr($accessor, $expression)
    {
        $this->builder->expr($accessor, $expression);
        return $this;
    }

    /**
     * Alias to build distance expression
     *
     * @param string $field the cloudsearch latlon field name
     * @param string $lat
     * @param string $lon
     */
    public function addDistanceExpr($field, $lat, $lon)
    {
        $this->builder->addDistanceExpr($field, $lat, $lon);
        return $this;
    }

    /**
     * Alias function to build return facets array
     *
     * @param  string  $field
     * @param  string  $sort
     * @param  integer $size
     * @return this
     */
    public function facet($field, $sort = "bucket", $size = 10)
    {
        $this->builder->facet($field, $sort, $size);
        return $this;
    }

    /**
     * Alias function to sort query
     *
     * @param  string $field
     * @param  string $direction
     * @return this
     */
    public function sort($field, $direction = 'asc')
    {
        $this->builder->sort($field, $direction);
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
        return $this->builder->query;
    }

    public function getFilterQuery()
    {
        return $this->builder->filterQuery;
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
        //var_dump($request);
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
