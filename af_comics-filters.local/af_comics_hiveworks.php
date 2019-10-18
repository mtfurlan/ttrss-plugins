<?php
class Af_Comics_Hiveworks extends Af_ComicFilter {

	function supported() {
		return array("Many hiveworks RSS feeds, with special stuff for SMBC");
	}

	function process(&$article) {
		if (strpos($article["author"], "tech@thehiveworks.com") === FALSE) {
			return false;
		}
		//Debug::log("hiveworks comic: " . $article["link"], Debug::$LOG_EXTENDED);
		//Debug::log(print_r($article, TRUE), Debug::$LOG_EXTENDED);

		$doc = new DOMDocument();
		@$doc->loadHTML(fetch_file_contents($article["link"]));

		$basenode = false;

		if ($doc) {
			$xpath = new DOMXPath($doc);

			$comicNode = $xpath->query('(//img[@id="cc-comic"])')->item(0);
			$mouseover = $comicNode->getAttribute('title');
			//$imgURL = $comicNode->getAttribute('src');

			$newsHeader = $xpath->query('//div[@class="cc-newsheader"]')->item(0)->textContent;
			$publishTime = $xpath->query('//div[@class="cc-publishtime"]')->item(0)->textContent;
			$newsContentNode = $xpath->query('//div[@class="cc-newsbody"]')->item(0);

			$hasNews = $newsContentNode;

			Debug::log(print_r($newsContentNode, TRUE), Debug::$LOG_EXTENDED);
			if (strpos($article["link"], "smbc-comics.com/comic") !== FALSE) {
				$extraContent = $xpath->query('(//div[@id="aftercomic"][1])')->item(0)->firstChild;
			}

			if ($comicNode || $newsContentNode) {
				$article["content"] = "<div>";
				$article["content"] .= "<a href=\"{$article["link"]}\">";
				$article["content"] .= $doc->saveHTML($comicNode);
				$article["content"] .= "</a>";
				if($mouseover) {
					$article["content"] .= "<br><span>$mouseover</span><br>";
				}
				if($extraContent) {
					$article["content"] .= $doc->saveHTML($extraContent);
				}
				if($hasNews) {
					if($newsHeader) {
						$article["content"] .= "<h2>$newsHeader</h2>";
					} else {
						$article["content"] .= "<h2>News</h2>";
					}
					$article["content"] .= "<small>$publishTime</small>";
					$article["content"] .= $doc->saveHTML($newsContentNode);
				}
				$article["content"] .= "</div>";
			}
		}
		return true;
	}
}
?>
