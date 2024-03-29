<?php
# this is seperate from af_comics because it needs to muck with sanitization to allow style
class af_comics_webtoons extends Plugin {
    function about() {
        return array(
            "1.0.0",
            "make webtoons work (requires refspoof)",
            "mtf");
    }

    function init($host) {
        $host->add_hook($host::HOOK_ARTICLE_FILTER, $this);
    }

    function get_js() {
        return file_get_contents(__DIR__ . "/init.js");
    }

    function hook_article_filter($article) {
        if (strpos($article["link"], "webtoons.com") === FALSE) {
            return $article;
        }
        if(!class_exists("af_refspoof")) {
            user_error("af_comics webtoon needs af_refspoof 2.0.0+", E_USER_ERROR);
            return $article;
        }

        $doc = new DOMDocument();
        @$doc->loadHTML(fetch_file_contents($article["link"]));

        $imagelist = false;

        if (!$doc) {
            user_error("af_comics webtoon failed to fetch article: ${article["link"]}", E_USER_ERROR);
            return $article;
        } else {
            $xpath = new DOMXPath($doc);

            $imagelist = $xpath->query("//*[@id='_imageList']")->item(0);

            if (!$imagelist) {
                user_error("af_comics webtoon failed to find image container in : ${article["link"]}", E_USER_ERROR);
                return $article;
            }

            $basenode = $doc->createElement("article", "");

            foreach ($xpath->query('(//img[@src])', $imagelist) as $entry){
                $entry->setAttribute("src",$entry->getAttribute("data-url"));
            }

            $imagelist->setAttribute('plugin-data-webtoons-fontsize-none', 'true');

            $basenode->appendChild($imagelist);


            $creatorNote = $xpath->query('//div[@class="creator_note"]');
            if($creatorNote->length > 0) {
                $basenode->appendChild($creatorNote->item(0));
            }

            $article["content"] = $doc->saveXML($basenode);
        }

        return $article;
    }

    function api_version() {
        return 2;
    }
}
