<?php
class Ebay_Image extends Plugin {
	private $host;

	function about() {
		return array(1.0,
			"Replace ebay pictures with larger size",
			"mtfurlan");
	}

	function init($host) {
		$this->host = $host;

		$host->add_hook($host::HOOK_ARTICLE_FILTER, $this);
	}

	function hook_article_filter($article) {
		//Debug::log("ebay: " . $article["link"], Debug::$LOG_EXTENDED);
		if (strpos($article["link"], "ebay.com") !== FALSE) {
			//$article["content"] = str_replace("ebayimg.com/thumbs/images", "ebayimg.com/images", $article["content"]);
			//Debug::log(print_r($article["content"], TRUE), Debug::$LOG_EXTENDED);
		}

		return $article;
	}


	function api_version() {
		return 2;
	}

}
?>
