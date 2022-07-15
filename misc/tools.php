<?php
    function request($url)
    {
        global $config;

        $ch = curl_init($url);
        curl_setopt_array($ch, $config->curl_settings);

        $response = curl_exec($ch);

        return $response;
    }
?>