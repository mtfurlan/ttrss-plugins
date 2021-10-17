/* global require, PluginHost */

require(['dojo/_base/kernel', 'dojo/ready'], function  (dojo, ready) {
	function doStuff(row) {
		const webtoonsImageDiv = row.querySelector("div[plugin-data-webtoons-fontsize-none='true']")
		if(webtoonsImageDiv) {
			webtoonsImageDiv.style = "font-size: 0;";
		}
	}

	ready(function () {
		PluginHost.register(PluginHost.HOOK_ARTICLE_RENDERED_CDM, function (row) {
			doStuff(row);
			return true;
		});

		PluginHost.register(PluginHost.HOOK_ARTICLE_RENDERED, function (row) {
			doStuff(row);
			return true;
		});
	});
});
