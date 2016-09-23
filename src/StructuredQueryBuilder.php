<?php

namespace AaronKaz\CloudSearchQuery;

use Aws\CloudSearchDomain\CloudSearchDomainClient;
use Aws\CloudSearchDomain\Exception\CloudSearchDomainException;

class StructuredQueryBuilder {


    private $structuredQuery = [
        'queryParser' => 'structured'
    ];
    private $size = 10; // default per page
    private $start = 0; // default offset
    public $query = [];
    private $returnFields;

    public function __construct()
    {

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

    public function phrase($value, $field = null, $boost = null)
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

    public function term($value, $field = null, $boost = null)
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

    public function prefix($value, $field = null, $boost = null)
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

    public function and($block)
    {
        $builder = new $this;
        $block($builder);
        $and = "(and ".implode('', $builder->query).")";
        $this->query[] = $and;
        return $this;
    }

    public function or($block)
    {
        $builder = new $this;
        $block($builder);
        $or = "(or ".implode('', $builder->query).")";
        $this->query[] = $or;
        return $this;
    }

    public function not($block)
    {
        $builder = new $this;
        $block($builder);
        $not = "(not ".implode('', $builder->query).")";
        $this->query[] = $not;
        return $this;
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
        return $structuredQuery;
    }

    public function buildQuery()
    {
        return "(and ".implode('', $this->query).")";
    }

}
