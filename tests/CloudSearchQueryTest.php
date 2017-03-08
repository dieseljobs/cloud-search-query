<?php

use TheLHC\CloudSearchQuery\CloudSearchQuery;
use TheLHC\CloudSearchQuery\Tests\TestCase;

class CloudSearchQueryTest extends TestCase
{

    public function testCanResolveFromTheContainer()
    {
        $manager = $this->app->make('TheLHC\CloudSearchQuery\CloudSearchQuery');
        $this->assertInstanceOf(CloudSearchQuery::class, $manager);
    }

    public function testItSearchesWithBreakingChars()
    {
        $query = $this->app->make('TheLHC\CloudSearchQuery\CloudSearchQuery');
        $query->term("Vander Haag's", 'seller');
        $results = $query->get();
        $this->assertEquals('200', $results->status);
    }

    public function testItSearchWithQuotesInParams()
    {
        $query = $this->app->make('TheLHC\CloudSearchQuery\CloudSearchQuery');
        $query->term("TSE INTERNATIONAL", 'make')
                ->term('82" HEAVY DUTY TILLER');
        $results = $query->get();
        $this->assertEquals('200', $results->status);
    }

    public function testItSearchesWithNearSearch()
    {
        $query = $this->app->make('TheLHC\CloudSearchQuery\CloudSearchQuery');
        $query->near("2012 FORD F-150");
        $results = $query->get();
        $this->assertEquals('200', $results->status);
    }

    public function testItSearchesWithOptions()
    {
        $query = $this->app->make('TheLHC\CloudSearchQuery\CloudSearchQuery');
        $query->options('fields', ['seller', 'category']);
        $query->qOr(function($builder) {
            $term = "Gregory Poole Equipment";
            $builder->phrase("{$term}*");
            $builder->prefix($term);
            $builder->near($term);
        });
        $query->facet('seller');
        $results = $query->get();
        $this->assertEquals('200', $results->status);
        $this->assertEquals(false, empty($results->hits));
    }

    public function testItSearchesWithNestedPhrase()
    {
        $query = $this->app->make('TheLHC\CloudSearchQuery\CloudSearchQuery');
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

    public function testItSearchesWithFilterQuery()
    {
        $query = $this->app->make('TheLHC\CloudSearchQuery\CloudSearchQuery');
        $query->size(10)
              ->start(0)
              ->filterOr(function($builder) {
                $builder->phrase('ford', 'desc')
                        ->phrase('truck', 'desc');
              })
              ->filterTerm('National Equipment', 'seller')
              ->range('year', '1987');
        $results = $query->get();
        $this->assertEquals('200', $results->status);
    }

    public function testItSearchesWithLatLon()
    {
        $query = $this->app->make('TheLHC\CloudSearchQuery\CloudSearchQuery');
        $query->phrase('ford')->latlon('latlon', '34.707731', '-89.906631');
        $results = $query->get();
        $this->assertEquals('200', $results->status);
    }

    public function testItSearchesWithFacets()
    {
        $query = $this->app->make('TheLHC\CloudSearchQuery\CloudSearchQuery');
        $query->phrase('ford')->facet('make', 'count')->facet('model_family');
        $results = $query->get();
        $this->assertEquals('200', $results->status);
        $this->assertEquals(true, is_array($results->facets));
    }

    public function testItSearchesWithExpression()
    {
        $query = $this->app->make('TheLHC\CloudSearchQuery\CloudSearchQuery');
        $lat = '34.707731';
        $lon = '-89.906631';
        $query->phrase('ford')
              ->latlon('latlon', $lat, $lon, 50, true)
              ->sort('distance', 'asc');
        $results = $query->get();
        $this->assertEquals('200', $results->status);
    }

    public function testItSearchesWithStats()
    {
        $query = $this->app->make('TheLHC\CloudSearchQuery\CloudSearchQuery');
        $query->phrase('ford')
                ->facet('year', 'count')
                ->stats('year');
        $results = $query->get();
        $this->assertEquals('200', $results->status);
        $this->assertEquals(true, is_array($results->stats));

    }

    public function testItSearchesWithFacetBuckets()
    {
        $query = $this->app->make('TheLHC\CloudSearchQuery\CloudSearchQuery');
        $query->phrase('ford')->facetBuckets('year', ["[1970,1979]","[1980,1989]","[1990,1999]"]);
        $results = $query->get();
        $this->assertEquals('200', $results->status);
        $this->assertEquals(true, is_array($results->facets));
    }

    public function testItCatchesMaxStart()
    {
        $query = $this->app->make('TheLHC\CloudSearchQuery\CloudSearchQuery');
        $query->size(10)
              ->start(15000)
              ->matchall()
              ->pretty();
        $results = $query->get();
        dd($results);
    }

}
