 
<?php
        require "static/header.xhtml";

            $config = require "config.php";
            require "misc/tools.php";
            require "results/channel_results.php";

            $channel_id = $_REQUEST["id"];
            $results = array();
            get_channel_results($channel_id);

            $channel_info = $results[0];

            echo "<img class=\"banner\" src=\"/image_proxy.php?url=" . $channel_info["channel_banner"] . "\" alt=\"banner\"/ >";
            echo "<p class=\"small-channel\"><img src=\"/image_proxy.php?url=" . $channel_info["thumbnail"] . "\" width=\"50\" height=\"40\"><a href=\"channel.php?id=" . $channel_info["channel_id"] . "\">" . $channel_info["channel_name"] . "</a> (" . $channel_info["subscribers"] . ")</p>";

            echo "<div class=\"video-results\">";
            foreach ($results as $result)
            {
                if (!$result["is_video"])
                    continue;
                
                echo "<div>";
                echo "<a href=\"watch.php?v=" . $result["video_id"] . "\">";
                echo "<img src=\"/image_proxy.php?url=" . $result["thumbnail"] . "\" />";
                echo "<p>" . $result["title"] . "</p>";
                echo "</a>";
                echo "<small class=\"channel-name\">" . $result["date"] . " - " . $result["views"] . "</a></small>";
                echo "</div>";
            }
            echo "</div>";

        require "static/footer.xhtml";
?>