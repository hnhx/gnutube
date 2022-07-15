
<?php
    function get_search_results($query)
    {
        global $config;

        $query = urlencode($query);
        $url = "https://www.youtube.com/results?search_query=$query&hl=en";

        $response = request($url);
        $data_part1 = explode("var ytInitialData = ", $response);
        $data_part2 = explode(";</script>", $data_part1[1]);
        $data = json_decode($data_part2[0], true);
        $raw_results = $data["contents"]["twoColumnSearchResultsRenderer"]["primaryContents"]["sectionListRenderer"]["contents"][0]["itemSectionRenderer"]["contents"];

        parse_search_results($raw_results);
    }

    function parse_search_results($raw_results)
    {
        global $results;
        
        foreach ($raw_results as $raw_result)
        {
            if (array_key_exists("videoRenderer", $raw_result))
            {
                $video = $raw_result["videoRenderer"];
                $video_id = $video["videoId"];
                $thumbnail = $video["thumbnail"]["thumbnails"][0]["url"];
                $title = $video["title"]["runs"][0]["text"];
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
            else if (array_key_exists("shelfRenderer", $raw_result))
            {
                $shelf_raw_results = $raw_result["shelfRenderer"]["content"]["verticalListRenderer"]["items"];
                parse_search_results($shelf_raw_results);
            }
            else if (array_key_exists("channelRenderer", $raw_result))
            {
                $channel = $raw_result["channelRenderer"];
                $channel_id = $channel["channelId"];
                $channel_name = $channel["title"]["simpleText"];
                $channel_thumbnail = $channel["thumbnail"]["thumbnails"][1]["url"];
                $fixed_channel_thumbnail = "https://" . substr($channel_thumbnail, 2);

                $result = array(
                    "is_video" => false,
                    "channel_id" => $channel_id,
                    "channel_name" => $channel_name,
                    "thumbnail" => $fixed_channel_thumbnail
                );

                array_push($results, $result);
            }
        }
    }
?>