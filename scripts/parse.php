<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);*/
chdir(__DIR__);
include("PHPExcel/PHPExcel/IOFactory.php");
$file = PHPExcel_IOFactory::load("PHPExcel/CSC Bonspiel.xlsx");
$file->setActiveSheetIndexByName("Data");
$go = true;
$i = 3;
$events = Array();
$teams = Array();
while ($go) {
	$cell = getCell('E' . $i, $file);
	if ($cell == null) {
		$go = false;
	} else {
		$events[$cell] = ["team1" => getCell("A" . $i, $file),
			"team2" => getCell("B" . $i, $file),
			"winner" => getCell("H" . $i, $file)];
		if (!in_array(getCell("A" . $i, $file), $teams)) {
			$teams[count($teams)] = getCell("A" . $i, $file);
		}
		if (!in_array(getCell("B" . $i, $file), $teams)) {
			$teams[count($teams)] = getCell("B" . $i, $file);
		}
	}
	$i++;
}
?>
	<div class="tabs">
		<ul class="tab-links">
			<li class="active"><a class="autoexempt" href="#tab1">First Event</a></li>
			<li><a class="autoexempt" href="#tab2">Second Event</a></li>
			<li><a class="autoexempt" href="#tab3">Third Event</a></li>
			<li><a class="autoexempt" href="#tab4">Fourth Event</a></li>
			<li><a class="autoexempt" href="#tab4">Fifth Event</a></li>
		</ul>
	</div>
	<h1>First Event</h1>
	<div id="drawing"></div>
	<script>
		var events = <?php echo json_encode($events) ?>;
		var teams = <?php echo json_encode($teams) ?>;
	</script>
	<script>
		var text;
		var cellWidth = 140;
		var cellHeight = 22;
		console.log(events);
		$(document).ready(function () {
			var draw = SVG('drawing').size(970, 800);
			var current = 0;
			var xPos = 0;
			var oldEvent = Math.min.apply(Math, Object.keys(events)) - 1;
			var maxCurrent = 0;
			var timesreset = 0;
			Object.keys(events).forEach(function (value, key) {
				key = parseInt(value);
				value = events[value];
				if (oldEvent + 1 != key) {
					current = 18 * (Math.pow(2, timesreset + 2) - 2);
					xPos += 200;
					timesreset++;
				}
				if (key == 151) {
					var cbak = current;
					var xbak = xPos;
					current = 1080;
					xPos -= 200;
				}
				if ((value['team1']).toLowerCase() !== "bye") {
					oldEvent = key;
					draw.rect(30, cellHeight * 2).fill('#EEE').move(xPos, current);

					var eve = draw.foreignObject(30, cellHeight * 2).move(xPos, current);
					var child = eve.appendChild("a", {});
					$(child.node['childNodes'][0]).attr('href', '/event/' + key.toString()).addClass('autoexempt');
					console.log(child.node['childNodes'][0]);
					$(child.node['childNodes'][0]).click(function (e) {
						var url = $(this).attr('href');
						$.get(url, function (data) {

						}).fail(function (jqXHR) {

						});
						return false;
					});
					$(child.node['childNodes'][0]).append($("<div>")).children().text(key.toString()).addClass("EventNumber");
					$(eve.appendChild("div").node['childNodes'][1])
						.addClass("EventNumberBorder");

				}
				for (var j = 1; j < 3; j++) {
					if ((value['team1']).toLowerCase() !== "bye") {
						var color = '#EEE';
						if (value['team' + (j.toString())] == value['winner'] && (key == 151 || key == 161)) {
							color = '#da0';
						}
						draw.rect(cellWidth, cellHeight).fill(color).move(xPos + 30, current);

						var fobj = draw.foreignObject(cellWidth, cellHeight + 1).move(xPos + 30, current).appendChild("a", {});
						$(fobj.node['childNodes'][0]).addClass('autoexempt');
						$(fobj.node['childNodes'][0]).append($('<div>')).children().text(value['team' + (j.toString())]);
						if ((key * 2 + (j - 1)) % 2 == 0) {
							$(fobj.appendChild('div').node['childNodes'][1]).addClass("borderTeam")
						}
					}
					current += (key * 2 + (j - 1)) % 2 == 1 ? (9 * Math.pow(2, timesreset + 3) - 22) : cellHeight;
					if (current > maxCurrent && (key * 2 + (j - 1)) % 2 == 0) {
						maxCurrent = current + 22;
					}
				}
				if (key == 151) {
					current = cbak;
					xPos = xbak - 200;
					if (current > maxCurrent && (key * 2 + (j - 1)) % 2 == 0) {
						maxCurrent = current + 22;
					}
					timesreset--;
				}
			});
			$('#drawing>svg').attr("height", maxCurrent);
		});
	</script>
<?php
function getCell($pCoordinate = 'A1', $file)
{
	if (!($file instanceof PHPExcel)) {
		throw new Exception("Needs to be PHPExcel");
	}
	return $file->getActiveSheet()->getCell($pCoordinate)->getCalculatedValue();
}