<?php
$fp = fsockopen("172.31.5.13", 2065, $errno, $errstr, 30);
if (!$fp) {
    echo "$errstr ($errno)<br />\n";
} else {
    stream_set_timeout($fp, 1);
    $buffer = ""; 

    // Get and ignore the telnet options for now
    // consists of 12 bytes on my Cisco 2651XM
    $buffer = fgets($fp, 13); 

    // Answer with telnet options grabbed from command line 
    // telnet client 
    // FFFD01FFFD03FFFB18FFFB1FFFFA1F00CC0043FFF0
    $data = hex2bin("FFFE01FFFD03");
    fwrite($fp, $data); 

    // read and ignore 6 byte answer
    $buffer = fgets($fp, 7); 

    // Another answer grabbed from a console telnet session
    // FFFA1800787465726D2D323536636F6C6F72FFF0
    $data = hex2bin("FFFA1800787465726D2D323536636F6C6F72FFF0");
    fwrite($fp, $data); 

    // Now the console seems to be open and ready to receive commands
    // Send one carriage return
    $data = hex2bin("0D");
    fwrite($fp, $data);

    // Read echo and discard
    $buffer = fgets($fp, 5); 

    // Read command prompt (i.e. "]-O ")
    $buffer = fgets($fp, 6); 

    // Send command "dorap"
    $data = "dorap\r";
    fwrite($fp, $data);

    // Read and discard echo of "dorap" command
    $buffer = fgets($fp, 7);

    // Read and discard line feed
    $buffer = fgets($fp, 5); 

    // Read and discard prompt "RAP: "
    $buffer = fgets($fp, 6);

    // Send command "MTR TX_PA_P1"
    $data = "MTR TX_PA_P1\r";
    fwrite($fp, $data);

    // Read and discard echo of "MTR TX_PA_P1" command
    $buffer = fgets($fp, 13);

    // Read and discard line feed
    $buffer = fgets($fp, 5); 

    // Read output of MTR TX_PA_P1 command
    $i = 1;
    for ($i=1; $i < 60; $i++) {
       $buffer = fgets($fp, 1024);
       printf("$buffer <br>");
    }

    // Send command "exit"
    $data = "exit\r";
    fwrite($fp, $data);

    // close connection
    fclose($fp);
}
?>
