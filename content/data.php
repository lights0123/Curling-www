CSC Curling | View Data
<div>
	<script>
		function downloadScript(url) {
			downloadScriptFunction:{
				if (localStorage) {
					try {
						var theData = JSON.parse(localStorage.getItem(url));
						if (theData[1].length == theData[0]) {
							break downloadScriptFunction;
						}
					} catch (e) { }
					$.get(url, function (data) {
						localStorage.setItem(url, JSON.stringify([data.length, data]))
					});
				}
			}
		}
		[
			'/js/svg.js'
		].forEach(function (src) {
			var content=$("#content");
			parse:{try {
				if (localStorage && localStorage.getItem(src)) {
						var theData = JSON.parse(localStorage.getItem(src));
						if (theData[1].length == theData[0]) {
							content.append($("<script>" + theData[1] + "<\/script>"));
							break parse;
						}
				}
			} catch (e) {}
				content.append($("<script src=\"" + src + "\"><\/script>"));
				downloadScript(src);
			}
		})
	</script>
	<link rel='stylesheet' type='text/css' href='/css/jquery.bracket.min.css'/>
	<?php
	chdir(__DIR__);
	include("../scripts/parse.php");
	?>
</div>