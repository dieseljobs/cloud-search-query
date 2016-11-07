<?php

namespace Kazak\CloudSearchQuery;

use Kazak\CloudSearchQuery\CloudSearchQuery;

class CloudSearchQueryTest extends \PHPUnit_Framework_TestCase
{

    private $endpoint = 'http://search-ueguide-s4e6zhkw6sg5jujhd6da5wrscu.us-east-1.cloudsearch.amazonaws.com';

    public function testItSearchesWithBreakingChars()
    {
        $query = new CloudSearchQuery($this->endpoint);
        $query->term("Vander Haag's", 'seller');
        $results = $query->get();
        $this->assertEquals('200', $results->status);
    }

    public function testItSearchesWithNearSearch()
    {
        $query = new CloudSearchQuery($this->endpoint);
        $query->near("2012 FORD F-150");
        $results = $query->get();
        $this->assertEquals('200', $results->status);
    }

    public function testItSearchesWithNestedPhrase()
    {
        $query = new CloudSearchQuery($this->endpoint);
        $query->size(10)
              ->start(0)
              ->qOr(function($builder) {
                $builder->phrase('ford')
                        ->phrase('truck');
              })
              ->term('National Equipment', 'seller')
              ->range('year', '1987');
        $results = $query->get();
        $this->assertEquals('200', $results->status);
    }

    public function testItSearchesWithLatLon()
    {
        $endpoint = 'http://search-ueguide-s4e6zhkw6sg5jujhd6da5wrscu.us-east-1.cloudsearch.amazonaws.com';
        $query = new CloudSearchQuery($endpoint);
        $query->phrase('ford')->latlon('latlon', '34.707731', '-89.906631');
        $results = $query->get();
        $this->assertEquals('200', $results->status);
    }

    public function testItSearchesWithFacets()
    {
        $endpoint = 'http://search-ueguide-s4e6zhkw6sg5jujhd6da5wrscu.us-east-1.cloudsearch.amazonaws.com';
        $query = new CloudSearchQuery($endpoint);
        $query->phrase('ford')->facet('make', 'count')->facet('model_family');
        $results = $query->get();
        //var_dump($results->facets);
        $this->assertEquals('200', $results->status);
        $this->assertEquals(true, is_array($results->facets));
    }

    public function testItSearchesWithExpression()
    {
        $endpoint = 'http://search-ueguide-s4e6zhkw6sg5jujhd6da5wrscu.us-east-1.cloudsearch.amazonaws.com';
        $query = new CloudSearchQuery($endpoint);
        $lat = '34.707731';
        $lon = '-89.906631';
        $query->phrase('ford')
              ->latlon('latlon', $lat, $lon, 50, true)
              ->sort('distance', 'asc');
        $results = $query->get();
        $this->assertEquals('200', $results->status);
    }

}
