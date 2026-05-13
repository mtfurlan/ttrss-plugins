<?php
class Af_Comics_SF extends Af_ComicFilter {

	function supported() {
		return array("Sam and Fuzzy");
	}

	function process(&$article) {
		if (!str_contains($article["link"], "samandfuzzy.com")) {
			return false;
		}

		$doc = new DOMDocument();
		@$doc->loadHTML(fetch_file_contents($article["link"]));

		$basenode = false;

		if ($doc) {
			$xpath = new DOMXPath($doc);

			$basenode = $xpath->query('(//img[@class="comic-image"])')->item(0);

			if ($basenode) {
				$article["content"] = $doc->saveXML($basenode);
			}
		}

		return true;
	}
}
?>

