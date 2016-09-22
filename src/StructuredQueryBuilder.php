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
    private $query = [];
    private $returnFields;

    public function __construct()
    {

    }

    public function setSize($size)
    {
        $this->size = $size;
    }

    public function setStart($start)
    {
        $this->start = $start;
    }

    public function setReturnFields($returnFields)
    {
        $this->returnFields = $returnFields;
    }

    public function addPhrase($value, $field = null, $boost = null)
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
    }

    public function addTerm($value, $field = null, $boost = null)
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
    }

    public function addPrefix($value, $field = null, $boost = null)
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
    }

    public function addRange($field, $min, $max)
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
