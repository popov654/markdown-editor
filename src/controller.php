<?php

    class Controller {

        public static function getArticlesDirectory(): String {
            if (preg_match("/html$/", getcwd())) {
                return 'articles';
            }
            return '../articles';
        }

        public static function getArticlesList(): Array {
            $result = [];
            $directory = self::getArticlesDirectory();
            $dir = opendir($directory);
            while ($file = readdir($dir)) {
                if ($file == "." || $file == "..") continue;
                $result[] = self::parseArticle($file);
            }

            return $result;
        }

        public static function parseArticle(String $file): ?Array {
            $article = [];
            $article['href'] = $file;
            $directory = self::getArticlesDirectory();
            if (!file_exists($directory . '/' . $file)) {
                return null;
            }
            $content = file($directory . '/' . $file, FILE_IGNORE_NEW_LINES);
            $n = 0;
            if (preg_match("/^-{3,}/", $content[0])) {
                $n++;
                while ($n < count($content) && !preg_match("/^-{3,}/", $content[$n])) {
                    $str = $content[$n++];
                    $index = strpos($str, ":");
                    $key = substr($str,0, $index);
                    $value = substr($str, $index + 1);
                    $article[trim($key, " \n\r\t\v\"")] = trim($value, " \n\r\t\v\"");
                }
            }
            $article['text'] = "";

            $n++;
            while ($n < count($content) && preg_match("/^\\s*$/", $content[$n])) {
                $n++;
            }
            if ($n < count($content)) {
                $article['text'] = implode("\n", array_slice($content, $n));
            }

            return $article;
        }

        public static function saveArticle(Array $article, String $file) {
            $h = ['---'];
            foreach ($article as $key => $value) {
                if ($key == 'text') {
                    continue;
                }
                $h[] = $key .': "'. $value .'"';
            }
            $h[] = '---';
            $header = implode("\n", $h)."\n";
            $directory = self::getArticlesDirectory();
            file_put_contents($directory . '/'. $file, $header . "\n". $article['text']);
        }

    }