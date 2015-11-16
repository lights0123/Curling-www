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
		$events[count($events)] = Array(getCell("A" . $i, $file), getCell("B" . $i, $file), $cell);
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
	<div id="drawing"></div>
	<script>
		var events = <?php echo json_encode($events) ?>;
		var teams = <?php echo json_encode($teams) ?>;
	</script>
	<script>
		var text;
		var cellWidth = 70;
		var cellHeight = 22;
		$(document).ready(function () {
			var draw = SVG('drawing').size(800, 800);
			var current = 0;
			var xPos = 0;
			var oldEvent = events[0][2] - 1;
			var maxCurrent = 0;
			var timesreset=0;
			for (var i = 0; i < events.length; i++) {
				if (oldEvent + 1 != events[i][2]) {
					current = Math.pow(3,(timesreset+1))*90;
					xPos += 200;
					timesreset++;
				}

				oldEvent = events[i][2];

				draw.rect(30, cellHeight * 2).fill('#EEE').move(xPos, current);

				var eve = draw.foreignObject(30, cellHeight * 2).move(xPos, current);
				$(eve.appendChild("div", {innerText: events[i][2].toString()}).node['childNodes'][0])
					.addClass("EventNumber");
				$(eve.appendChild("div").node['childNodes'][1])
						.addClass("EventNumberBorder");

				for (var j = 0; j < 2; j++) {

					draw.rect(cellWidth, cellHeight).fill('#EEE').move(xPos + 30, current);

					var fobj = draw.foreignObject(cellWidth, cellHeight + 1).move(xPos + 30, current).
					appendChild("div", {innerText: events[i][j]});

					if ((i * 2 + j) % 2 == 0) {
						$(fobj.appendChild('div').node['childNodes'][1]).addClass("borderTeam")
					}

					current += (i * 2 + j) % 2 == 1 ? 50 : cellHeight;

					if (current > maxCurrent) {
						maxCurrent = current;
					}

				}
			}
			$('#drawing>svg').attr("height", maxCurrent);
		});
	</script>
<?php
echo <<<EOF
<div id="brackets"></div>
<script>
var bigData = {
  teams : [
    ["ThisIsAVeryLongName",  "Team 2" ],
    ["Team 3",  "Team 4" ],
    ["Team 5",  "Team 6" ],
    ["Team 7",  "Team 8" ],
    ["Team 9",  "Team 10"],
    ["Team 11", "Team 12"],
    ["Team 13", "Team 14"],
    ["Team 15", "Team 16"]
  ],
  results : [[ /* WINNER BRACKET */
    [[7,5], [2,4], [6,3], [2,3], [1,5], [5,3], [7,2], [1,2]],
    [[1,2], [3,4], [5,6], [7,8]],
    [[9,1], [8,2]],
    [[1,3]]
  ], [         /* LOSER BRACKET */
    [[5,1], [1,2], [3,2], [6,9]],
    [[8,2], [1,2], [6,2], [1,3]],
    [[1,2], [3,1]],
    [[3,0], [1,9]],
    [[3,2]],
    [[4,2]]
  ], [         /* FINALS */
    [[3,8], [1,2]],
    [[null,null]]
  ]]
}

$(function() { $('div #brackets').bracket({init: bigData}) })
</script>
EOF;
function getCell($pCoordinate = 'A1', $file)
{
	if (!($file instanceof PHPExcel)) {
		throw new Exception("Needs to be PHPExcel");
	}
	return $file->getActiveSheet()->getCell($pCoordinate)->getCalculatedValue();
}