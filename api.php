<?php
    $config = require "config.php";
    require "misc/tools.php";

    $query = $_REQUEST["q"];
    $type = isset($_REQUEST["type"]) ? (int) $_REQUEST["type"] : 0;

    $results = array();

    switch ($type)
    {
        case 0:
            require "results/search_results.php";
            get_search_results($query);
            break;
        case 1:
            require "results/channel_results.php";
            get_channel_results($query);
            break;
        case 2:
            require "results/watch_results.php";
            get_watch_results($query);
            break;
        default:
            require "results/search_results.php";
            get_search_results($query);
            break;
    }

    header("Content-Type: application/json");
    echo json_encode($results, JSON_PRETTY_PRINT);
?>