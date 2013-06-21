#!/usr/bin/php
<?PHP

require_once('vendor/cssmin/CssMin.php');

// Remove script name
array_shift($argv);

$recursive = false;
$targets = array();

do {
	
	$arg = current($argv);
	
	if ($arg === false) // No more arguements
		break;
		
	if ($arg == '-r') {
		// Config file option
		
		$recursive = true;
		
	} else {
		
		$targets[] = $arg;
		
	}
	
} while (next($argv));

if (!sizeof($targets))
	errorExit('No target files specified.');
	
function walk($target, $recursive = false) {
	if (is_dir($target)) {
		
		$d = opendir($target);
		
		while ($next = readdir($d)) {
			
			if (preg_match('/^\./', $next)) continue;
			
			$next = $target . DIRECTORY_SEPARATOR . $next;
			
			if (is_dir($next) && $recursive)
				walk($next, $recursive);
			elseif (!is_dir($next))
				minify($next);
			
		}
		
		closedir($d);
		
	} else {
		minify($target);
	}
}

function minify($targetFile) {
	
	// Already minified
	if (preg_match('/\.min\.(css|js)$/i', $targetFile)) return;
	
	// Doesn't exist?
	if (!file_exists($targetFile)) return;
	
	// Can't read it?
	if (!is_readable($targetFile)) 
		errorExit('Target file not readable: '. $targetFile);
	
	$fileNameArray = explode('.', $targetFile);
	$suffix = array_pop($fileNameArray);
	array_push($fileNameArray, 'min'); // New name is *.min.js or *.min.css
	array_push($fileNameArray, $suffix);
	
	$newFileName = implode('.', $fileNameArray);
	
	$targetFileContents = file_get_contents($targetFile);
	
	if (preg_match('/\.css$/i', $targetFile)) {
		// CSS
		
		$minString = CssMin::minify($targetFileContents);
				
		$res = file_put_contents($newFileName, $minString);
		
	} elseif (preg_match('/\.js$/i', $targetFile)) {
		// Javascript
		
		exec('java -jar '. dirname(__FILE__) .'/vendor/google/compiler.jar --js '. $targetFile .' --js_output_file '. $newFileName, $out, $res);
		
	} else // Unknown file type
		return; 
		
	if ($res === false) 
		errorExit('Unable to write minified file: '. $newFileName, implode("\n", $out));
	
	$before = filesize($targetFile);
	$after = filesize($newFileName);
	
	$change = round( ( $before - $after ) / $before, 2 ) * 100;
	
	writeLine($newFileName .' minified (size reduced by '. $change .'%)');
	
}

// Change
foreach ($targets as $target) 
	walk($target, $recursive);
	
function errorExit($string, $errInfo = false) {
	fwrite(STDERR, "ERROR: ". $string ."\n");
	if ($errInfo) fwrite(STDERR, $errInfo ."\n");
	exit(1);
}

function writeLine($string) {
	fwrite(STDOUT, $string ."\n");
}