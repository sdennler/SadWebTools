<?php
require_once __DIR__.'/../lib/sadConv.lib.php';

class sadConvTest extends PHPUnit_Framework_TestCase{

    protected $hourMinData = array(
        "00:00" => "0.00",
        "00:01" => "0.02",
        "00:02" => "0.03",
        "00:03" => "0.05",
        "00:04" => "0.07",
        "00:05" => "0.08",
        "00:06" => "0.10",
        "00:07" => "0.12",
        "00:08" => "0.13",
        "00:09" => "0.15",
        "00:10" => "0.17",
        "00:11" => "0.18",
        "00:12" => "0.20",
        "00:13" => "0.22",
        "00:14" => "0.23",
        "00:15" => "0.25",
        "00:16" => "0.27",
        "00:17" => "0.28",
        "00:18" => "0.30",
        "00:19" => "0.32",
        "00:20" => "0.33",
        "00:21" => "0.35",
        "00:22" => "0.37",
        "00:23" => "0.38",
        "00:24" => "0.40",
        "00:25" => "0.42",
        "00:26" => "0.43",
        "00:27" => "0.45",
        "00:28" => "0.47",
        "00:29" => "0.48",
        "00:30" => "0.50",
        "00:31" => "0.52",
        "00:32" => "0.53",
        "00:33" => "0.55",
        "00:34" => "0.57",
        "00:35" => "0.58",
        "00:36" => "0.60",
        "00:37" => "0.62",
        "00:38" => "0.63",
        "00:39" => "0.65",
        "00:40" => "0.67",
        "00:41" => "0.68",
        "00:42" => "0.70",
        "00:43" => "0.72",
        "00:44" => "0.73",
        "00:45" => "0.75",
        "00:46" => "0.77",
        "00:47" => "0.78",
        "00:48" => "0.80",
        "00:49" => "0.82",
        "00:50" => "0.83",
        "00:51" => "0.85",
        "00:52" => "0.87",
        "00:53" => "0.88",
        "00:54" => "0.90",
        "00:55" => "0.92",
        "00:56" => "0.93",
        "00:57" => "0.95",
        "00:58" => "0.97",
        "00:59" => "0.98",
        "01:00" => "1.00",

        "01:10" => "1.17",
        "02:12" => "2.20",
        "10:00" => "10.00",
        "10:12" => "10.20",
        "100:12" => "100.20",
    );

    public function testHourDec2hurSex(){
        foreach($this->hourMinData as $min => $dec){
            $this->assertSame($min, hourDec2hurSex($dec));
        }
        $this->assertSame("00:01", hourDec2hurSex("0.01"));
        $this->assertSame("00:02", hourDec2hurSex("0.04"));
        $this->assertSame("00:04", hourDec2hurSex("0.06"));
        $this->assertSame("00:05", hourDec2hurSex("0.09"));
        $this->assertSame("00:07", hourDec2hurSex("0.11"));
        $this->assertSame("00:08", hourDec2hurSex("0.14"));
        $this->assertSame("00:10", hourDec2hurSex("0.16"));
        $this->assertSame("00:11", hourDec2hurSex("0.19"));
        $this->assertSame("00:13", hourDec2hurSex("0.21"));
        $this->assertSame("00:14", hourDec2hurSex("0.24"));
        $this->assertSame("00:16", hourDec2hurSex("0.26"));
        $this->assertSame("00:17", hourDec2hurSex("0.29"));
        $this->assertSame("00:19", hourDec2hurSex("0.31"));
        $this->assertSame("00:20", hourDec2hurSex("0.34"));
        $this->assertSame("00:22", hourDec2hurSex("0.36"));
        $this->assertSame("00:23", hourDec2hurSex("0.39"));
        $this->assertSame("00:25", hourDec2hurSex("0.41"));
        $this->assertSame("00:26", hourDec2hurSex("0.44"));
        $this->assertSame("00:28", hourDec2hurSex("0.46"));
        $this->assertSame("00:29", hourDec2hurSex("0.49"));
        $this->assertSame("00:31", hourDec2hurSex("0.51"));
        $this->assertSame("00:32", hourDec2hurSex("0.54"));
        $this->assertSame("00:34", hourDec2hurSex("0.56"));
        $this->assertSame("00:35", hourDec2hurSex("0.59"));
        $this->assertSame("00:37", hourDec2hurSex("0.61"));
        $this->assertSame("00:38", hourDec2hurSex("0.64"));
        $this->assertSame("00:40", hourDec2hurSex("0.66"));
        $this->assertSame("00:41", hourDec2hurSex("0.69"));
        $this->assertSame("00:43", hourDec2hurSex("0.71"));
        $this->assertSame("00:44", hourDec2hurSex("0.74"));
        $this->assertSame("00:46", hourDec2hurSex("0.76"));
        $this->assertSame("00:47", hourDec2hurSex("0.79"));
        $this->assertSame("00:49", hourDec2hurSex("0.81"));
        $this->assertSame("00:50", hourDec2hurSex("0.84"));
        $this->assertSame("00:52", hourDec2hurSex("0.86"));
        $this->assertSame("00:53", hourDec2hurSex("0.89"));
        $this->assertSame("00:55", hourDec2hurSex("0.91"));
        $this->assertSame("00:56", hourDec2hurSex("0.94"));
        $this->assertSame("00:58", hourDec2hurSex("0.96"));
        $this->assertSame("00:59", hourDec2hurSex("0.99"));

        $this->assertSame("00:00", hourDec2hurSex("0.0"));
        $this->assertSame("00:06", hourDec2hurSex("0.1"));
        $this->assertSame("00:12", hourDec2hurSex("0.2"));
        $this->assertSame("00:18", hourDec2hurSex("0.3"));
        $this->assertSame("00:24", hourDec2hurSex("0.4"));
        $this->assertSame("00:30", hourDec2hurSex("0.5"));
        $this->assertSame("00:36", hourDec2hurSex("0.6"));
        $this->assertSame("00:42", hourDec2hurSex("0.7"));
        $this->assertSame("00:48", hourDec2hurSex("0.8"));
        $this->assertSame("00:54", hourDec2hurSex("0.9"));

        $this->assertSame("01:01", hourDec2hurSex("1.01"));
        $this->assertSame("02:14", hourDec2hurSex("2.24"));
        $this->assertSame("03:22", hourDec2hurSex("3.36"));
        $this->assertSame("09:29", hourDec2hurSex("9.49"));
        $this->assertSame("10:37", hourDec2hurSex("10.61"));
        $this->assertSame("22:44", hourDec2hurSex("22.74"));
        $this->assertSame("151:52", hourDec2hurSex("151.86"));
        $this->assertSame("1220:59", hourDec2hurSex("1220.99"));

        $this->assertSame("00:00", hourDec2hurSex("0.001"));
        $this->assertSame("00:01", hourDec2hurSex("0.009"));
        $this->assertSame("00:01", hourDec2hurSex("0.0099"));
        $this->assertSame("00:01", hourDec2hurSex("0.024"));
        $this->assertSame("00:02", hourDec2hurSex("0.034"));
        $this->assertSame("00:02", hourDec2hurSex("0.025"));
        $this->assertSame("00:03", hourDec2hurSex("0.045"));
        $this->assertSame("00:01", hourDec2hurSex("0.0159"));
        $this->assertSame("00:01", hourDec2hurSex("0.0199"));
        $this->assertSame("00:59", hourDec2hurSex("0.99"));
        $this->assertSame("00:59", hourDec2hurSex("0.994"));
        $this->assertSame("01:00", hourDec2hurSex("0.995"));
    }

    public function testHurSex2hourDec(){
        foreach($this->hourMinData as $min => $dec){
            $this->assertSame($dec, hourSex2hourDec($min));
        }
        $this->assertSame("1.00", hourSex2hourDec("00:60"));
        $this->assertSame("6.02", hourSex2hourDec("05:61"));
        $this->assertSame("9.58", hourSex2hourDec("08:95"));
        $this->assertSame("10.00", hourSex2hourDec("10:00"));
        $this->assertSame("22.70", hourSex2hourDec("22:42"));
        $this->assertSame("222.70", hourSex2hourDec("222:42"));
        $this->assertSame("3222.93", hourSex2hourDec("3222:56"));
    }

}
