<?php

    require "controller.php";

    if (!isset($_GET['action'])) {
        $_GET['action'] = 'get';
    }

    switch ($_GET['action']) {
        case 'get':
            if (isset($_GET['article'])) {
                // Sanitize user input
                $file = preg_replace("/[^0-9a-z\._-]/i", "", $_GET['article']);
                $article = Controller::parseArticle($file);
                echo $article['text'];
            } else {
                echo "Not found.";
            }
            break;
        case 'update':
            if (!isset($_POST['text'])) {
                return;
            }
            // Support POST and GET
            if (isset($_REQUEST['article'])) {
                // Sanitize user input
                $file = preg_replace("/[^0-9a-z\._-]/i", "", $_REQUEST['article']);
                $article = Controller::parseArticle($file);
                if (isset($_POST['title']) && !preg_match("/^\\s*$/", $_POST['title'])) {
                    $article['title'] = trim(preg_replace("/[\\v\\t]/", "", $_POST['title']));
                }
                $article['text'] = trim($_POST['text']);
                $article['modified_on'] = date("Y-m-d H:i:s");
                Controller::saveArticle($article, $file);
                echo "Success";
            } else {
                echo "Not found.";
            }
            break;
    }

?>