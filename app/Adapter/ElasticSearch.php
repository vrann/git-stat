<?php
/**
 * Created by PhpStorm.
 * User: etulika
 * Date: 3/7/17
 * Time: 11:51 AM
 */

namespace Vrann\Adapter;
use Elasticsearch\ClientBuilder;

class ElasticSearch {

    private $resource;

    public function __construct() {
        $this->resource = ClientBuilder::create()->build();
    }

    /**
     * Push pull request json to the ElasticSearch
     *
     * @param $pullRequest
     */
    public function ingest($pullRequest) {
        $this->resource->index([
            'index' => 'get_pull_requests',
            'type' => 'pull_request',
            'id' => $pullRequest['id'],
            'body' => $pullRequest
        ]);
    }

    public function getFiltered($filter) {
        $params = [
            "scroll" => "30s",
            "size" => 50,
            'index' => 'get_pull_requests',
            'type' => 'pull_request',
            'body' => [
                "query" => $filter
            ]
        ];
        $docs = $this->resource->search($params);
        return $this->loadDocuments($docs);
    }

    public function get() {
        $params = [
            "scroll" => "30s",
            "size" => 50,
            'index' => 'get_pull_requests',
            'type' => 'pull_request',
            'body' => [
                "query" => [
                    "match_all" => new \stdClass()
                ]
            ]
        ];
        $docs = $this->resource->search($params);
        return $this->loadDocuments($docs);
    }

    private function loadDocuments($docs)
    {
        $scroll_id = $docs['_scroll_id'];   // The response will contain no results, just a _scroll_id
        $prs = [];
        // Now we loop until the scroll "cursors" are exhausted
        while (true) {

            // Execute a Scroll request
            $response = $this->resource->scroll([
                    "scroll_id" => $scroll_id,  //...using our previously obtained _scroll_id
                    "scroll" => "30s"           // and the same timeout window
                ]
            );

            // Check to see if we got any search hits from the scroll
            if (count($response['hits']['hits']) > 0) {
                // If yes, Do Work Here

                // Get new scroll_id
                // Must always refresh your _scroll_id!  It can change sometimes
                foreach ($response['hits']['hits'] as $hit) {
                    $prs[] = $hit;
                }
                $scroll_id = $response['_scroll_id'];
            } else {
                // No results, scroll cursor is empty.  You've exported all the data
                break;
            }
        }
        return $prs;
    }
}