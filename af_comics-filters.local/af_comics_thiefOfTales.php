<?php
class af_comics_thiefOfTales extends Af_ComicFilter {
	function supported() {
		return array("Thief of Tales");
	}

	function process(&$article) {
		if (!str_contains($article["link"], "thethiefoftales.com")) {
			return false;
		}
		$article["content"] = str_replace("-150x150", "", $article["content"]);
		return true;
	}
}
?>
