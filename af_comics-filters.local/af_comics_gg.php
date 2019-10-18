<?php
class Af_Comics_GG extends Af_ComicFilter {
	function supported() {
		return array("Girl Genius");
	}

	function process(&$article) {
		if (strpos($article["link"], "girlgeniusonline.com") === FALSE) {
			return false;
		}

		$doc = new DOMDocument();
		@$doc->loadHTML(fetch_file_contents($article["link"]));

		$basenode = false;

		if ($doc) {
			$xpath = new DOMXPath($doc);

			$basenode = $xpath->query('//img[contains(@alt, "Comic")]')->item(0);


			if ($basenode) {
				$article["content"] = $doc->saveXML($basenode);
			}
		}
		return true;
	}
}
?>
