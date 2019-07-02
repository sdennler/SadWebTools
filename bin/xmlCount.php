#!/usr/bin/env php
<?php

$file = $argv[1];

include_once dirname(__FILE__).'/../lib/lib.php';

$path = new path();
$nodeCounter = new counter();
$nodeWithContentCounter = new counter();
$curNodeHasData = false;


function startElement($parser, $name, $attr)
{
    global $path, $nodeCounter, $curNodeHasData;
    $path->down($name);
    $nodeCounter->add($path->pwd());
    $curNodeHasData = false;
}

function endElement($parser, $name)
{
    global $path, $curNodeHasData, $nodeWithContentCounter;
    if($curNodeHasData){
        $nodeWithContentCounter->add($path->pwd());
    }
    $path->up();
}

function content($parser, $data)
{
    global $curNodeHasData;
    if(trim($data)){
        $curNodeHasData = true;
    }
}


$xml_parser = xml_parser_create();
xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);
xml_set_element_handler($xml_parser, 'startElement', 'endElement');
xml_set_character_data_handler($xml_parser, 'content');
if (!($fp = fopen($file, 'r'))) {
    die('Could not open XML');
}

while ($data = fread($fp, 4096)) {
    if (!xml_parse($xml_parser, $data, feof($fp))) {
        die(sprintf('XML error: %s at line %d',
                    xml_error_string(xml_get_error_code($xml_parser)),
                    xml_get_current_line_number($xml_parser)));
    }
}
xml_parser_free($xml_parser);

echo "All nodes:\n";
$counts = $nodeCounter->get();
ksort($counts);
print_r($counts);

echo "All non empty nodes:\n";
$counts = $nodeWithContentCounter->get();
ksort($counts);
print_r($counts);
