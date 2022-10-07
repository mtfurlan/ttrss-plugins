<?php
class Af_Comics_DeadWinter extends Af_ComicFilter {
	function supported() {
		return array("Dead Winter");
	}

	function process(&$article) {
		if (strpos($article["link"], "deadwinter.cc") === FALSE) {
			return false;
		}

		$doc = new DOMDocument();
		@$doc->loadHTML(fetch_file_contents($article["link"]));

		if ($doc) {
			$xpath = new DOMXPath($doc);

			$comicNode = $xpath->query('//div[@id="comic"]/img')->item(0);
			$mouseover = $comicNode->getAttribute('title');

			if ($comicNode) {
				echo "did stuff\n";
				$article["content"] = "<div>";
				$article["content"] .= $doc->saveHTML($comicNode);
				if($mouseover) {
					$article["content"] .= "<br><span>$mouseover</span><br>";
				}
				$article["content"] .= "</div>";
			}
		}
		return true;
	}
}
?>

