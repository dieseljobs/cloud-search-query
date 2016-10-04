# cloud-search-query
An ORM-like wrapper for building AWS CloudSearch structured queries

## Installation ##
CloudSearchQuery is currently a repository package only.  In `composer.json` add:

    "repositories": [
      {
        "type": "vcs",
        "url": "https://github.com/aaronkaz/cloud-search-query.git"
      }
    ],
    "require": {
        "aaron-kaz/cloud-search-query": "dev-master"
    },
    

## Basic Usage ##
Initialize a query object with a valid CloudSearch full URI endpoint

    $query = new CloudSearchQuery('http://search-yourdomain.us-east-1.cloudsearch.amazonaws.com');

You can chain query methods like so

    $query->phrase('ford')
          ->term('National Equipment', 'seller')
          ->range('year', '1987');

use the `get()` method to submit query and retrieve results from AWS.  Use property accessors on the returned results object.

    $results = $query->get();
    $matchedDocuments = $results->hits;

## Search Query Operators and Nested Queries ##
You can use the `and`, `or`, and `not` operators to build compound and nested queries.  
The corresponding `and()`, `or()`, and `not()` methods expect a closure as their argument.
You can chain all available methods as well nest more subqueries inside of closures.

    $query->or(function($builder) {
              $builder->phrase('ford')
                      ->phrase('truck');
            })
