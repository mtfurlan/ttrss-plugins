<?php
class Hackaday extends Plugin {
	private $host;

	function about() {
		return array(1.0,
			"Hackaday featured image",
			"mtfurlan");
	}

	function init($host) {
		$this->host = $host;

		$host->add_hook($host::HOOK_ARTICLE_FILTER, $this);
	}

	function hook_article_filter($article) {
		if (strpos($article["link"], "hackaday.com") === FALSE) {
			return $article;
		}

		//Debug::log("hackaday: " . $article["link"], Debug::$LOG_EXTENDED);
		//Debug::log(print_r($article, TRUE), Debug::$LOG_EXTENDED);

		$doc = new DOMDocument();
		@$doc->loadHTML(fetch_file_contents($article["link"]));

		$basenode = false;

		if ($doc) {
			$xpath = new DOMXPath($doc);

			$featuredImage = $xpath->query('//div[@class="entry-featured-image"]')->item(0)->firstChild;


			if ($featuredImage) {
		Debug::log("hackaday: " . $article["link"], Debug::$LOG_EXTENDED);
		Debug::log(print_r($featuredImage, TRUE), Debug::$LOG_EXTENDED);
				$content = $article["content"];
				$article["content"] = "<div class=\"fuckyou\">";
				$article["content"] .= $doc->saveXML($featuredImage);
				$article["content"] .= $content;
				$article["content"] .= "</div>";
			}
		}
		return $article;
	}


	function api_version() {
		return 2;
	}

}
?>
