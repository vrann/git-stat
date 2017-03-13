<?php
// This file is generated by Composer
require_once 'vendor/autoload.php';

$client = new \Github\Client();
$token = "";
if (!file_exists(".githubtoken")) {
    throw new Exception("Create file .githubtoken and put valid GitHub token there");
} else {
    $token = file_get_contents(".githubtoken");
}
/** @var \Github\Api\PullRequest $pullRequests */
$client->authenticate($token, null, \Github\Client::AUTH_HTTP_TOKEN);

$githubApiClient = new \Vrann\Adapter\GitHub($client);

$filter = [
    "range"=> [
        "closed_at" => [
            "gte" => 1485928800000,
            "lt" => 1488348000000,
            "format" => "epoch_millis"
        ]
    ]
];

$esAdapter = new \Vrann\Adapter\ElasticSearch();
$prs = $esAdapter->getFiltered($filter);

$users = [];
foreach ($prs as $prData) {
    $issues = [];
    $pr = $prData['_source'];
    if (isset($users[$pr['user']['login']])) {
        $users[$pr['user']['login']]++;
    } else {
        $users[$pr['user']['login']] = 1;
    }
//    $matches = [];
//    preg_match('/.*(\#\d+)/', $pr['title'], $matches);
//    if (count($matches) > 0) {
//        $issues[] = $matches[1];
//    }
//
//    $matches = [];
//    preg_match('/.*(\#\d+)/', $pr['body'], $matches);
//    if (count($matches) > 0) {
//        $issues[] = $matches[1];
//    }
//    $comments = $githubApiClient->getAll(
//        '/repos/magento/magento2/issues/' . $pr['number'] . '/comments'
//    );
//
//    $pr['comments_number'] = count($comments);
//    foreach ($comments as $comment) {
//        $matches = [];
//        preg_match('/.*(\#\d+)/', $comment['body'], $matches);
//        if (count($matches) > 0) {
//            $issues[] = $matches[1];
//        }
//    }
//
//    $reactionsCount = 0;
//    $issues = array_unique($issues);
//    foreach ($issues as $issue) {
//        $reactions = $githubApiClient->getAll(
//            '/repos/magento/magento2/issues/' . str_replace('#', '', $issue) . '/reactions',
//            ['content' => '+1'],
//            ["Accept" => "application/vnd.github.squirrel-girl-preview"]
//        );
//        $reactionsCount += count($reactions);
//
//        $issueComments = $githubApiClient->getAll(
//            '/repos/magento/magento2/issues/' . str_replace('#', '', $issue). '/comments'
//        );
//        foreach ($issueComments as $comment) {
//            $reactions = $githubApiClient->getAll(
//                '/repos/magento/magento2/issues/comments/' . $comment['id'] . '/reactions',
//                ['content' => '+1'],
//                ["Accept" => "application/vnd.github.squirrel-girl-preview"]
//            );
//            $reactionsCount += count($reactions);
//        }
//
//    }
//    $pr['issue_upvotes_number'] = $reactionsCount;
//    echo $pr['number'] . " " . $reactionsCount . "\n";
//    $esAdapter->ingest($pr);
}
arsort($users);
foreach ($users as $name =>  $userPRs) {
    echo $name . "\t" . $userPRs . "\n";
}

//foreach ($issues as $issue) {
//    $reactions = $githubApiClient->getAll(
//        '/repos/magento/magento2/issues/' . str_replace('#', '', $issue). '/reactions',
//        ['content' => '+1'],
//        ["Accept" => "application/vnd.github.squirrel-girl-preview"]
//    );
//    var_dump($issue, count($reactions));

//    $comments = $githubApiClient->getAll(
//        '/repos/magento/magento2/issues/' . str_replace('#', '', $issue). '/comments'
//    );
//    var_dump(count($comments));
//}
