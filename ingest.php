<?php

// This file is generated by Composer
require_once 'vendor/autoload.php';



$stat = prstat();
krsort($stat);
echo '<table border="1">';
echo "<tr><th>Week</th><th>Opened</th><th>Accepted</th><th>Rejected</th>";
foreach ($stat as $week => $weekStat) {
    echo "<tr><td>" . $week . "</td>";
    echo "<td>";
    if (isset($weekStat['opened'])) {
        echo count($weekStat['opened']);
    } else {
        echo 0;
    }

    echo "</td><td>";
    if (array_key_exists('accepted', $weekStat)) {
        echo count($weekStat['accepted']);
    } else {
        echo 0;
    }
    echo  "</td><td>";
    if (isset($weekStat['rejected'])) {
        echo count($weekStat['rejected']);
    } else {
        echo 0;
    }
    echo "</td></tr>";
}
echo "</table>";

function prstat() {

    $esAdapter = new \Vrann\Adapter\ElasticSearch();
    $mysqlAdapter = new \Vrann\Adapter\Mysql();

    $client = new \Github\Client();

    $token = "";
    if (!file_exists(".githubtoken")) {
        throw new Exception("Create file .githubtoken and put valid GitHub token there");
    } else {
        $token = file_get_contents(".githubtoken");
    }
    /** @var \Github\Api\PullRequest $pullRequests */
    $client->authenticate($token, null, \Github\Client::AUTH_HTTP_TOKEN);
    $pullRequests = $client->api('pull_requests');

    $page = 1;
    $prs = $pullRequests->all('magento', 'magento2',
        ['page' => $page]
    );
    //weeks
    //$dim = 'W';
    //months
    $dim = 'm';

    while (count($prs) > 0) {
        foreach ($prs as $pr) {
            //$esAdapter->ingest($pr);
            ///$mysqlAdapter->ingest($pr);

            $year = date('Y', strtotime($pr['created_at']));
            $dimNum = date($dim, strtotime($pr['created_at']));
            $num = $year . '-' . $dimNum;
            $stat[$num]['opened'][] = $pr['number'];

        }
        $page++;
        $prs = $pullRequests->all('magento', 'magento2',
            ['page' => $page]
        );
    }


    $page = 1;
    $prs = $pullRequests->all('magento', 'magento2',
        ['state' => 'closed', 'page' => $page]
    );
    while (count($prs) > 0) {
        foreach ($prs as $pr) {
            //$esAdapter->ingest($pr);
            //$mysqlAdapter->ingest($pr);

            $year = date('Y', strtotime($pr['created_at']));
            $dimNum = date($dim, strtotime($pr['created_at']));
            $num = $year . '-' . $dimNum;
            $stat[$num]['opened'][] = $pr['number'];

            $year = date('Y', strtotime($pr['closed_at']));
            $dimNum = date($dim, strtotime($pr['closed_at']));
            $num = $year . '-' . $dimNum;
            if ($pr['merged_at'] == null) {
                //rejected
                $stat[$num]['rejected'][] = $pr['number'];
            } else {
                //accepted
                $stat[$num]['accepted'][] = $pr['number'];
            }
        }
        $page++;
        $prs = $pullRequests->all('magento', 'magento2',
            ['state' => 'closed', 'page' => $page]
        );
    }

    return $stat;
}

