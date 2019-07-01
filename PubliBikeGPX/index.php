<?php

require_once 'vendor/autoload.php';

use phpGPX\Models\GpxFile;
use phpGPX\Models\Link;
use phpGPX\Models\Metadata;
use phpGPX\Models\Point;

$url = 'https://api.publibike.ch/v1/public/stations';


$stations = json_decode(file_get_contents($url), true);


$gpx_file = new GpxFile();
$gpx_file->metadata = new Metadata();
$gpx_file->metadata->time = new \DateTime();
$gpx_file->metadata->description = "PubliBike Stations";
$link = new Link();
$link->href = "https://www.publibike.ch/de/publibike/stations";
$link->text = 'PubliBike';
$gpx_file->metadata->links[] = $link;


foreach($stations as $station){
    $point = new Point(Point::WAYPOINT);
    $point->name = 'PubliBike Station #'.$station['id'];
    $point->latitude = $station['latitude'];
    $point->longitude = $station['longitude'];
    $gpx_file->waypoints[] = $point;
}

header("Content-Type: application/gpx+xml");
header("Content-Disposition: attachment; filename=PubliBikeStations.gpx");
echo $gpx_file->toXML()->saveXML();
exit();
