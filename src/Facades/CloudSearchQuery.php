<?php

namespace TheLHC\CloudSearchQuery\Facades;

use Illuminate\Support\Facades\Facade;

class CloudSearchQuery extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
      return 'TheLHC\CloudSearchQuery\CloudSearchQuery';
    }

}
