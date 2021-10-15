<?php
class Github_Release extends Plugin {
	private $host;

	function about() {
		return array(0.5,
			"find github release feed from repo url",
			"mtf");
	}

	function init($host) {
		$host->add_hook($host::HOOK_SUBSCRIBE_FEED, $this);
	}

	function hook_subscribe_feed($contents, $url, $auth_login, $auth_pass) {
		if (strpos($url, "github.com") === FALSE) {
			return false;
		}
		// so if we are trying to get a github repo, return this so
		// _get_feeds_from_html can figure it out
		if(preg_match("/^https?:\/\/github.com\/[^\/]*\/[^\/]*$/",$url)) {
			$contents = "<html><head><link type=\"application/atom+xml\" rel=\"alternate\" href=\"$url/releases.atom\"/></head></html>";
		}

		return $contents;
	}

	function api_version() {
		return 2;
	}

}
?>
