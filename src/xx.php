<?php
# Check for minify flag in URL
//$minify = (!empty($argv[1]) ? $argv[1] : !empty($_GET["min"]) ? true : false);
//
$loop = [0,1];
# Establish processing start time for total render time calculation
# Set header to CSS content type
header("Content-type: text/css; charset: UTF-8");
# Clean float takes an value ($input) and turns it into a string, eliminating any decimals and hyphens (for example, 4.0, 4, and -4.0 all render out to "4". 4.5 renders to "45")
function cleanFloat($input) {
	$almost = str_replace(['-','.'],'',"".$input);
	#$done = (substr($almost,0,1)=="0" ? substr($almost,1) : $almost);
	return $almost;
}
foreach ($loop as $idx=>$example) {
	$startTime = microtime();
	$minify = $idx;
	# Initialize output css string
	$output = "";
	# Pull properties from relevant json file
	# true as argument forces an array instead of a php object (so we can iterate)
	$props = json_decode(file_get_contents('xx.json'),true);
	# Iterate through each category
	foreach ($props as $categoryName=>$set) {
		# Unless minify is set...
		if (empty($minify)) {
			// write out this category's name in ornamented comment style
			$output .= "\n\n";
			$output .= "/*--------------------------------------\n";
			$output .= "/*--------------------------------------\n";
			$output .= "/*------- ".strtoupper($categoryName)." ";
			$x = 0;
			while ($x <= (28 - strlen($categoryName))) {$output .= "-"; $x++;}
			$output .= "\n";
			$output .= "/*--------------------------------------\n";
			$output .= "/*--------------------------------------*/\n";
			$output .= "\n";
		}
		# Iterate through the objects (properties) in this category
		foreach ($set as $property) {
			# unless minifying, write out this property for clarity's sake
			if (empty($minify)) {
				$output .= "\n/*    ".$property["property"]."   */\n";
			}
			# static properties run here
			# static properties have preset values, i.e., 'bloc' => 'display:block'
			# usually not numeric (except in some cases)
			if ($property["type"]=="static") {
				# cycle through this property's set of values
				foreach ($property["values"] as $value) {
					# properties come comma-separated (i.e., 'classname,propValue')
					$ready = explode(',',$value);
					# establish the class prefix if there is one
					$prefix = (empty($property["prefix"]) ? '' : $property["prefix"]);
					# write out the class
					$output .= ".".$prefix.$ready[0]."{".$property["property"].":".$ready[1].";}\n";
				}
				# Now prepare for mobile classes by creating media query...
				$output .= "\n@media(max-width:768px){\n";
				# cycle again in the same way...
				foreach ($property["values"] as $value) {
					$ready = explode(',',$value);
					$prefix = (empty($property["prefix"]) ? '' : $property["prefix"]);
					# ... except add an 'm' at the end of the classname
					# i.e., f22 becomes f22m for mobile font-size of 22px
					$output .= ".".$prefix.$ready[0]."m{".$property["property"].":".$ready[1].";}\n";
				}
				$output .= "}\n";
				# dynamic properties run here
				# dynamic properties have parameters for writing out many values
				# parameters are comma separated
				# as such: [startingValue,endingValue,stepValue,units,suffix]
				# e.g. [0,50,1,px] starts at 0, increments by 1, until 50, adding the unit 'px'
				# e.g. [5,100,5,%,p] starts at 5%, increments by 5% until 100%, and classes have suffix 'p' (i.e., font-size:20% is css class f20p)
			} elseif ($property["type"]=="dynamic") {
				# cycle through properties once again...
				foreach ($property["values"] as $set) {
					# Get prefix if one exists
					$prefix = (empty($property["prefix"]) ? '' : $property["prefix"]);
					# turn comma-separated values into array
					$thisSet = explode(',',$set);
					# assign start
					$start = floatval($thisSet[0]);
					# assign end
					$end = floatval($thisSet[1]);
					# assign step
					$step = floatval($thisSet[2]);
					# 'subprop' is any string which comes before the dynamic value
					# e.g., trans5 creates properties "transition:all 0.5s"
					# in this case, 'all' is a subProp
					$subProp = ( empty($property["subProp"]) ? '' : $property["subProp"]." ");
					# assign units
					$units = ( empty($thisSet[3]) ? '' : $thisSet[3]);
					# assign suffix
					$suffix = ( empty($thisSet[4]) ? '' : $thisSet[4]);
					# setup iterator
					$x = $start;
					# iterate!
					while($x<=$end) {
						# if this css property has 'webkit' set to true in the json...
						if (isset($property["webkit"]) && $property["webkit"] === true) {
							# we create an addon that prepends the '-webkit-' before the property name
							$webKitAddon = '-webkit-'.$property["property"].":" . $subProp . $x . $units .";";
						} else {
							# otherwise do nothin'
							$webKitAddon = "";
						}
						# write it all down in order
						$output .= "." . $prefix.cleanFloat($x) . $suffix."{" . $webKitAddon . $property["property"] . ":" . $subProp . $x . $units . ";}\n";
						# increment to continue iteration
						$x = $x+$step;
					}
				}
				# prepare for mobile version of the property
				$output .= "\n@media(max-width:768px){\n";
					# do it all again, but with an 'm' suffix for mobile
					foreach ($property["values"] as $set) {
						$prefix = (empty($property["prefix"]) ? '' : $property["prefix"]);
						$thisSet = explode(',',$set);
						$start = floatval($thisSet[0]);
						$end = floatval($thisSet[1]);
						$step = floatval($thisSet[2]);
						$subProp = ( empty($property["subProp"]) ? '' : $property["subProp"]." ");
						$units = ( empty($thisSet[3]) ? '' : $thisSet[3]);
						$suffix = ( empty($thisSet[4]) ? '' : $thisSet[4]);
						$x = $start;
						while ($x<=$end) {
							if (isset($property["webkit"]) && $property["webkit"] === true) {
								$webKitAddon = '-webkit-'.$property["property"].":" . $subProp . $x . $units .";";
							} else {
								$webKitAddon = "";
							}
							$output .= "." . $prefix.cleanFloat($x) . $suffix."m{".$webKitAddon.$property["property"].":" . $subProp . $x . $units . ";}\n";

							$x = $x+$step;
						}
					}
					$output .= "}\n";
				}
			}
		}
		# if we're minifying....
		if (!empty($minify)) {
			# remove all the line breaks
			$output = str_replace("\n", "", $output);
			# otherwise...
		} else {
			# put a little header up top with compilation time
			//echo PHP_EOL.'/* XX.CSS -- compiled in '.((microtime() - $startTime) * 1000)."ms */".PHP_EOL.PHP_EOL;
		}
		# show it!
		//echo $output;
		echo 'XX.'.(!empty($minify)?'min.':'').'CSS -- compiled in '.((microtime() - $startTime) * 1000)."ms".PHP_EOL;
		## optional:
		## every time it's run, create a new flat css file with the output
		file_put_contents(dirname(__DIR__).'/dist/xx.'.(!empty($minify) ? 'min.' : '').'css',$output);
	}
