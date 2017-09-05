<?php
include 'class.YabsHandler.php';

// get the data in JSON format
$posts = YabsHandler::get_posts('underdog-projects');
$posts = (array)$posts['bookmarks'];

// view first 6 entries
$i = 0;
$c = count($posts);
for(;$i < $c && $i < 6;$i++)
    echo var_dump((array)$posts[$i]);
