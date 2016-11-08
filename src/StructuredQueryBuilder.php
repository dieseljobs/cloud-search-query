<?php

namespace Kazak\CloudSearchQuery;

use Aws\CloudSearchDomain\CloudSearchDomainClient;
use Aws\CloudSearchDomain\Exception\CloudSearchDomainException;

class StructuredQueryBuilder {

    public $structuredQuery = [
        'queryParser' => 'structured'
    ];
    public $size = 10; // default per page
    public $start = 0; // default offset
    public $query = [];
    public $filterQuery = [];
    public $facets = [];
    public $expressions = [];
    public $stats;
    public $options;
    public $returnFields;
    public $sort;

    public function __construct()
    {
        //
    }

    public function __call($method, $args) {
        if (!method_exists($this, $method)) {
            throw new Exception("Method doesn't exist");
        }
        // escape string arguments
        foreach($args as $key => $value) {
            if (gettype($value) == "string") {
                $args[$key] = addslashes($value);
            }
        }
        call_user_func_array([$this, $method], $args);
        return $this;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getFilterQuery()
    {
        return $this->filterQuery;
    }

    public function size($size)
    {
        $this->size = $size;
        return $this;
    }

    public function start($start)
    {
        $this->start = $start;
        return $this;
    }

    public function returnFields($returnFields)
    {
        $this->returnFields = $returnFields;
        return $this;
    }

    public function options($options)
    {
        foreach(preg_split('/\s/', $options) as $field)
        {
            $this->options['fields'][] = $field;
        }

        return $this;
    }

    private function phrase($value, $field = null, $boost = null)
    {
        $phrase = "(phrase ";
        if ($field) {
            $phrase .= "field='{$field}' ";
        }
        if ($boost) {
            $phrase .= "boost='{$boost}' ";
        }
        $phrase .= "'{$value}')";
        $this->query[] = $phrase;
        return $this;
    }

    private function near($value, $field = null, $distance = 3)
    {
        $near = "(near ";
        if ($field) {
            $near .= "field='{$field}' ";
        }
        if ($distance) {
            $near .= "distance='{$distance}' ";
        }
        $near .= "'{$value}')";
        $this->query[] = $near;
        return $this;
    }

    private function term($value, $field = null, $boost = null)
    {
        $term = "(term ";
        if ($field) {
            $term .= "field='{$field}' ";
        }
        if ($boost) {
            $term .= "boost='{$boost}' ";
        }
        $term .= "'{$value}')";
        $this->query[] = $term;
        return $this;
    }

    private function prefix($value, $field = null, $boost = null)
    {
        $prefix = "(prefix ";
        if ($field) {
            $prefix .= "field='{$field}' ";
        }
        if ($boost) {
            $prefix .= "boost='{$boost}' ";
        }
        $prefix .= "'{$value}')";
        $this->query[] = $prefix;
        return $this;
    }

    public function range($field, $min, $max)
    {
        $range = "(range field={$field} ";
        if ($min and !$max) {
            $value = "[{$min},}";
        } elseif (!$min and $max) {
            $value = "{,{$max}]";
        } elseif ($min and $max) {
            $value = "[{$min},{$max}]";
        } else {
            return;
        }
        $range .= "{$value})";
        $this->query[] = $range;
        return $this;
    }

    public function qAnd($block)
    {
        $builder = new $this;
        $block($builder);
        $and = "(and ".implode('', $builder->getQuery()).")";
        $this->query[] = $and;
        return $this;
    }

    public function qOr($block)
    {
        $builder = new $this;
        $block($builder);
        $or = "(or ".implode('', $builder->getQuery()).")";
        $this->query[] = $or;
        return $this;
    }

    public function qNot($block)
    {
        $builder = new $this;
        $block($builder);
        $not = "(not ".implode('', $builder->getQuery()).")";
        $this->query[] = $not;
        return $this;
    }

    private function filterPhrase($value, $field = null, $boost = null)
    {
        $phrase = "(phrase ";
        if ($field) {
            $phrase .= "field='{$field}' ";
        }
        if ($boost) {
            $phrase .= "boost='{$boost}' ";
        }
        $phrase .= "'{$value}')";
        $this->filterQuery[] = $phrase;
        return $this;
    }

    private function filterTerm($value, $field = null, $boost = null)
    {
        $term = "(term ";
        if ($field) {
            $term .= "field='{$field}' ";
        }
        if ($boost) {
            $term .= "boost='{$boost}' ";
        }
        $term .= "'{$value}')";
        $this->filterQuery[] = $term;
        return $this;
    }

    private function filterPrefix($value, $field = null, $boost = null)
    {
        $prefix = "(prefix ";
        if ($field) {
            $prefix .= "field='{$field}' ";
        }
        if ($boost) {
            $prefix .= "boost='{$boost}' ";
        }
        $prefix .= "'{$value}')";
        $this->filterQuery[] = $prefix;
        return $this;
    }

    public function filterRange($field, $min, $max)
    {
        $range = "(range field={$field} ";
        if ($min and !$max) {
            $value = "[{$min},}";
        } elseif (!$min and $max) {
            $value = "{,{$max}]";
        } elseif ($min and $max) {
            $value = "[{$min},{$max}]";
        } else {
            return;
        }
        $range .= "{$value})";
        $this->filterQuery[] = $range;
        return $this;
    }

    public function filterAnd($block)
    {
        $builder = new $this;
        $block($builder);
        $and = "(and ".implode('', $builder->getFilterQuery()).")";
        $this->filterQuery[] = $and;
        return $this;
    }

    public function filterOr($block)
    {
        $builder = new $this;
        $block($builder);
        $or = "(or ".implode('', $builder->getFilterQuery()).")";
        $this->filterQuery[] = $or;
        return $this;
    }

    public function filterNot($block)
    {
        $builder = new $this;
        $block($builder);
        $not = "(not ".implode('', $builder->getFilterQuery()).")";
        $this->filterQuery[] = $not;
        return $this;
    }

    public function latlon($field, $lat, $lon, $radius = 50, $addExpr = false)
    {
        // upper left bound
        $lat1 = $lat + ($radius/69);
        $lon1 = $lon - $radius/abs(cos(deg2rad($lat))*69);
        // lower right bound
        $lat2 = $lat - ($radius/69);
        $lon2 = $lon + $radius/abs(cos(deg2rad($lat))*69);

        //$statment = "{$field}:['{$lat1},{$lon1}','{$lat2},{$lon2}']";
        //$this->filterQuery[] = $statment;
        $min = "'{$lat1},{$lon1}'";
        $max = "'{$lat2},{$lon2}'";
        $this->filterRange($field, $min, $max);
        if ($addExpr) {
            $this->addDistanceExpr($field, $lat, $lon);
        }
        return $this;
    }

    public function expr($accessor, $expression)
    {
        $this->expressions[$accessor] = $expression;
    }

    public function addDistanceExpr($field, $lat, $lon)
    {
        $expression = "haversin(".
          "{$lat},".
          "{$lon},".
          "{$field}.latitude,".
          "{$field}.longitude)";
        $this->expr("distance", $expression);
        return $this;
    }

    public function facet($field, $sort = "bucket", $size = 10)
    {
        $this->facets[$field] = [
            'sort' => $sort,
            'size' => $size
        ];
    }

    public function facetBuckets($field, $buckets, $method = "filter")
    {
        $this->facets[$field] = [
            'buckets' => $buckets,
            'method'  => $method
        ];
    }

    public function sort($field, $direction = 'asc')
    {
        $this->sort = "{$field} {$direction}";
        return $this;
    }

    public function stats($field)
    {
        $this->stats[] = $field;
    }

    public function buildStructuredQuery()
    {
        $structuredQuery;
        $structuredQuery['queryParser'] = 'structured';
        $structuredQuery['size'] = $this->size;
        $structuredQuery['start'] = $this->start;
        if ($this->query) {
            $structuredQuery['query'] = $this->buildQuery();
        }
        if ($this->filterQuery) {
            $structuredQuery['filterQuery'] = $this->buildFilterQuery();
        }
        if ($this->expressions) {
            $structuredQuery['expr'] = json_encode($this->expressions);
        }
        if ($this->facets) {
            $structuredQuery['facet'] = json_encode($this->facets);
        }
        if ($this->sort) {
            $structuredQuery['sort'] = $this->sort;
        }
        if ($this->returnFields) {
            $structuredQuery['return'] = $this->returnFields;
        }
        if ($this->options) {
            $structuredQuery['queryOptions'] = json_encode($this->options);
        }
        if ($this->stats) {
            $stats = [];
            foreach($this->stats as $statField) {
                $stats[] = "\"{$statField}\":{}";
            }
            $stats = "{".implode(',',$stats)."}";
            $structuredQuery['stats'] = $stats;
        }
        return $structuredQuery;
    }

    public function buildQuery()
    {
        return "(and ".implode('', $this->query).")";
    }

    public function buildFilterQuery()
    {
        return "(and ".implode('', $this->filterQuery).")";
    }

}
