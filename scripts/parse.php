<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
chdir(__DIR__);
include("PHPExcel/PHPExcel/IOFactory.php");
$file=PHPExcel_IOFactory::load("PHPExcel/CSC Bonspiel.xlsx");
$file->setActiveSheetIndexByName("Master Draw");
/*$go=true;
$i=0;
$letter="B";
$teams=Array();
while ($go){
	if($i%2==1){
		$iter=ceil($i/2)*6;
	}else{
		$iter=2+($i/2*6);
	}
	$cell=$letter.$iter;
	$team=getCell($cell);
	if($team==null){
		if($letter=="B"){
			$letter="V";
			$i=-1; //because of $i++ below, otherwise it would be 0
		}else {
			$go = false;
		}
	}else{
		echo $team."<br />";
		$teams[count($teams)]=$team;
	}
	$i++;
}
var_dump($teams);*/
$file->setActiveSheetIndexByName("Data");
$go=true;
$i=0;
$events=Array();
$teams=Array();
while($go){
	$cell=getCell('E'.($i+3),$file);
	if($cell==null){
		$go=false;
	}else{
		$events[$cell]=Array(getCell("A".($i+3),$file),getCell("B".($i+3),$file));
		if(!in_array(getCell("A".($i+3),$file),$teams)){
			$teams[count($teams)]=getCell("A".($i+3),$file);
		}
		if(!in_array(getCell("B".($i+3),$file),$teams)){
			$teams[count($teams)]=getCell("B".($i+3),$file);
		}
	}
	$i++;
}
echo "<pre>";
var_dump($events);
var_dump($teams);
$jsonevents=base64_encode(json_encode($events));
$jsonteams=base64_encode(json_encode($teams));
echo "</pre>";
?>
<canvas id="datacanvas" width="800px" height="800px"></canvas>
	<div id="drawing"></div>
<script>
	{
		/**
		 *
		 *  Base64 encode / decode
		 *  http://www.webtoolkit.info/
		 *
		 **/
		var Base64 = {

// private property
			_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",


// public method for decoding
			decode : function (input) {
				var output = "";
				var chr1, chr2, chr3;
				var enc1, enc2, enc3, enc4;
				var i = 0;

				input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

				while (i < input.length) {

					enc1 = this._keyStr.indexOf(input.charAt(i++));
					enc2 = this._keyStr.indexOf(input.charAt(i++));
					enc3 = this._keyStr.indexOf(input.charAt(i++));
					enc4 = this._keyStr.indexOf(input.charAt(i++));

					chr1 = (enc1 << 2) | (enc2 >> 4);
					chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
					chr3 = ((enc3 & 3) << 6) | enc4;

					output = output + String.fromCharCode(chr1);

					if (enc3 != 64) {
						output = output + String.fromCharCode(chr2);
					}
					if (enc4 != 64) {
						output = output + String.fromCharCode(chr3);
					}

				}

				output = Base64._utf8_decode(output);

				return output;

			},

// private method for UTF-8 decoding
			_utf8_decode : function (utftext) {
				var string = "";
				var i = 0;
				var c = c1 = c2 = 0;

				while ( i < utftext.length ) {

					c = utftext.charCodeAt(i);

					if (c < 128) {
						string += String.fromCharCode(c);
						i++;
					}
					else if((c > 191) && (c < 224)) {
						c2 = utftext.charCodeAt(i+1);
						string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
						i += 2;
					}
					else {
						c2 = utftext.charCodeAt(i+1);
						c3 = utftext.charCodeAt(i+2);
						string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
						i += 3;
					}

				}

				return string;
			}

		};
	}
	{
		///
/// t:fabric.IText, maxW:number, maxH:number, canvas:HTMLCanvas
///
		function wrapCanvasText(t, canvas, maxW, maxH ) {

			if (typeof maxH === "undefined") { maxH = 0; }
			var words = t.text.split(" ");
			var formatted = '';

// clear newlines
			var sansBreaks = t.text.replace(/(\r\n|\n|\r)/gm, "");
// calc line height
			var lineHeight = new fabric.Text(sansBreaks, {
				fontFamily: t.fontFamily,
				fontSize: t.fontSize
			}).height;

// adjust for vertical offset
			var maxHAdjusted = maxH > 0 ? maxH - lineHeight : 0;
			var context = canvas.getContext("2d");


			context.font = t.fontSize + "px " + t.fontFamily;
			var currentLine = "";
			var breakLineCount = 0;

			for(var n = 0; n < words.length; n++) {

				var isNewLine = currentLine == "";
				var testOverlap = currentLine + ' ' + words[n];

				// are we over width?
				var w = context.measureText(testOverlap).width;

				if(w < maxW) {  // if not, keep adding words
					currentLine += words[n] + ' ';
					formatted += words[n] + ' ';
				} else {

					// if this hits, we got a words that need to be hypenated
					if(isNewLine) {
						var wordOverlap = "";

						// test word length until its over maxW
						for(var i = 0; i < words[n].length; ++i) {

							wordOverlap += words[n].charAt(i);
							var withHypeh = wordOverlap + "…";

							if(context.measureText(withHypeh).width >= maxW) {
								// add hyphen when splitting a word
								withHypeh = wordOverlap.substr(0, wordOverlap.length - 2) + "…";
								// reset current word
								words[n] = words[n].substr(wordOverlap.length - 1, words[n].length);
								formatted += withHypeh; // add hypenated word
								break;
							}
						}
					}
					n--; // restart cycle
					formatted += '\n';
					breakLineCount++;
					currentLine = "";
				}
				if(maxHAdjusted > 0 && (breakLineCount * lineHeight) > maxHAdjusted) {
					// add ... at the end indicating text was cutoff
					formatted = formatted.substr(0, formatted.length - 3) + "...\n";
					break;
				}
			}
// get rid of empy newline at the end
			formatted = formatted.substr(0, formatted.length - 1);

			var ret = new fabric.Text(formatted, { // return new text-wrapped text obj
				left: t.left,
				top: t.top,
				fill: t.fill,
				fontFamily: t.fontFamily,
				fontSize: t.fontSize
			});
			return ret;

		}}
	var events = JSON.parse(Base64.decode('<?php echo $jsonevents ?>'));
	var teams = JSON.parse(Base64.decode('<?php echo $jsonteams ?>'));
	$(document).ready(function(){
			console.log(events);
			var canvas = new fabric.Canvas('datacanvas');
			for (var i = 0; i < teams.length; i++) {
				text = new fabric.Text(teams[i],
						{
							left: 3,
							top: (i + 1) * 22 + 3,
							fill: '#000',
							fontSize: 14,
							fontFamily: "Arial"
						});
				text = wrapCanvasText(text, canvas, 70, 14);
				text.selectable = false;

				var rect = new fabric.Rect({
					left: 0,
					top: (i + 1) * 22,
					fill: '#EEE',
					width: 70,
					height: 22
				});
				rect.selectable = false;
				canvas.add(rect);
				canvas.add(text);
			}
			canvas.selection = false;
			canvas.allowTouchScrolling = true;
			canvas.on('mouse:over', function () {
				console.log("Mouse");
			});
		});
</script>
<script>
	var text;
	$(document).ready(function(){
		var draw = SVG('drawing').size(800,800);
		var current=0;
		for (var i = 0; i < teams.length; i++) {

			draw.rect(70,22).fill('#EEE').move(0,current);

			var fobj = draw.foreignObject(70,23).move(0,current);
			fobj.appendChild("div", {innerText: teams[i]});

			if(i%2==0){
				fobj.appendChild('div').node['childNodes'][1].style='top: 21px; width: 70px; height: 0px; padding: 0px; position: absolute; border-top: 1px solid rgb(153, 153, 153);'
			}

			current+=i%2==1?50:22;
		}
		$('#drawing').height(current);
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
function getCell($pCoordinate='A1',$file){
	if(!($file instanceof PHPExcel)){throw new Exception("Needs to be PHPExcel");}
	return $file->getActiveSheet()->getCell($pCoordinate)->getCalculatedValue();
}