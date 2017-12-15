#!/usr/bin/php

<?php

include 'fb_settings.php';

$seed = str_split('abcdefghijklmnopqrstuvwxyz'
				 .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
				 .'0123456789');
shuffle($seed);

$randomSSID = 'WiFi-';
foreach (array_rand($seed, 8) as $k) $randomSSID .= $seed[$k];
echo "Random SSID:     " . $randomSSID . PHP_EOL;

$randomPSK = @simplexml_load_file("http://www.lipsum.com/feed/xml?amount=63&what=bytes&start=0")->lipsum;
if ($randomPSK == "" or strlen($randomPSK) < 32) {
	$randomPSK = '';
	foreach (array_rand($seed, 32) as $k) $randomPSK .= $seed[$k];
}
echo "Random PSK:      " . $randomPSK . PHP_EOL;

echo PHP_EOL;

foreach ($box_arr as $box) {
	$client = new SoapClient(
		null,
		array(
			'location'   => "http://".$box["IP"].":".$box["Port"]."/upnp/control/wlanconfig3",
			'uri'        => "urn:dslforum-org:service:WLANConfiguration:3",
			'noroot'     => True,
			'exceptions' => 0,
			'trace'      => 1,
			'login'      => $box["Login"],
			'password'   => $box["Password"]
		)
	);
	
	// WLAN einschalten
//	$client->SetEnable(new SoapParam(true,'NewEnable'));  
	
	$result = $client->{"GetInfo"}();
	if ((bool)$result['NewEnable']==1) {
		echo "==============================" . PHP_EOL . PHP_EOL;
		echo $box["IP"] .  " WiFi is enabled" . PHP_EOL;
		
		// get old SSID
		$OldSSID = $client->{"GetSSID"}();
		echo "Old SSID:        " . $OldSSID . PHP_EOL;
		
		// get old Security Keys 
		$result = $client->{"GetSecurityKeys"}();
		$OldKeyPassphrase=$result['NewKeyPassphrase'];
		echo "Old Passphrase:  " . $OldKeyPassphrase . PHP_EOL;
		echo PHP_EOL;
		
		// SSID ändern
		$client->SetSSID(new SoapParam($randomSSID,'NewSSID'));
		
		// get SSID
		$NewSSID = $client->{"GetSSID"}();
		echo "New SSID:        " . $NewSSID . PHP_EOL;
		
		//// set new passphrase
		$response = $client->SetSecurityKeys(
			new SoapParam("",'NewWEPKey0'),
			new SoapParam("",'NewWEPKey1'),
			new SoapParam("",'NewWEPKey2'),
			new SoapParam("",'NewWEPKey3'),
			new SoapParam("",'NewPreSharedKey'),
			new SoapParam("$randomPSK",'NewKeyPassphrase')
			);
		
		// get Security Keys 
		$result = $client->{"GetSecurityKeys"}();
		$NewKeyPassphrase=$result['NewKeyPassphrase'];
		echo "New Passphrase:  " . $NewKeyPassphrase . PHP_EOL;
	} else {
		echo $box["IP"] .  " WiFi is disabled" . PHP_EOL;
	}
	echo PHP_EOL;
}

?>
