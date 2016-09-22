<?php

namespace AaronKaz\CloudSearchQuery;

use AaronKaz\CloudSearchQuery\CloudSearchQuery;

class CloudSearchQueryTest extends \PHPUnit_Framework_TestCase
{

    public function testItSearchesWithPhrase()
    {
      $endpoint = 'http://search-ueguide-s4e6zhkw6sg5jujhd6da5wrscu.us-east-1.cloudsearch.amazonaws.com';
      $query = new CloudSearchQuery($endpoint);
      $query->phrase('ford');
      $results = $query->get();
      $this->assertEquals('200', $results->status);
    }


}
