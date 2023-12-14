<?php

require '../vendor/autoload.php';

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\DisallowedRawHtml\DisallowedRawHtmlExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Extension\Table\Table;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\TaskList\TaskListExtension;
use League\CommonMark\Node\Query;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Node\Block\Document;

class Controller {

    public static function getArticlesDirectory(): String {
        if (preg_match("/html$/", getcwd())) {
            return 'articles/';
        }
        return '../articles/';
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
        if (!file_exists($directory . $file)) {
            return null;
        }
        $content = file($directory . $file, FILE_IGNORE_NEW_LINES);
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
        file_put_contents($directory . $file, $header . "\n". $article['text']);
    }

    public static function getStats(String $text, Array $nodeTypes = [Link::class, Image::class, Table::class]): Array {
        $stats = [];
        $config = [];

        // Configure the Environment with all the CommonMark parsers/renderers
        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());

        // Remove any of the lines below if you don't want a particular feature
        $environment->addExtension(new AutolinkExtension());
        $environment->addExtension(new DisallowedRawHtmlExtension());
        $environment->addExtension(new StrikethroughExtension());
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new TaskListExtension());

        $parser = new MarkdownParser($environment);
        $document = $parser->parse($text);

        foreach ($nodeTypes as $type) {
            $matchingNodes = (new Query())->where(Query::type($type))->findAll($document);
            $count = 0;
            foreach ($matchingNodes as $node) {
                $count++;
            }
            $stats[$type] = $count;
        }

        return $stats;
    }

    public static function getStringContent(Document $document): String {
        $text = "";
        foreach ($document->iterator() as $node) {
            if ($node instanceOf League\CommonMark\Node\Inline\Text) {
                $text .= $node->getLiteral() . ' ';
            }
        }

        return $text;
    }

    public static function stripTags(String $markup) {
        return preg_replace("/(<a-z+(\\s+[a-z-]+[a-z]+(=\"[^\"]*\")?)*\\s*\\/?>|<\\/[a-z]+>|<!--.*-->)/", ' ', $markup);
    }

}