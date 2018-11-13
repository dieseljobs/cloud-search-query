<?php

namespace TheLHC\CloudSearchQuery;

use Aws\Result;

class CloudSearchQueryResults
{

    private $awsResult;
    public $status;
    public $found;
    public $start;
    public $cursor;
    public $hits;
    public $facets;
    public $stats;

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
            'cursor' => $this->cursor,
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
          if (isset($this->awsResult['hits']['cursor'])) {
              $this->cursor = $this->awsResult['hits']['cursor'];
          }
          $this->mapHits();
          if ($this->awsResult['facets']) {
              // we should map these out better
              $this->facets = $this->awsResult['facets'];
          }
          if ($this->awsResult['stats']) {
              $this->stats = $this->awsResult['stats'];
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
            if (isset($hit['fields'])) {
                foreach($hit['fields'] as $key => $field) {
                    $mappedHit[$key] = $field[0];
                }
            }
            if (isset($hit['exprs']['match']) &&
                isset($hit['exprs']['distance'])
            ) {
                $mappedHit['match'] = $hit['exprs']['match'];
                $mappedHit['distance'] = $hit['exprs']['distance'];
            }
            $hits[] = $mappedHit;
        }
        $this->hits = $hits;
    }
}
