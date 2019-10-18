<?php
class Af_Comics_Taptastic extends Af_ComicFilter {

	function supported() {
		return array("Taptastic");
	}

	function process(&$article) {
		if (strpos($article["link"], "tapas.io") === FALSE) {
			return false;
		}

		$doc = new DOMDocument();
		@$doc->loadHTML(fetch_file_contents($article["link"]));

		$basenode = false;

		if ($doc) {
			$xpath = new DOMXPath($doc);

			$basenode = $xpath->query('(//article[@class="ep-contents"])')->item(0);
			Debug::log(print_r($basenode, TRUE), Debug::$LOG_EXTENDED);


			if ($basenode) {
				$article["content"] = $doc->saveXML($basenode);
			}
		}

		return true;
	}
}
?>


