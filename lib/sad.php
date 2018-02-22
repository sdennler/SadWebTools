<?php

/** Cry out a variable
 * @version 0.10:2018-02-22
 * @author dennler.jobs.ch
 * @versionTrack 0.9:2016-11-02:0.8:2013-12-02,0.7:2010-06-15,0.6:2008-09-09,0.5:2007-06-04,0.4:2006-03-17,0.3:2004-12-02,0.2:2004-11-29,0.1:2004-05-06,scratch
 * @param mixed   The Value to display
 * @param string  Name to describe the output
 * @param int     Displaymodus: 1 = pre; 2 = one line; 3 = hidde in HTML; 4 = plain; 5 = don't dive, 6 = Write to Logfile SAD_LOGFILE
 * @param int     Return modus: 1 = return $Value; 2 = return output isted ouf print
 * @return mixed  The Value
 */
function sad($Value=false, $Name=false, $Mode=1, $ReturnMode=1){
	global $php_errormsg, $cDebug;
	$output = $pre = $post = '';

	if($Mode > 10){
		$Mode -= 10;
	}
	elseif($cDebug !== true){
		return false;
	}

	if(func_num_args() === 0){
		$Value = $php_errormsg;
		$Name = '$php_errormsg';
		$Mode = 2;
		sad(mysql_error(), 'MySQL Error', 2);
	}

	if(php_sapi_name() == 'cli'){
		$Mode = 4;
	}

	switch($Mode){ // Switch for the output
		case 5:
			if(is_array($Value) || is_object($Value) || is_resource($Value)){
				$output .= gettype($Value)." whit:\n";
				foreach($Value as $key => $content){
					if(is_string($content)) $content = 'String of '.strlen($content).' length';
					if(is_object($content)) $content = 'Object of type '.get_class($content);
					if(is_resource($content)) $content = 'Resource of type '.get_resource_type($content);
					if(is_array($content)) $content = 'Array whit '.count($content).' elements';
					$output .= "$key => $content\n";
				}
				break;
			} else $Mode = 1;

		default:
			if(is_bool($Value)) $output .= "Bool ".($Value ? 'TRUE' : 'FALSE');
			else $output .= var_export($Value, true);
	} // End Switch

	switch($Mode){
		case 6:
			$pre .= date('Y-m-d H:i:s')." $Name: ";
			$post .= "\n";
		break;

		case 4:
			$pre .= "\n## $Name: ";
			$post .= "\n";
		break;

		case 3:
			$pre .= "\n<!-- ## $Name\n";
			$post .= "\n-->\n";
		break;

		case 2:
		case 1:
		default:
			              $pre .= '<p class="error">';
			if($Name)     $pre .= '<b class="error">'.$Name.':</b> ';
			if($Mode!=2)  $pre .= '<pre class="error">';
			if($Mode!=2)  $post .= '</pre>';
			              $post .= "</p>\n";
		break;
	} // End Switch

	$output = $pre.$output.$post;
	$return = $Value;

	if($Mode==6 && defined('SAD_LOGFILE')){
	 file_put_contents(SAD_LOGFILE, $output, FILE_APPEND);
	}
	elseif($ReturnMode==2){
		$return = $output;
	}
	else echo $output;

	return $return;
} // End sad
