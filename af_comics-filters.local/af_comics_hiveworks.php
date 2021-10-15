<?php
class Af_Comics_Hiveworks extends Af_ComicFilter {

	function supported() {
		return array("Many hiveworks RSS feeds, with special stuff for SMBC");
	}

	function getFirstXpathQueryResultOrNull($xpath, $query) {
		$results = $xpath->query($query);
		if($results->length == 0) {
			return null;
		}
		return $results->item(0);
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

			$newsHeader = $this->getFirstXpathQueryResultOrNull($xpath, '//div[@class="cc-newsheader"]');
			$publishTime = $this->getFirstXpathQueryResultOrNull($xpath, '//div[@class="cc-publishtime"]');
			$newsContentNode = $this->getFirstXpathQueryResultOrNull($xpath, '//div[@class="cc-newsbody"]');

			//Debug::log(print_r($newsContentNode, TRUE), Debug::$LOG_EXTENDED);

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
			}
		}
		return true;
	}
}
?>
