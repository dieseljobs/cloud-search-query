<?php

namespace Aaronkaz\CloudSearchQuery;

use Aws\Result;

class CloudSearchQueryResults
{

    private $awsResult;
    public $status;
    public $found;
    public $start;
    public $hits;
    public $facets;

    public function __construct(Result $result)
    {
        $this->awsResult = $result;
    }

    public function __toString()
    {
        $arr = [
            'status' => $this->status,
            'found'  => $this->found,
            'start'  => $this->start,
            'hits'   => $this->hits,
            'facets' => $this->facets
        ];
        return json_encode($arr);
    }

    public function map()
    {
        $this->status = $this->awsResult['@metadata']['statusCode'];
        if ($this->status == '200') {
          $this->found = $this->awsResult['hits']['found'];
          $this->start = $this->awsResult['hits']['start'];
          $this->mapHits();
          if ($this->awsResult['facets']) {
              // we should map these out better
              $this->facets = $this->awsResult['facets'];
          }
        }
        return $this;
    }

    public function mapHits()
    {
        $hits = [];
        foreach($this->awsResult['hits']['hit'] as $hit) {
            $mappedHit = [
                'id' => $hit['id']
            ];
            foreach($hit['fields'] as $key => $field) {
                $mappedHit[$key] = $field[0];
            }
            $hits[] = $mappedHit;
        }
        $this->hits = $hits;
    }
}
