<?php
class Af_Comics_Webtoons extends Af_ComicFilter {
    function supported() {
        return array("webtoons (requires af_refspoof 2.0.0+)");
    }

    function process(&$article) {
        if (strpos($article["link"], "webtoons.com") === FALSE) {
            return false;
        }
        if(!class_exists("af_refspoof")) {
            user_error("af_comics webtoon needs af_refspoof 2.0.0+", E_USER_ERROR);
            return false;
        }

        $doc = new DOMDocument();
        @$doc->loadHTML(fetch_file_contents($article["link"]));

        $basenode = false;

        if (!$doc) {
            user_error("af_comics webtoon failed to fetch article: ${article["link"]}", E_USER_ERROR);
            return false;
        } else {
            $xpath = new DOMXPath($doc);

            $basenode = $xpath->query("//*[@id='_imageList']")->item(0);

            if (!$basenode) {
                user_error("af_comics webtoon failed to find image container in : ${article["link"]}", E_USER_ERROR);
                return false;
            }

            foreach ($xpath->query('(//img[@src])', $basenode) as $entry){
                //$entry->setAttribute("src",$entry->getAttribute("data-url"));
                $origSrc = $entry->getAttribute("data-url");
                $entry->setAttribute("src", af_refspoof::make_redirect_url($origSrc, $article['link']));
            }

            $creatorNote = $xpath->query('//div[@class="creator_note"]');
            if($creatorNote->length > 0) {
                $basenode->appendChild($creatorNote->item(0));
            }

            $article["content"] = $doc->saveXML($basenode);
        }

        return true;
    }
}
?>

