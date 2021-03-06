<?php

namespace SA;

use Aws\Credentials\CredentialProvider;
use Aws\Signature\SignatureV4;
use Elasticsearch\ClientBuilder;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Ring\Future\CompletedFutureArray;
use Psr\Http\Message\ResponseInterface;

class ElasticsearchHandler {
    private $client;

    public function __construct($endpoints) {
        $psr7Handler = \Aws\default_http_handler();
        $signer = new SignatureV4("es", $_SERVER['AWS_DEFAULT_REGION']);
        $credentialProvider = CredentialProvider::defaultProvider();

        $handler = function(array $request) use($psr7Handler, $signer, $credentialProvider, $endpoints) {
            // Amazon ES listens on standard ports (443 for HTTPS, 80 for HTTP).
            $request['headers']['host'][0] = parse_url($request['headers']['host'][0], PHP_URL_HOST);

            // Create a PSR-7 request from the array passed to the handler
            $psr7Request = new Request(
                $request['http_method'],
                (new Uri($request['uri']))
                    ->withScheme($request['scheme'])
                    ->withHost($request['headers']['host'][0]),
                $request['headers'],
                $request['body']
            );

            // Sign the PSR-7 request with credentials from the environment
            $signedRequest = $signer->signRequest(
                $psr7Request,
                call_user_func($credentialProvider)->wait()
            );

            // Send the signed request to Amazon ES
            /** @var ResponseInterface $response */
            $response = $psr7Handler($signedRequest)->then(
                function(\Psr\Http\Message\ResponseInterface $r) {
                    return $r;
                }, function($error) {
                    return $error['response'];
                }
            )->wait();

            // Convert the PSR-7 response to a RingPHP response
            return new CompletedFutureArray([
                "status" => $response->getStatusCode(),
                "headers" => $response->getHeaders(),
                "body" => $response->getBody()->detach(),
                "transfer_stats" => ["total_time" => 0],
                "effective_url" => (string) $psr7Request->getUri(),
            ]);
        };

        $this->client = ClientBuilder::create()
            ->setHandler($handler)
            ->setHosts($endpoints)
            ->allowBadJSONSerialization()
            ->build();
    }

    public function aggregate($index, $query, $data, $type = null) {
        $params = [
            "index" => $index,
            "type" => $index,
            "q" => $query,
        ];

        if($type != null)
            $params['type'] = $type;

        $body = [];
        foreach($data as $k => $v) {
            $name = $k;
            $type = $v['type'];
            $field = $v['field'];
            $body[$name] = [
                $type => [
                    "field" => $field,
                ],
            ];
        }

        $params['body'] = ["aggs" => $body];

        return $this->client->search($params)['aggregations'];
    }

    public function count($index, $query, $type = null) {
        $params = [
            "index" => $index,
            "type" => $index,
            "q" => $query,
        ];

        if($type != null)
            $params['type'] = $type;

        return $this->client->count($params)['count'];
    }

    public function createDocument($index, $data, $id = null) {
        $params = [
            "index" => $index,
            "type" => $index,
            "body" => $data,
        ];

        if($id != null)
            $params['id'] = $id;

        return $this->client->index($params);
    }

    public function createIndex($index) {
        $params = [
            "index" => $index,
        ];

        return $this->client->indices()->create($params);
    }

    public function deleteDocument($index, $id) {
        $params = [
            "index" => $index,
            "type" => $index,
            "id" => $id,
        ];

        return $this->client->delete($params);
    }

    public function deleteIndex($index) {
        $params = [
            "index" => $index,
        ];

        return $this->client->indices()->delete($params);
    }

    public function indexExists($index) {
        $params = [
            "index" => $index,
        ];

        return $this->client->indices()->exists($params);
    }

    public function indices() {
        return $this->client->indices()->stats();
    }

    public function query($index, $query, $count = 1, $sort = null, $offset = 0, $type = null) {
        $results = $this->raw($index, $query, $count, $sort, $offset, $type);

        $results = array_map(function($item) {
            return $item['_source'];
        }, $results['hits']['hits']);

        return $results;
    }

    public function raw($index, $query, $count = 1, $sort = null, $offset = 0, $type = null) {
        $params = [
            "index" => $index,
            "type" => $index,
            "from" => $offset,
            "q" => $query,
            "size" => $count,
        ];

        if($type != null)
            $params['type'] = $type;

        if($sort != null && $sort != "")
            $params['sort'] = $sort;

        $results = $this->client->search($params);

        return $results;
    }

    public function scan($index, $query, $type = null) {
        $params = [
            "body" => [
                "sort" => [
                    ["_uid" => "asc"],
                ],
            ],
            "index" => $index,
            "q" => $query,
            "size" => 10000,
            "type" => $index,
        ];

        if($type != null)
            $params['type'] = $type;

        $results = [];
        $total = 0;
        do {
            $temp = $this->client->search($params);
            $total = $temp['hits']['total'];
            $hits = $temp['hits']['hits'];
            $last = end($hits);

            $results = array_merge($results, $hits);

            $params['body']['search_after'] = $last['sort'];
        } while(count($results) < $total);

        return $results;
    }
}
