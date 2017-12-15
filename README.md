fb_rnd_guest_wifi
=================

With these PHP scripts you can easily change the SSID and the PSK of your Fritz!Box guest access point.
The new SSID will be named by the prefix "WiFi-" followed by 8 randomized chars.
The new PSK will be created randomly by a 63 chars long Lorem Ipsum string right from the page http://www.lipsum.com
As a fall back (maybe if lipsum.com is not reachable), the PSK fill be randomly filled up with 32 chars.


Configure
---------
Please replace in the file fb_settings.php "Username" and "Password" to your Fritz!Box credentials. If you have more then one Fritz!Box or maybe a Fritz!Powerline you can add these to the fb_settingsfile.

Usage
-----
Just start the script it will show you the new SSID and PSK, followed by the old settings and if successful, the new settings for each defined device.
