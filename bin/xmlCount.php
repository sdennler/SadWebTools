#!/usr/bin/env php
<?php

$file = $argv[1];

include_once dirname(__FILE__).'/../lib/lib.php';

$path = new path();
$counter = new counter();


function startElement($parser, $name, $attr)
{
    global $path, $counter;
    $path->down($name);
    $counter->add($path->pwd());
}

function endElement($parser, $name)
{
    global $path;
    $path->up();
}


$xml_parser = xml_parser_create();
xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);
xml_set_element_handler($xml_parser, 'startElement', 'endElement');
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


$counts = $counter->get();
ksort($counts);
print_r($counts);
