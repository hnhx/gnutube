
<?php
    function get_channel_results($channel_id)
    {
        global $config;

        $url = "https://www.youtube.com/channel/$channel_id/videos?hl=en";

        $response = file_get_contents($url);
        $data_part1 = explode("var ytInitialData = ", $response);
        $data_part2 = explode(";</script>", $data_part1[1]);
        $data = json_decode($data_part2[0], true);
        $raw_results = $data["contents"]["twoColumnBrowseResultsRenderer"]["tabs"][1]["tabRenderer"]["content"]["sectionListRenderer"]["contents"];

        parse_channel_header($data);
        parse_channel_results($raw_results);

        
    }

    function parse_channel_header($data)
    {
        global $results;

        $header = $data["header"]["c4TabbedHeaderRenderer"];
        $channel_name = $header["title"];
        $channel_id = $header["channelId"];
        $channel_thumbnail = $header["avatar"]["thumbnails"][0]["url"];
        $channel_banner = $header["banner"]["thumbnails"][0]["url"];
        $subscribers = $header["subscriberCountText"]["simpleText"];

        $result = array(
            "is_video" => false,
            "channel_name" => $channel_name,
            "channel_id" => $channel_id,
            "thumbnail" => $channel_thumbnail,
            "channel_banner" => $channel_banner,
            "subscribers" => $subscribers
        );

        array_push($results, $result);
    }

    function parse_channel_results($raw_results)
    {
        global $results;
        
        foreach ($raw_results as $raw_result)
        {
            $shelf_raw_results = $raw_result["itemSectionRenderer"]["contents"][0]["gridRenderer"]["items"];
            foreach ($shelf_raw_results as $shelf_raw_result)
            {
                if (array_key_exists("gridVideoRenderer", $shelf_raw_result))
                {
                        
                        $video = $shelf_raw_result["gridVideoRenderer"];
                        $video_id = $video["videoId"];
                        $thumbnail = $video["thumbnail"]["thumbnails"][0]["url"];
                        $title = $video["title"]["runs"][0]["text"];
                        $views = $video["viewCountText"]["simpleText"];
                        $date = $video["publishedTimeText"]["simpleText"];

                        $result = array(
                            "is_video" => true,
                            "video_id" => $video_id,
                            "title" => $title,
                            "thumbnail" => $thumbnail,
                            "views" => $views,
                            "date" => $date
                        );

                        array_push($results, $result);
                }
            }
        }
    }
?>