<?php
class Newsletter_404 extends Plugin {
    private $host;

    function about() {
        return array(1.0,
            "404 media newsletter only show around the web",
            "mtfurlan");
    }

    function init($host) {
        $this->host = $host;

        $host->add_hook($host::HOOK_ARTICLE_FILTER, $this);
    }

    function hook_article_filter($article) {
        if (!str_contains($article["link"], "www.404media.co")) {
            return $article;
        }

        //Debug::log("404 media: " . $article["link"], Debug::$LOG_EXTENDED);
        //Debug::log(print_r($article, TRUE), Debug::$LOG_EXTENDED);

        $doc = new DOMDocument();
        if (!@$doc->loadHTML('<?xml encoding="UTF-8"><div id="nl404-root">' . $article["content"] . '</div>')) {
            return $article;
        }
        $xpath = new DOMXPath($doc);

        $marker = $xpath->query('//*[@id="around-the-web"]')->item(0);
        if (!$marker) {
            return $article;
        }
        $root = $xpath->query('//div[@id="nl404-root"]')->item(0);

        // walk up the node stack to the root deleting all previous siblings
        for ($node = $marker; $node && !$node->isSameNode($root); $node = $node->parentNode) {
            while ($node->previousSibling) {
                $node->parentNode->removeChild($node->previousSibling);
            }
        }

        // drop the "Around the web" heading
        $marker->parentNode->removeChild($marker);

        $article["content"] = '';
        foreach ($root->childNodes as $child) {
            $article["content"] .= $doc->saveHTML($child);
        }

        // remove attachemnts because they put the lead image in and if it's
        // not in the article it will get attached to the bottom
        $article["enclosures"] = [];

        $article["title"] = "Around The Web " . date("Y-m-d", $article["timestamp"] ?? time());

        if (!in_array("around the web", $article["tags"])) {
            $article["tags"][] = "around the web";
        }

        return $article;
    }

    function api_version() {
        return 2;
    }

}
?>
