
<?php
     function get_watch_results($video_id)
     {
         global $config;
 
         $mh = curl_multi_init();
 
         // Get directl URL to the video
         $direct_ch = curl_init("https://youtubei.googleapis.com/youtubei/v1/player?key=AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8");
         curl_setopt_array($direct_ch, $config->curl_settings);
         curl_setopt($direct_ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
         curl_setopt($direct_ch, CURLOPT_POST, 1);
         curl_setopt($direct_ch, CURLOPT_POSTFIELDS, "{
                 'context': {
                     'client': {
                         'hl': 'en',
                         'clientName': 'WEB',
                         'clientVersion': '2.20210721.00.00',
                         'mainAppWebInfo': {
                             'graftUrl': '/watch?v=$video_id'
                         }
                     }
                 },
                 'videoId': '$video_id'
         }");
         curl_multi_add_handle($mh, $direct_ch);
 
         // Get additional info (description, recommended videos etc.)
         $video_ch = curl_init("https://www.youtube.com/watch?v=$video_id&hl=en");
         curl_setopt_array($video_ch, $config->curl_settings);
         curl_multi_add_handle($mh, $video_ch);
         
         $running = null;
         do {
             curl_multi_exec($mh, $running);
         } while ($running);
 
         // Parse the response
         $video_response = curl_multi_getcontent($video_ch);
         get_video_results($video_response);

         $direct_response = curl_multi_getcontent($direct_ch);
         get_direct_url($direct_response);
     }


    function get_direct_url($json_response, $audio_only=false)
    {
        global $config , $results;

        $response = json_decode($json_response, true);
        $videos = array_merge($response["streamingData"]["formats"], $response["streamingData"]["adaptiveFormats"]);

        $video_url = null;

        do 
        {
            foreach ($videos as $video)
            {
                $type = $video["mimeType"];
                $quality = $video["quality"];

                if ($audio_only)
                {
                    if (strpos($type, "audio") !== false)
                        $video_url = $video["url"];
                }
                else if (strpos($type, "video") !== false && strpos($quality, $config->default_quality) !== false)
                    $video_url = $video["url"];
                        
                if ($video_url)
                    break;
            }

            $config->default_quality = ""; 
        } while ($video_url == null);

        $results[0]["direct_url"] = urlencode($video_url);
    }

    function get_video_results($response)
    {
        $data_part1 = explode("var ytInitialData = ", $response);
        $data_part2 = explode(";</script>", $data_part1[1]);
        $data = json_decode($data_part2[0], true);
        $raw_results = $data["contents"]["twoColumnWatchNextResults"]["secondaryResults"]["secondaryResults"]["results"];

        parse_main_video_info($data);
        parse_recommended_results($raw_results);
    } 
    
    function parse_main_video_info($data)
    {
        global $results;

        $main_info = $data["contents"]["twoColumnWatchNextResults"]["results"]["results"]["contents"];
        $video_info = $main_info[0]["videoPrimaryInfoRenderer"];
        $title = $video_info["title"]["runs"][0]["text"];
        $views = $video_info["viewCount"]["videoViewCountRenderer"]["viewCount"]["simpleText"];
        $date = $video_info["dateText"]["simpleText"];
        $likes =  $video_info["videoActions"]["menuRenderer"]["topLevelButtons"][0]["toggleButtonRenderer"]["defaultText"]["accessibility"]["accessibilityData"]["label"];
        $channel_info = $main_info[1]["videoSecondaryInfoRenderer"]["owner"]["videoOwnerRenderer"];
        $channel_name = $channel_info["title"]["runs"][0]["text"];
        $subscribers = $channel_info["subscriberCountText"]["simpleText"];
        $channel_thumbnail = urlencode($channel_info["thumbnail"]["thumbnails"][0]["url"]);
        $channel_id = $channel_info["navigationEndpoint"]["browseEndpoint"]["browseId"];
        $raw_description = $main_info[1]["videoSecondaryInfoRenderer"]["description"]["runs"];
        $description = "";
        foreach ($raw_description as $part)
        {
            $text = $part["text"];

            if (array_key_exists("navigationEndpoint", $part))
               $description .= "$text <br><br>";
            else
               $description .= $text . "<br><br>";
        }

        $result = array(
            "is_main_video" => true,
            "is_video" => true,
            "direct_url" => null,
            "title" => $title,
            "thumbnail" => $channel_thumbnail,
            "channel_id" => $channel_id,
            "channel_name" => $channel_name,
            "description" => $description,
            "views" => $views,
            "date" => $date,
            "likes" => $likes,
            "subscribers" => $subscribers
        );

        array_push($results, $result);  
    }

    function parse_recommended_results($raw_results)
    {
        global $results;
        
        foreach ($raw_results as $raw_result)
        {
            if (array_key_exists("compactVideoRenderer", $raw_result))
            {
                        
                $video = $raw_result["compactVideoRenderer"];
                $video_id = $video["videoId"];
                $thumbnail = $video["thumbnail"]["thumbnails"][1]["url"];
                $title = $video["title"]["simpleText"];
                $views = $video["shortViewCountText"]["simpleText"];
                $date = $video["publishedTimeText"]["simpleText"];

                $channel = $video["longBylineText"]["runs"][0];
                $channel_name = $channel["text"];
                $channel_id = $channel["navigationEndpoint"]["browseEndpoint"]["browseId"];

                $result = array(
                    "is_video" => true,
                    "video_id" => $video_id,
                    "title" => $title,
                    "thumbnail" => $thumbnail,
                    "channel_id" => $channel_id,
                    "channel_name" => $channel_name,
                    "views" => $views,
                    "date" => $date
                );

                array_push($results, $result);
             }
        }
    }
?>