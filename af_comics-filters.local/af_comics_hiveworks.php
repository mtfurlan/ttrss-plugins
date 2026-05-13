<?php
class Af_Comics_Hiveworks extends Af_ComicFilter {

    private $supportedList = array('littletinythings.com', 'smbc-comics.com', 'egscomics.com', 'girlgeniusonline.com');

    function supported() {
        return array("Hiveworks(and maybe ex-hiveworks?) RSS feeds, with special stuff for SMBC, urls detected: "
            . implode(",", $this->supportedList));
    }

    function getFirstXpathQueryResultOrNull($xpath, $query) {
        $results = $xpath->query($query);
        if($results->length == 0) {
            return null;
        }
        return $results->item(0);
    }

    // return if we are intended to handle this url
    function process(&$article) {
        $link = $article["link"];
        $host = parse_url($link)['host'];
        $hostArr = explode(".", $host);
        $host = $hostArr[array_key_last($hostArr)-1] . "." . $hostArr[array_key_last($hostArr)];

        if(!in_array($host, $this->supportedList) && !str_contains($article["author"], "tech@thehiveworks.com")) {
            return false;
        }
        Debug::log("hiveworks comic: " . $link, Debug::$LOG_EXTENDED);
        Debug::log(print_r($article, TRUE), Debug::$LOG_EXTENDED);

        $doc = new DOMDocument();
        @$doc->loadHTML(fetch_file_contents($link));

        $basenode = false;

        if ($doc) {
            $xpath = new DOMXPath($doc);

            $comicNode = $xpath->query('(//img[@id="cc-comic"])')->item(0);
            $mouseover = $comicNode->getAttribute('title');
            //$imgURL = $comicNode->getAttribute('src');

            $newsHeader = $this->getFirstXpathQueryResultOrNull($xpath, '//div[@class="cc-newsheader"]');
            $publishTime = $this->getFirstXpathQueryResultOrNull($xpath, '//div[@class="cc-publishtime"]');
            $newsContentNode = $this->getFirstXpathQueryResultOrNull($xpath, '//div[@class="cc-newsbody"]');

            //Debug::log(print_r($newsContentNode, TRUE), Debug::$LOG_EXTENDED);

            if ($host == "smbc-comics.com") {
                $extraContent = $xpath->query('//div[@id="aftercomic"]')->item(0)->firstChild;
            }

            if ($comicNode) {
                $article["content"] = "<div>";
                $article["content"] .= "<a href=\"{$link}\">";
                $article["content"] .= $doc->saveHTML($comicNode);
                $article["content"] .= "</a>";
                if($mouseover) {
                    $article["content"] .= "<br><span>$mouseover</span><br>";
                }
                if(isset($extraContent)) {
                    $article["content"] .= $doc->saveHTML($extraContent);
                }
                if($newsContentNode) {
                    if(isset($newsHeader)) {
                        $article["content"] .= "<h2>$newsHeader->textContent</h2>";
                    } else {
                        $article["content"] .= "<h2>News</h2>";
                    }
                    if(isset($publishTime)) {
                        $article["content"] .= "<small>$publishTime->textContent</small>";
                    }
                    $article["content"] .= $doc->saveHTML($newsContentNode);
                }
                $article["content"] .= "</div>";
            } else {
                Debug::log("failed to find comic node, not updating article " . $link);
            }
        } else {
            Debug::log("failed to fetch " . $link);
        }

        return true;
    }
}
?>
