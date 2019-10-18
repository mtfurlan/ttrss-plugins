<?php
class af_comics_thiefOfTales extends Af_ComicFilter {
	function supported() {
		return array("Thief of Tales");
	}

	function process(&$article) {
		if (strpos($article["link"], "thethiefoftales.com") === FALSE) {
			return false;
		}
		//feed stores article link in the guid field, which ttrss merges with owner_uid in classes/rssutils.php
		$link = substr($article["guid"], strpos($article["guid"], ",")+1);
		//Also page number does not match?
		//The post titled "Page 175" goes to a page that claims to be "page 172".
		//How does someone fuck this up so badly?

		//Debug::log("thie fo  tales: " . $link, Debug::$LOG_EXTENDED);
		//Debug::log(print_r($article, TRUE), Debug::$LOG_EXTENDED);

		$doc = new DOMDocument();
		@$doc->loadHTML(fetch_file_contents($link));

		$comicNode = false;
		$newsNode = false;

		if ($doc) {
			$xpath = new DOMXPath($doc);

			$comicNode = $xpath->query('(//figure[@class="comic__image"])')->item(0)->firstChild->firstChild;
			$newsNode = $xpath->query('(//article[@class="secondary__blog"])')->item(0);


			if ($comicNode) {
				$article["content"] = "<div>";
				$article["content"] .= "<a href=\"{$link}\">";
				$article["content"] .= $doc->saveHTML($comicNode);
				$article["content"] .= "</a>";
				if($newsNode) {
					$article["content"] .= $doc->saveHTML($newsNode);
				}
				$article["content"] .= "</div>";
			}
		}
		return true;
	}
}
?>
