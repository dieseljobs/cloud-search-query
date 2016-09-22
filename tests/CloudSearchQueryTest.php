<?php

namespace AaronKaz\CloudSearchQuery;

use AaronKaz\CloudSearchQuery\CloudSearchQuery;

class CloudSearchQueryTest extends \PHPUnit_Framework_TestCase
{

    public function test_it_should_return_hello_world()
    {
        $query = new CloudSearchQuery();
        $this->assertEquals("Hello World", $query->foo());
    }


}
