<?php
require_once 'lib/sad.php';
require_once 'lib/sadConv.lib.php';
require_once 'lib/Quack.php';

error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", 1);


/**
 * Set timezone for date relevant conversions.
 */
$timezone = $timezone_default = 'UTC';
if(isset($_GET['timezone'])) $timezone = $_GET['timezone'];
elseif(isset($_POST['Timezone'])) $timezone = $_POST['Timezone'];
elseif(isset($_COOKIE['timezone'])) $timezone = $_COOKIE['timezone'];
if(!in_array($timezone, timezone_identifiers_list())) $timezone = $timezone_default;
date_default_timezone_set($timezone) || date_default_timezone_set($timezone_default);
if(!isset($_COOKIE['timezone']) || $timezone != $_COOKIE['timezone']){
 setcookie('timezone', $timezone, 2145916800);
}


/************************
 * Conversion Functions *
 ************************/


function uStamp2Date($uStamp){
    if (version_compare(PHP_VERSION, '5.0.0', '>=')) {
        return date('Y-m-d H:i:s O', (int) $uStamp);
    } else {
        return date('c', $uStamp);
    }
}

function uStampMillis2Date($uStamp){
    return uStamp2Date($uStamp/1000);
}

function dateTime2Iso8601($timestr){
    return uStamp2Date(strtotime($timestr));
}

function schoepferExportTranslator($input){
    #return ereg_replace('&quot;&quot;20&quot;&quot;', "\n", $input);
    $input = substr($input, strpos($input, "\n")+1);
    $input = str_replace("\n", ' ', $input);
    $input = str_replace("\r", '', $input);
    $input = str_replace("&quot;", "#", $input);
    $input = preg_replace("/##([0-9]{5,9})##/","\n$1##", $input);
    $input = str_replace("##", "\t", $input);
    $input = str_replace("#", '', $input);
    return $input;
}

function toIdList($input){
    $input = preg_replace('/[^\d]+/', ',', trim($input));
    $input = trim($input, ',');
    return $input;
}

function toIdListUnique($input){
    $input = implode(',', array_unique(explode(',', toIdList($input))));
    return $input;
}

function tableColEncode($input){
    $cols = array();
    $match = array();
    $rows = preg_split('@\n@', $input);
    foreach($rows as $row){
        if(preg_match('@^\| (.*?)[[:space:]]+\|@s', $row, $match)) array_push($cols, $match[1]);
    }
    return implode(', ', $cols);
}

function getReadableSerializedString($serialized){
    $unserialized = unserialize(stripslashes($serialized));
    if($unserialized === false) $unserialized = 'FALSE or incorrect String!';
    return print_r($unserialized, true);
}

function htmlEncode($string){
    return htmlentities(htmlentities($string)); // Double Encode decoded by the Browser
}

function scramble_word(array $matches) {
    $word = $matches[1];
    if(strlen($word) <= 3) return $word;
    else {
        $new_word = $word;
        $count = 0;
        do {
            $new_word = $word[0] . str_shuffle(substr($word, 1, -1)) . $word[strlen($word) - 1];
            $count++;
        } while($new_word == $word && $count < 10);
        return $new_word;
    }
}

function scramble_words($input){
    return preg_replace_callback('/(\w+)/', 'scramble_word', $input);
}

function tags2lower($input){
    return preg_replace("/(<\/?)(\w+)([^>]*>)/e", "'\\1'.strtolower('\\2').'\\3'", $input);
}

function tags2upper($input){
    return preg_replace("/(<\/?)(\w+)([^>]*>)/e", "'\\1'.strtoupper('\\2').'\\3'", $input);
}

/**
 * Based on "nmmm at nmmm dot nu"s code
 * @see https://www.php.net/manual/en/function.hash.php#104987
 */
function hashes($input){
    $out = sprintf("%-12s %6s %s\n", 'Algorithm', 'Lenght', 'Hash');
    foreach(hash_algos() as $algo){
        $hash = hash($algo, $input, false);
        $out .= sprintf("%-12s %6d %s\n", $algo, strlen($hash), $hash);
    }
    return $out;
}

function json_string_decode($input){
    $decoded = json_decode(utf8_encode($input));
    if(!is_string($decoded)){
        $decoded = var_export($decoded, true);
    }
    return addslashes($decoded);
}

function json_string_encode($input){
    return addslashes(json_encode($input));
}


/**********************
 * Do the conversions *
 **********************/

class sadConv{

    protected $originalString;
    protected $convertetString = false;
    protected $method;
    protected $decode;
    protected $timeZone;
    protected $errorMSG = false;

    protected $methods = array(
        // Internal Name => Name, encode function, decode function
        'toIdList'         => array('To ID list', 'toIdList', false),
        'toIdListUnique'   => array('To ID list (Unique)', 'toIdListUnique', false),
        'quoted-printable' => array('Quoted-Printable', null, 'quoted_printable_decode'),
        'base64'           => array('MIME base64', 'base64_encode', 'base64_decode'),
        'rawurl'           => array('Raw URL (RFC 1738)', 'rawurlencode', 'rawurldecode'),
        'url'              => array('URL', 'urlencode', 'urldecode'),
        'utf8'             => array('UTF8', 'utf8_encode', 'utf8_decode'),
        'md5'              => array('MD5', 'md5', false),
        'hashes'           => array('Hashes', 'hashes', false),
        'soundex'          => array('Soundex', 'soundex', false),
        'rot13'            => array('ROT13', 'str_rot13', 'str_rot13'),
        'scramble_words'   => array('Scramble Words', 'scramble_words', null),
        'serialize'        => array('Serialize', 'serialize', 'getReadableSerializedString'),
        'slashes'          => array('Slashes', 'addslashes', 'stripslashes'),
        'nl2br'            => array('New Line 2 <br />', 'nl2br', null),
        'htmlentities'     => array('HTML Entites', 'htmlEncode', 'html_entity_decode'),
        'strip_tags'       => array('Strip HTML Tags', false, 'strip_tags'),
        'json_string'      => array('JSON String', 'json_string_encode', 'json_string_decode'),
        'tags2lower'       => array('Tags2lower', 'tags2lower', 'tags2upper'),
        'strtoupper'       => array('String to upper', 'strtoupper', 'strtolower'),
        'ucwords'          => array('First in word to upper', 'ucwords', null),
        'reverse'          => array('String reverse', 'strrev', 'strrev'),
        'ustamp'           => array('Unix time stamp', 'strtotime', 'uStamp2Date'),
        'ustampmillis'     => array('Unix time stamp (milliseconds)', null, 'uStampMillis2Date'),
        'dateTime2Iso8601' => array('DateTime/zone to ISO 8601', 'dateTime2Iso8601', null),
        'hourdec2hourmin'  => array('Hour decimal 2 Hour sexagesimal', 'hourDec2hurSex', 'hourSex2hourDec'),
        'schopferTrans'    => array('Sch&ouml;pferTrans', false, 'schoepferExportTranslator'),
        'tableCols'        => array('Table Col List', 'tableColEncode', null),
        'countBytes'       => array('Count Bytes', 'strlen', null),
        'countChar'        => array('Count characters', 'mb_strlen', null),
        'quack'            => array('Quack', '(new Quack())->encode', '(new Quack())->decode'),
    );

    public function __construct($InputString, $Method, $Way, $TimeZone){
        $this->originalString = trim($InputString);
        $this->method = $Method;
        if($Way == 'decode') $this->decode = true;
        else $this->decode = false;
        $this->timeZone = $TimeZone;
    }

    public function printImputForm(){
        $methodList = '';
        foreach($this->methods as $methodName => $method){
            $methodList .= '<option value="'.$methodName.'"';
            if($this->method == $methodName) $methodList .= ' selected="selected"';
            $methodList .= '>'.$method[0]."</option>\n";
        }

        $timeZones = $this->getOptionList(timezone_identifiers_list(), $this->timeZone, false);

        $form = '<form action="'.$_SERVER['PHP_SELF'].'" method="post" target="_self">'."\n".
                '<textarea name="InputString" id="InputString" cols="120" rows="12">'.htmlspecialchars(stripslashes($this->originalString)).'</textarea> <label for="InputString" class="textarea">In</label><br />'."\n".
                '<select name="Method" size="1">'."\n".$methodList.'</select>'."\n".
                '<div class="options">&nbsp;<input type="Radio" name="Way" value="decode"'.($this->decode?' checked="checked"':'').' /><label for="Way">decode</label>&nbsp;<input type="Radio" name="Way" value="encode"'.(!$this->decode?' checked="checked"':'').' /><label for="Way">encode</label>'."\n".
                '&nbsp;<input type="Checkbox" name="AsFile" value="yes" /><label for="AsFile">Download&nbsp;result</label></div>'."\n".
                '&nbsp;<input type="Submit" name="convert" value="Convert">'."\n".
                '&nbsp;&nbsp;&nbsp;<input type="Button" name="clear" value="Clear" onclick="javascript:document.getElementById(\'InputString\').value=\'\';">'."\n".
                '<br/><br/><select name="Timezone" size="1">'."\n".$timeZones.'</select>'."\n".
                "</form>\n";

        echo $form;

    }

    protected function getOptionList(Array $values, $selected = false, $writeValue = true){
        $options = '';
        foreach($values as $key => $value){
            $options .= '<option';
            if($writeValue) $options .= ' value="'.$key.'"';
            else $key = $value;
            if($selected == $key) $options .= ' selected="selected"';
            $options .= '>'.$value."</option>\n";
        }
        return $options;
    }

    public function convert(){
        if($this->originalString == '') return false;
        if(!array_key_exists($this->method, $this->methods)) return false;

        $method = $this->methods[$this->method];
        $methodFunction = $this->decode?$method[2]:$method[1];

        if($methodFunction === null){
            $this->errorMSG = 'Not supported';
            return false;
        }

        if($methodFunction === false){
            $this->errorMSG = 'Not possible';
            return false;
        }

        if(is_string($methodFunction)){
            $this->convertetString = eval("return $methodFunction(\$this->originalString);");
            if($this->convertetString === null) sad('false!');
            return true;
        }
    } // End function convert

    public function printOutput(){
        if(!$this->errorMSG && !($this->convertetString === false)){
            $form = '<textarea name="OutputString" cols="120" rows="24">'.stripslashes($this->convertetString).'</textarea> <label for="OutputString" class="textarea">Out</label>'."\n";
            echo $form;
        }
        if($this->errorMSG) echo '<b>Error: '.$this->errorMSG.'</b>';
    }

    public function sendOutputAsFile(){
        if(!$this->errorMSG && !($this->convertetString === false)){
            $output_file = 'sadConved.file';
            $convertetString = stripslashes($this->convertetString);

            header('Pragma: public');
            header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
            header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
            header('Cache-Control: pre-check=0, post-check=0, max-age=0'); // HTTP/1.1
            header('Content-Transfer-Encoding: none');
            header('Content-Type: application/octetstream; name="'.$output_file.'"'); //This should work for IE & Opera
            header('Content-Type: application/octet-stream; name="'.$output_file.'"'); //This should work for the rest
            header('Content-Disposition: inline; filename="'.$output_file.'"');
            header('Content-length: '.strlen($convertetString));

            echo $convertetString;
            exit();
        }
        if($this->errorMSG) echo '<b>Error: '.$this->errorMSG.'</b>';
    }

} // End class sadConv


if($_POST['AsFile'] ?? 'no' != 'yes'){
?><!DOCTYPE HTML><html>
<head>
<title>sadConv</title>
<meta name="generator" content="Ulli Meybohms HTML EDITOR">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<link rel="stylesheet" href="style.css" type="text/css" media="screen" />
</head>
<body text="#000000" bgcolor="#FFFFFF" link="#FF0000" alink="#FF0000" vlink="#FF0000">

<h1>sadConv 1.0</h1>

<?php
}

$conv = new sadConv($_POST['InputString'] ?? '', $_POST['Method'] ?? '', $_POST['Way'] ?? '', $timezone);
$conv->convert();
if($_POST['AsFile'] ?? 'no' != 'yes'){
 $conv->printOutput();
 $conv->printImputForm();
}else{
 $conv->sendOutputAsFile();
}
//phpinfo();

if($_POST['AsFile'] ?? 'no' != 'yes'){
?>

</body>
</html>
<?php
}
