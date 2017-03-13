<?php
/**
 * Created by PhpStorm.
 * User: etulika
 * Date: 3/7/17
 * Time: 11:51 AM
 */

namespace Vrann\Adapter;

class Mysql {

    private $resource;

    public function __construct() {
        $dsn = 'mysql:host=localhost;dbname=pull_requests';
        $username = 'root';
        $password = 'root';
        $options = array(
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        );

        $this->resource = new \PDO($dsn, $username, $password, $options);
    }

    public function ingest($pullRequest) {
        $sql = 'INSERT INTO employee '.
            '(emp_name,emp_address, emp_salary, join_date) '.
            'VALUES ( "guest", "XYZ", 2000, NOW() )';
        $this->resource->exec($sql);
    }
}