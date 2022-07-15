<?php
        require "static/header.xhtml"; 

            $config = require "config.php";
            require "misc/tools.php";
            require "results/search_results.php";

            $query = $_REQUEST["q"];
            $results = array();
            get_search_results($query);
            
            if (empty($query))
            {
               echo "<p>No popular video feed here, don't be a consoomer.</p>";
            }
            else
            {
                echo "<div class=\"video-results\">";
                foreach ($results as $result)
                {
                    echo "<div>"; 
                    if ($result["is_video"])
                    {
                        echo "<a href=\"watch.php?v=" . $result["video_id"] . "\">";
                        echo "<img src=\"/image_proxy.php?url=" . $result["thumbnail"] . "\" />";
                        echo "<p>" . $result["title"] . "</p>";
                        echo "</a>";
                        echo "<small class=\"channel-name\"><a href=\"channel.php?id=" . $result["channel_id"] . "\">" . $result["channel_name"] . "</a> - " . $result["date"] . " - " . $result["views"] . "</small>";
                    }
                    else
                    {
                        echo "<a href=\"channel.php?id=" . $result["channel_id"] . "\">";
                        echo "<img src=\"/image_proxy.php?url=" . $result["thumbnail"] . "\" />";
                        echo "<p>" . $result["channel_name"] . "</p></a>"; 
                    }
                    echo "</div>";
                }
                echo "</div>";
            }
        
        require "static/footer.xhtml";
?>
