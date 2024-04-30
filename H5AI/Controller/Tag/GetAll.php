<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/Model/Tag.php';

$tag = new Tag();

class GetAll
{
        public function __construct()
        {
                global $tag;
                $tags = $tag->getTags();

                if($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($tags))
                {
                        header('Content-Type: application/json');
                        echo json_encode($tags);
                }
        }
}

new GetAll();