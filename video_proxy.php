<?php
    $config = require "config.php";
    if (!$config->proxy_videos)
    {
        echo "Sorry but the host disabled this feature!";
        die();
    }
        

    header("Content-Type: video/mp4");
    ini_set("memory_limit", "1024M");
    set_time_limit(3600);

    $url = urldecode($_GET["url"]);
    
    ob_start();
    
    $opts["http"]["header"] = "Range: " . $_SERVER["HTTP_RANGE"];
    $opts["http"]["method"] = "HEAD";
    $conh=stream_context_create($opts);
    $opts["http"]["method"] = "GET";
    $cong = stream_context_create($opts);
    $out[ ]= file_get_contents($url, false, $conh);
    $out[] = $http_response_header;
    
    ob_end_clean();
    
    array_map("header", $http_response_header);
    
    readfile($url, false, $cong);
?>