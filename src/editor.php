<?php

use Diff\ArrayComparer\StrictArrayComparer;

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
            $oldText = $article['text'];
            $stats1 = Controller::getStats($oldText);
            $newText = trim($_POST['text']);
            $stats2 = Controller::getStats($newText);
            $article['text'] = $newText;
            $article['modified_on'] = date("Y-m-d H:i:s");
            Controller::saveArticle($article, $file);
            
            $line = "[".date("Y-m-d H:i:s")."] Updated article $file: ";
            foreach ($stats1 as $key => $value) {
                if (is_array($stats2[$key])) continue;
                $delta = $stats2[$key] - $stats1[$key];
                $sign = $delta > 0 ? '+' : ($delta < 0 ? '-' : '');
                $a = explode('\\', $key);
                $line .= $sign . abs($delta) . ' ' . strtolower(array_pop($a)) . 's, ';
            }
            $line = substr($line, 0, -2);

            $comparer = new StrictArrayComparer();
            $wordsAdded = count($comparer->diffArrays($stats2['words'], $stats1['words']));
            $wordsRemoved = count($comparer->diffArrays($stats1['words'], $stats2['words']));

            $line .= "; added $wordsAdded word(s), removed $wordsRemoved word(s)";

            $logPath = Controller::getArticlesDirectory() . '../audit.log';
            file_put_contents($logPath, $line . "\n", FILE_APPEND | LOCK_EX);

            echo '{"result": "success", "timestamp": "'.$article['modified_on'].'"}';
        } else {
            echo "Not found.";
        }
        break;
}

?>