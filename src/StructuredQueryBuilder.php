<?php

namespace AaronKaz\CloudSearchQuery;

use Aws\CloudSearchDomain\CloudSearchDomainClient;

class StructuredQueryBuilder {

    private $client;
    private $structuredQuery = [
        'queryParser' => 'structured'
    ];
    private $query = [];

    public function __construct(CloudSearchDomainClient $client)
    {
        $this->client = $client;
    }

    public function addPhrase($phrase)
    {
        $this->query[] = "(phrase '{$phrase}')";
    }

    public function getResults()
    {
        $this->structuredQuery['query'] = implode('', $this->query);
        $response = $this->client->search($this->structuredQuery);
        return $response;
        if ($response['@metadata']['statusCode'] == "200") {
          $this->jobs = $response['hits'];
          if (!empty($response['facets'])) {
              $this->facets = $response['facets'];
          }
        }
    }

}
