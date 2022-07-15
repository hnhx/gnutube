
<?php
        require "static/header.xhtml"; 

                $config = require "config.php";
                require "misc/tools.php";
                require "results/watch_results.php";

                $video_id = $_REQUEST["v"];

                $results = array();
                get_watch_results($video_id);

                $main_vid = $results[0];

                echo "<video controls autoplay>";
                if ($config->proxy_videos)
                    echo "<source src=\"/video_proxy.php?url=" . $main_vid["direct_url"] . "\" type=\"video/mp4\">";
                else
                    echo "<source src=\"" . urldecode($main_vid["direct_url"]) . "\" type=\"video/mp4\">";
                echo "</video>";
                echo "<h3>" . $main_vid["title"] . "</h3>";
                echo "<p>" . $main_vid["date"] . " - " . $main_vid["views"] . " - " . $main_vid["likes"] . "</p>";
                echo "<p class=\"small-channel\"><img src=\"/image_proxy.php?url=" . $main_vid["thumbnail"] . "\" width=\"50\" height=\"40\"><a href=\"channel.php?id=" . $main_vid["channel_id"] . "\">" . $main_vid["channel_name"] . "</a> (" . $main_vid["subscribers"] . ")</p>";
                //echo "<p class=\"description\">" . $main_vid["description"] . "</p>";

                echo "<div class=\"video-results\">";
                foreach ($results as $result)
                {
                    if (array_key_exists("is_main_video", $result))
                        continue;

                    echo "<div>";
                    echo "<a href=\"watch.php?v=" . $result["video_id"] . "\">";
                    echo "<img src=\"/image_proxy.php?url=" . $result["thumbnail"] . "\" />";
                    echo "<p>" . $result["title"] . "</p>";
                    echo "</a>";
                    echo "<span class=\"channel-name\"><a href=\"channel.php?id=" . $result["channel_id"] . "\">" . $result["channel_name"] . "</a> - " . $result["date"] . " - " . $result["views"] . "</span>";
                    echo "</div>";
                }
                echo "</div>";

        require "static/footer.xhtml";
?>