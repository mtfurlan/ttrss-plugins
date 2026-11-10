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

    private function is_newsletter($article) {
        if (!str_contains($article["link"] ?? "", "www.404media.co")) {
            return false;
        }
        foreach ($article["tags"] ?? [] as $tag) {
            // they have used "newsletters" and "daily newsletter"
            if (str_contains(mb_strtolower(trim($tag)), "newsletter")) {
                return true;
            }
        }
        return false;
    }

    function hook_article_filter($article) {
        if (!$this->is_newsletter($article)) {
            return $article;
        }

        //Debug::log("404 media newsletter: " . $article["link"], Debug::$LOG_EXTENDED);
        //Debug::log(print_r($article, TRUE), Debug::$LOG_EXTENDED);

        $doc = new DOMDocument();
        if (!@$doc->loadHTML('<?xml encoding="UTF-8"><div id="nl404-root">' . $article["content"] . '</div>')) {
            return $article;
        }
        $xpath = new DOMXPath($doc);

        // ghost slugifies heading text into the id, and the wording varies
        // ("around-the-web", "from-around-the-web", once "more-from-not-404")
        $marker = $xpath->query('//*[contains(@id, "around-the-web") or @id="more-from-not-404"]')->item(0);

        if (!$marker) {
            user_error(sprintf("404 media: newsletter without around the web: %s ", $article["link"]), E_USER_WARNING);
            $note = '<p><em>[Newsletter_404] This is a 404 Media newsletter, but the "Around the web" section could not be found.</em></p>';
            if (!str_contains($article["content"], "[Newsletter_404]")) {
                $article["content"] = $note . $article["content"];
            }
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

        return $article;
    }

    function api_version() {
        return 2;
    }
}
?>
