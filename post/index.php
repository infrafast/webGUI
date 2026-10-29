<?php

///////////////////////////////////////////// 
//         SESSION DEFINITION
///////////int//////////////////////////////////
session_start();

if (!isset($_SESSION['mixerConnected'])) {
  $_SESSION['mixerConnected'] = 'disconnected';
}
if (!isset($_SESSION['serverConnected'])) {
  $_SESSION['serverConnected'] = 'true';
}
if (!isset($_SESSION['serverName'])) {
  $_SESSION['serverName'] = "auto";
} 
if (!isset($_SESSION['serverIP'])) {
  $_SESSION['serverIP'] =  "0.0.0.0";
}
if (!isset($_SESSION['mixerPort'])) {
  $_SESSION['mixerPort'] = "0";
} 
if (!isset($_SESSION['serverPort'])) {
  $_SESSION['serverPort'] = '10023';
} 
if (!isset($_SESSION['lang'])) {
  $_SESSION['lang'] = 'francais'; 
}
if (!isset($_SESSION['liveserverIP'])) {
  $_SESSION['liveserverIP'] = "127.0.0.1";
}
if (!isset($_SESSION['useNetwork'])) {
  $_SESSION['useNetwork'] = 1;
}
if (!isset($_SESSION['cable'])) {
  $_SESSION['cable'] = 1;
}
if (!isset($_SESSION['networkState'])) {
  $_SESSION['networkState'] = 1;
}
if (!isset($_SESSION['AP'])) {
  $_SESSION['AP'] = 0;
}
if (!isset($_SESSION['pushDelay'])) {
  $_SESSION['pushDelay'] = 550;
}
if (!isset($_SESSION['nextStepQueue'])) {
  $_SESSION['nextStepQueue'] = []; 
}
if (!isset($_SESSION['progress'])) {
  $_SESSION['progress'] = 0; 
}
if (!isset($_SESSION['discovery'])){
    $_SESSION['discovery'] = "manual";
}


///////////////////////////////////////////// 
//         GLOBAL JSON VARIABLES
///////////////////////////////////////////// 


$doNothing = '{}';
$setSaveButton = '{"setSaveButton":"1"}';


$interfaces = '
  "interfaces": 
[{"system":{"enabled":false,"exposed":false,"hasStrip":true,"group":"Utils","provider":"Utils","icon":"F","properties":[]}},{"Directory":{"enabled":false,"exposed":true,"hasStrip":false,"group":"Utils","provider":"Utils","icon":"F","properties":["/port/0","/ip/0.0.0.0"],"state":"disabled","progress":-1,"details":"Device is not detectable on the network. Use 0.0.0.0 to connect","activable":true,"led":"black"}},{"WebApplication":{"enabled":true,"exposed":false,"hasStrip":false,"group":"Utils","provider":"Utils","icon":"F","properties":["/port/80","/ip/0.0.0.0"]}},{"MixingStation":{"enabled":false,"exposed":true,"hasStrip":true,"group":"Mixer","provider":"MixingStation","icon":"F","properties":["/port/8088","/ip/0.0.0.0"],"state":"Offline","progress":-1,"details":"","activable":true,"led":"black"}},{"XMSeries":{"enabled":true,"exposed":true,"hasStrip":true,"group":"Mixer","provider":"XMSeries","icon":"g","properties":["/connect/1","/port/10023","/ip/192.168.0.1","/ping//xinfo"],"state":"live","progress":-1,"details":"X32OnlineIntervalDemo xm32 192.168.0.1:10023","activable":true,"led":"green"}},{"USBMIDI":{"enabled":false,"exposed":true,"hasStrip":true,"group":"MIDI","provider":"MIDI","icon":"f","properties":[],"state":"disabled","progress":-1,"details":"restartUSB","activable":false,"led":"black"}},{"BLEMIDI":{"enabled":true,"exposed":true,"hasStrip":true,"group":"MIDI","provider":"MIDI","icon":"e","properties":[],"state":"pending","progress":-1,"details":"Connect Blutooth interval","activable":false,"led":"blue medium"}},{"DINMIDI":{"enabled":false,"exposed":true,"hasStrip":true,"group":"MIDI","provider":"MIDI","icon":"c","properties":[],"state":"unavailable","progress":-1,"details":"details","activable":false,"led":"black"}},{"sysOSC":{"enabled":false,"exposed":true,"hasStrip":true,"group":"OSC","provider":"OSC","icon":"g","properties":["/port/5000","/ip/1.1.1.1","/ping//info"],"state":"disabled","progress":-1,"details":"Interface is disabled","activable":true,"led":"black"}},{"Artnet":{"enabled":false,"exposed":true,"hasStrip":true,"group":"DMX","provider":"DMX","icon":"b","properties":[],"state":"unavailable","progress":-1,"details":"Interface not yet implemented","activable":false,"led":"black"}},{"3PinDMX":{"enabled":false,"exposed":true,"hasStrip":true,"group":"DMX","provider":"DMX","icon":"a","properties":[],"state":"unavailable","progress":-1,"details":"Interface not yet implemented","activable":false,"led":"black"}},{"AppleMIDI":{"enabled":false,"exposed":true,"hasStrip":true,"group":"MIDI","provider":"MIDI","icon":"d","properties":["/session/passive"],"state":"disabled","progress":-1,"details":"Interface is disabled","activable":true,"led":"black"}}]
';

$startup ='
{"langs":["francais","english"],"maxPOSTsize":4096,"safeScanfSpace":"^","sysexLibFile":"sysexlib.json","oscLibFile":"osclib.json","wizard":false,"lang":"francais","configNames":["default"],"startupUpdate":true} 
';	

$jsonInput =  '"jsonInput": 
[{"system":"Utils","properties":[]},{"system":"Utils","properties":[{"/text/%s":["freetext","free text","string"]}]},{"Directory":"Utils","properties":[]},{"WebApplication":"Utils","properties":[{"/port/%d":["freetext","port","0-65535"]},{"/ip/%s":["freetext","ip","ip"]}]},{"jump":"shortcut","properties":[{"/strip/id/%s":["dropdown"]}]},{"macro":"shortcut","properties":[{"/macro/name/%s":["dropdown"]},{"/action/%s":["dropdown"]}]},{"advanced":"shortcut","properties":[{"/command/%s":["freetext","command","string"]}]},{"jump":"shortcut","properties":[{"/strip/id/%s":["dropdown"]}]},{"macro":"shortcut","properties":[{"/macro/name/%s":["dropdown"]},{"/action/%s":["dropdown"]}]},{"advanced":"shortcut","properties":[{"/command/%s":["freetext","command","string"]}]},{"MixingStation":"Mixer","properties":[{"/port/%d":["freetext","port","0-65535"]},{"/ip/%s":["freetext","ip","ip"]}]},{"XMSeries":"Mixer","properties":[{"/port/%d":["freetext","port","0-65535"]},{"/ip/%s":["freetext","ip","ip"]},{"/connect/%d":["dropdown"]},{"/model/%s":["dropdown"]}]},{"ch2bus":"Mixer","properties":[{"/chname/%d":["dropdown"]},{"/busname/%d":["dropdown"]}],"icon":0},{"chGain":"Mixer","properties":[{"/chname/%d":["dropdown"]}],"icon":0},{"text":"Utils","properties":[{"/text/%s":["freetext","free text","string"]}],"icon":68},{"Note":"MIDI","properties":[{"/channel/%d":["dropdown"]},{"/note/%s":["dropdown"]},{"/value1/%s":["freetext","note","0-126"]},{"/value2/%s":["freetext","velocity","0-126"]}],"icon":255},{"Sysex":"MIDI","properties":[{"/sysex/%s":["dropdown"]}],"icon":94},{"SendOSC":"OSC","properties":[{"/osc/%s":["dropdown"]}],"icon":0},{"USBMIDI":"MIDI","properties":[]},{"BLEMIDI":"MIDI","properties":[]},{"DINMIDI":"MIDI","properties":[]},{"sysOSC":"OSC","properties":[{"/port/%d":["freetext","port","0-65535"]},{"/ip/%s":["freetext","ip","ip"]},{"/ping/%s":["freetext","ping","string"]}]},{"Artnet":"DMX","properties":[]},{"3PinDMX":"DMX","properties":[]},{"AppleMIDI":"MIDI","properties":[{"/session/%s":["dropdown"]}]}]
';

$skeletons = '"skeletons": 
[{"ch2bus":"Mixer","properties":[{"/chname/%d":["dropdown"]},{"/busname/%d":["dropdown"]}],"icon":0},{"chGain":"Mixer","properties":[{"/chname/%d":["dropdown"]}],"icon":0},{"text":"Utils","properties":[{"/text/%s":["freetext","free text","string"]}],"icon":68},{"Note":"MIDI","properties":[{"/channel/%d":["dropdown"]},{"/note/%s":["dropdown"]},{"/value1/%s":["freetext","note","0-126"]},{"/value2/%s":["freetext","velocity","0-126"]}],"icon":255},{"Sysex":"MIDI","properties":[{"/sysex/%s":["dropdown"]}],"icon":94},{"SendOSC":"OSC","properties":[{"/osc/%s":["dropdown"]}],"icon":0}]
';


$banks = '"banks":
{"Midi":{"layout":2,"icon":3,"strips":[{"type":"Sysex","color":0,"icon":5,"size":"1x4","interface":"AppleMIDI","position":{"x":3,"y":0,"id":4,"win":0},"properties":["/sysex/toto"]},{"type":"Note","color":0,"icon":5,"size":"1x4","interface":"DINMIDI","position":{"x":2,"y":0,"id":3,"win":0},"properties":["/channel/1","/value1/11","/value2/33","/note/Off"]},{"type":"Note","color":0,"icon":5,"size":"1x4","interface":"BLEMIDI","position":{"x":1,"y":0,"id":2,"win":0},"properties":["/channel/1","/value1/12","/value2/43","/note/Off"]},{"type":"Sysex","color":0,"icon":5,"size":"1x4","interface":"USBMIDI","position":{"x":0,"y":0,"id":1,"win":0},"properties":["/sysex/firstkey"]},{"type":"Sysex","color":0,"icon":5,"size":"4x1","interface":"USBMIDI","position":{"x":0,"y":0,"id":5,"win":1},"properties":["/sysex/firstkey"]},{"type":"text","color":0,"icon":5,"size":"2x2","interface":"system","position":{"x":0,"y":1,"id":6,"win":1},"properties":["/text/mediumsizetext"]},{"type":"Sysex","color":0,"icon":5,"size":"2x2","interface":"BLEMIDI","position":{"x":2,"y":1,"id":8,"win":1},"properties":["/sysex/firstkey"]},{"type":"text","color":0,"icon":5,"size":"4x1","interface":"system","position":{"x":0,"y":3,"id":7,"win":1},"properties":["/text/verylongstringsizetext"]},{"type":"Note","color":0,"icon":5,"size":"1x4","interface":"AppleMIDI","position":{"x":0,"y":0,"id":9,"win":2},"properties":["/channel/1","/value1/11","/value2/23","/note/Off"]},{"type":"Note","color":0,"icon":5,"size":"2x2","interface":"USBMIDI","position":{"x":1,"y":0,"id":10,"win":2},"properties":["/channel/1","/value1/52","/value2/57","/note/Off"]},{"type":"Note","color":0,"icon":5,"size":"2x2","interface":"USBMIDI","position":{"x":1,"y":2,"id":11,"win":2},"properties":["/channel/1","/value1/52","/value2/57","/note/Off"]},{"type":"Note","color":0,"icon":5,"size":"1x4","interface":"AppleMIDI","position":{"x":3,"y":0,"id":12,"win":2},"properties":["/channel/1","/value1/11","/value2/23","/note/Off"]}]},"midi2":{"layout":2,"icon":3,"strips":[{"type":"Sysex","color":0,"icon":5,"size":"1x4","interface":"USBMIDI","position":{"x":0,"y":0,"id":1,"win":0},"properties":["/sysex/firstkey"]},{"type":"Note","color":0,"icon":5,"size":"1x4","interface":"BLEMIDI","position":{"x":1,"y":0,"id":2,"win":0},"properties":["/channel/1","/value1/12","/value2/43","/note/Off"]},{"type":"Note","color":0,"icon":5,"size":"1x4","interface":"DINMIDI","position":{"x":2,"y":0,"id":3,"win":0},"properties":["/channel/1","/value1/11","/value2/33","/note/Off"]},{"type":"Sysex","color":0,"icon":5,"size":"1x4","interface":"AppleMIDI","position":{"x":3,"y":0,"id":4,"win":0},"properties":["/sysex/toto"]},{"type":"Sysex","color":0,"icon":5,"size":"4x1","interface":"USBMIDI","position":{"x":0,"y":0,"id":5,"win":1},"properties":["/sysex/firstkey"]},{"type":"text","color":0,"icon":5,"size":"2x2","interface":"system","position":{"x":0,"y":1,"id":6,"win":1},"properties":["/text/mediumsizetext"]},{"type":"text","color":0,"icon":5,"size":"4x1","interface":"system","position":{"x":0,"y":3,"id":7,"win":1},"properties":["/text/verylongstringsizetext"]},{"type":"Sysex","color":0,"icon":5,"size":"2x2","interface":"BLEMIDI","position":{"x":2,"y":1,"id":8,"win":1},"properties":["/sysex/firstkey"]},{"type":"Note","color":0,"icon":5,"size":"1x4","interface":"AppleMIDI","position":{"x":0,"y":0,"id":9,"win":2},"properties":["/channel/1","/value1/11","/value2/23","/note/Off"]},{"type":"Note","color":0,"icon":5,"size":"2x2","interface":"USBMIDI","position":{"x":1,"y":0,"id":10,"win":2},"properties":["/channel/1","/value1/52","/value2/57","/note/Off"]},{"type":"Note","color":0,"icon":5,"size":"2x2","interface":"USBMIDI","position":{"x":1,"y":2,"id":11,"win":2},"properties":["/channel/1","/value1/52","/value2/57","/note/Off"]},{"type":"Note","color":0,"icon":5,"size":"1x4","interface":"AppleMIDI","position":{"x":3,"y":0,"id":12,"win":2},"properties":["/channel/1","/value1/11","/value2/23","/note/Off"]}]},"xmseries":{"layout":2,"icon":3,"strips":[{"type":"ch2bus","color":0,"icon":5,"size":"1x4","interface":"XMSeries","position":{"x":1,"y":0,"id":2,"win":0},"properties":["/chname/2","/busname/3"]},{"type":"ch2bus","color":0,"icon":5,"size":"1x4","interface":"XMSeries","position":{"x":0,"y":0,"id":1,"win":0},"properties":["/chname/1","/busname/3"]},{"type":"ch2bus","color":0,"icon":5,"size":"1x4","interface":"XMSeries","position":{"x":3,"y":0,"id":12,"win":2},"properties":["/chname/12","/busname/3"]},{"type":"ch2bus","color":0,"icon":5,"size":"1x4","interface":"XMSeries","position":{"x":2,"y":0,"id":11,"win":2},"properties":["/chname/11","/busname/3"]},{"type":"ch2bus","color":0,"icon":5,"size":"1x4","interface":"XMSeries","position":{"x":1,"y":0,"id":10,"win":2},"properties":["/chname/10","/busname/3"]},{"type":"ch2bus","color":0,"icon":5,"size":"1x4","interface":"XMSeries","position":{"x":0,"y":0,"id":9,"win":2},"properties":["/chname/9","/busname/3"]},{"type":"ch2bus","color":0,"icon":5,"size":"1x4","interface":"XMSeries","position":{"x":2,"y":0,"id":7,"win":1},"properties":["/chname/7","/busname/3"]},{"type":"ch2bus","color":0,"icon":5,"size":"1x4","interface":"XMSeries","position":{"x":3,"y":0,"id":8,"win":1},"properties":["/chname/8","/busname/3"]},{"type":"ch2bus","color":0,"icon":5,"size":"1x4","interface":"XMSeries","position":{"x":0,"y":0,"id":5,"win":1},"properties":["/chname/5","/busname/3"]},{"type":"ch2bus","color":0,"icon":5,"size":"1x4","interface":"XMSeries","position":{"x":1,"y":0,"id":6,"win":1},"properties":["/chname/6","/busname/3"]},{"type":"ch2bus","color":0,"icon":5,"size":"1x4","interface":"XMSeries","position":{"x":3,"y":0,"id":4,"win":0},"properties":["/chname/4","/busname/3"]},{"type":"ch2bus","color":0,"icon":5,"size":"1x4","interface":"XMSeries","position":{"x":2,"y":0,"id":3,"win":0},"properties":["/chname/3","/busname/3"]}]},"mstation":{"layout":2,"icon":3,"strips":[{"type":"ch2bus","color":0,"icon":5,"size":"1x4","interface":"MixingStation","position":{"x":3,"y":0,"id":12,"win":2},"properties":["/chname/12","/busname/3"]},{"type":"ch2bus","color":0,"icon":5,"size":"1x4","interface":"MixingStation","position":{"x":2,"y":0,"id":11,"win":2},"properties":["/chname/11","/busname/3"]},{"type":"ch2bus","color":0,"icon":5,"size":"1x4","interface":"MixingStation","position":{"x":1,"y":0,"id":10,"win":2},"properties":["/chname/10","/busname/3"]},{"type":"ch2bus","color":0,"icon":5,"size":"1x4","interface":"MixingStation","position":{"x":0,"y":0,"id":9,"win":2},"properties":["/chname/9","/busname/3"]},{"type":"ch2bus","color":0,"icon":5,"size":"1x4","interface":"MixingStation","position":{"x":3,"y":0,"id":8,"win":1},"properties":["/chname/8","/busname/3"]},{"type":"ch2bus","color":0,"icon":5,"size":"1x4","interface":"MixingStation","position":{"x":2,"y":0,"id":7,"win":1},"properties":["/chname/7","/busname/3"]},{"type":"ch2bus","color":0,"icon":5,"size":"1x4","interface":"MixingStation","position":{"x":1,"y":0,"id":6,"win":1},"properties":["/chname/6","/busname/3"]},{"type":"ch2bus","color":0,"icon":5,"size":"1x4","interface":"MixingStation","position":{"x":0,"y":0,"id":5,"win":1},"properties":["/chname/5","/busname/3"]},{"type":"ch2bus","color":0,"icon":5,"size":"1x4","interface":"MixingStation","position":{"x":3,"y":0,"id":4,"win":0},"properties":["/chname/4","/busname/3"]},{"type":"ch2bus","color":0,"icon":5,"size":"1x4","interface":"MixingStation","position":{"x":2,"y":0,"id":3,"win":0},"properties":["/chname/3","/busname/3"]},{"type":"ch2bus","color":0,"icon":5,"size":"1x4","interface":"MixingStation","position":{"x":1,"y":0,"id":2,"win":0},"properties":["/chname/2","/busname/3"]},{"type":"ch2bus","color":0,"icon":5,"size":"1x4","interface":"MixingStation","position":{"x":0,"y":0,"id":1,"win":0},"properties":["/chname/1","/busname/3"]}]}}
';


function getDataSets(){
    return '"stripdatasets": 
{"Utils":[{"address":"/text/%s","values":["hello0"]},{"address":"/provider/Utils/%s","values":["system","Directory","WebApplication"]},{"address":"/provider/Mixer/%s","values":["MixingStation","XMSeries"]},{"address":"/provider/MIDI/%s","values":["USBMIDI","BLEMIDI","DINMIDI","AppleMIDI"]},{"address":"/provider/OSC/%s","values":["sysOSC"]},{"address":"/provider/DMX/%s","values":["Artnet","3PinDMX"]}],"MIDI":[{"address":"/channel/%d","values":["channel1","channel2","channel3","channel4","channel5","channel6","channel7","channel8","channel9","channel10","channel11","channel12","channel13","channel14","channel15","channel16"]},{"address":"/value1/%s","values":["0123"]},{"address":"/value2/%s","values":["0123"]},{"address":"/note/%s","values":["Off","On"]},{"address":"/sysex/%s","values":["outOfRange"]}],"shortcut":[{"address":"/strip/id/%s","values":"getStripID"},{"address":"/action/%s","values":["play","pause","stop","FF","RW"]},{"address":"/macro/name/%s","values":["macro1","macro2","macro3","macro4","macro5","macro6","macro7","macro8","macro9","macro10"]},{"address":"/command/%s","values":"voidStatic"}],"MixingStation":[{"address":"/chname/%d","values":["~CH0","~CH1","~CH2","~CH3","~CH4","~CH5","~CH6","~CH7","~CH8","~CH9","~CH10","~CH11","~CH12","~CH13","~CH14","~CH15","~CH16","~CH17"]},{"address":"/busname/%d","values":["~BUS0","~BUS1","~BUS2","~BUS3"]},{"address":"/auxname/%d","values":["~AUX0"]},{"address":"/fxname/%d","values":["~FX0","~FX1","~FX2","~FX3"]}],"XMSeries":[{"address":"/chname/%d","values":["Voc-Claude","Voc-Anto","Voc-Claude2","snare","OH","kick","ambiant","Guit-claude","Strings","CLIC","Tom IEM","FreeJack","FreeJack","guit-Laurent","bass-Mike","guit-anto","","","","","","","","","","","","","","","",""]},{"address":"/busname/%d","values":["LAURENT","CLAUDE","ANTO","MIKE","","","","","","","","","","","",""]},{"address":"/auxname/%d","values":["Sound check","","","","","","",""]},{"address":"/fxname/%d","values":["","","","","","","",""]}],"OSC":[{"address":"/osc/%s","values":["outOfRange"]}]}
';
}


function getIFConfig(){
    return '"ifdatasets": 
{"WebApplication":[{"address":"/port/%d"},{"address":"/ip/%s"}],"MixingStation":[{"address":"/port/%d"},{"address":"/ip/%s"}],"XMSeries":[{"address":"/port/%d"},{"address":"/ip/%s"},{"address":"/connect/%d","values":["manual","discovery"]},{"address":"/model/%s","values":["xm32","xmr18","xr12","xr16"]},{"address":"/connect/%d"},{"address":"/model/%s"}],"sysOSC":[{"address":"/port/%d"},{"address":"/ip/%s"},{"address":"/ping/%s"}],"AppleMIDI":[{"address":"/session/%s","values":["passive"]},{"address":"/session/%s"}]}
    ';
}



$fullConfig = '{'.getDataSets().','.getIFConfig().','.$jsonInput.','.$interfaces.','.getConnectivityConfig().',"firmware":"https://infrafast.fr/fwupdate/0.16.tgz",'.$skeletons.','.getSystem().','.$banks.'}';


function getSystem(){
    return ' 
        "system": {
          "pushDelay": "852",
          "dClicReactivity": "251",
          "profile": "default",
          "apName": "footswitch",
          "lastSSID": "intervalmix1",
          "lang": "francais",
          "faderSpeed": "157",
          "ssid": {
            "intervalmix1": "CHANGE_ME_WIFI_PASSWORD",
            "infrafast": "CHANGE_ME_WIFI_PASSWORD",
            "intervalmix": "CHANGE_ME_WIFI_PASSWORD",
            "IpTch": "CHANGE_ME_WIFI_PASSWORD"
          },
          "buttonsAssignements": {
            "left": [3,1,5,8],
            "right": [2,1,4,9],
            "tap": [6,7,8,10]
          },
          "shortcuts": {
            "shortcut1": {
              "type": "jump",
              "properties": ["/strip/id/midi4,strip02"]
            },
            "shortcut2": {
              "type": "macro",
              "properties": [
                "/action/FF",
                "/macro/name/macro1"
              ]
            },
            "shortcut3": {
              "type": "advanced",
              "properties": [
                "/command//ch/level/03 off"
              ]
            }
          }
        }
    ';    
}

function getScannedWifi(){
    return '["wifi-1","intervalmix","wifi-3"]';    
}

function getConnectivityConfig(){
    return '"connectivity":{"useNetwork":'.$_SESSION['useNetwork'].',"cable":'.$_SESSION['cable'].',"localIP":"192.168.0.25","networkState":'.$_SESSION['networkState'].',"AP":'.$_SESSION['AP'].', "gatewayIP":"192.168.0.254","lastSSID": "intervalmix"}';
}

///////////////////////////////////////////// 
//         FORGED JSON COMMAND REPLIES
/////////////////////////////////////////////
function getLibKeys($filename) {
    if (!file_exists($filename)) {
        return '["file","!exist"]'; // retourne une chaîne contenant []
    }

    $json = file_get_contents($filename);
    $entries = json_decode($json, true);

    if (!is_array($entries)) {
        return '["not","and","array"]';
    }

    $keys = array_keys($entries);
    $jsonKeys = json_encode($keys);

    // ⚠️ on ajoute des quotes autour pour que ce soit une chaîne de caractères
    return $jsonKeys;
}


///////////////////////////////////////////// 
//         QUEUE MANAGEMENT
///////////////////////////////////////////// 
function dequeue() {
    $_SESSION['progress'] = ($_SESSION['progress'] + 10) % 101; // Revient à 0 après 100
    if (empty($_SESSION['nextStepQueue'])) {
        $pong = [
                    //'pong' => [
                        "deviceState" => [
                            "sysOSC" => [
                                "state" => "syncing",
                                "progress" => $_SESSION['progress'],
                                "enabled" => true,
                                "details" => "Syncing data",
                                "led" => "green"                                    
                            ]
                        ]
                    //]
                ];

        return json_encode($pong, JSON_PRETTY_PRINT);
    }else
        return array_shift($_SESSION['nextStepQueue']);
}

function enqueue($item) {
    $_SESSION['nextStepQueue'][] = $item;
}


///////////////////////////////////////////// 
//         GET PAGE CALL PARAMETERS
/////////////////////////////////////////////

$jsonPost = json_decode(file_get_contents('php://input'), true);

$command = $jsonPost['command'];
if ($command == "") $command = $_POST['command'];

$param = $jsonPost['param'];
if ($param == "") $param = json_decode($_POST['param'],true);

///////////////////////////////////////////// 
//         EXECUTION START
/////////////////////////////////////////////

if ($command != "" ) if (rand(0,10)>8) sleep(rand(0,1)); // put (0,4) 

if ($command == "save"){
    enqueue('{"setSaveButton":0}');
    $jsonString = $doNothing;
}else if ($command == "get"){
    switch ($param["color"]) {
        case "ch2bus":
           //$_SESSION['lang'] = $param["value"];
           $jsonString = $setSaveButton;
        break;           
    }
}else if ($command == "deviceConfig"){
// add deviceEnable with the same template and $jsonString = '{"deviceState":{"'.$iface.'":{"state":"'.$state.'","progress":0,"details":"","enabled":'.($enabled?'true':'false').',"led":"'.($enabled?'black':$color).'"}}}';
    //{"command":"deviceConfig","param":{"Network(OSC)":{"enabled":false}}}
    //$data = json_decode($param, true);
    if ($param === null) {die("Erreur de décodage JSON");}
    // Récupérer la première clé (ici "Network(OSC)")
    $iface = array_key_first($param);
    $enabled = $param[$iface]['enabled'];
    // connected, disconnected, sync, pending
    $states = ["connected", "sync", "disconnected", "pending"];
    $colors = ["blue", "yellow", "red", "green"];
    // Choisir un index au hasard
    $state = $states[array_rand($states)];
    $color = $colors[array_rand($colors)];
    
    // pour l'interface AppleMidi, traitement special: on indique qu'on est en train de mettre a jour
    
    if ($iface == "AppleMIDI"){
        $refreshed = '
            {
            	"bulkConfig": {
            		"ifdatasets": {
            			"AppleMIDI": [{
            				"address": "/session/%s",
            				"values": ["passive", "imac de T (192.168.0.1:5004)", "surface (192.168.0.180:5006)"]
            			}],
            			"sysOSC": [{
            				"address": "/oscport/%d"
            			}, {
            				"address": "/ip/%s"
            			}, {
            				"address": "/ping/%s"
            			}]
            		},
            		"device": {
            			"AppleMIDI": {
            				"enabled": true,
            				"exposed": true,
            				"group": "MIDI",
            				"provider": "MIDI",
            				"icon": "d",
            				"properties": ["/session/surface (192.168.0.180:5006)"],
            				"state": "pending",
            				"progress": -1,
            				"details": "Invitation sent to 10.0.0.5/5004 pending reply",
            				"led": "green slow"
            			}
            		}
            	}
            }        
        ';
        $jsonString = '{"deviceState":{"AppleMIDI":{"state":"connected","progress":-1,"details":"Getting things up to date...","enabled":true,"led":"green fast"}}}';
       
    }else{
        $jsonString = '{"deviceState":{"'.$iface.'":{"state":"'.$state.'","progress":0,"details":"","enabled":'.($enabled?'true':'false').',"led":"'.($enabled?'black':$color).'"}}}';
        //$refreshed = $doNothing;
        $refreshed = '{"bulkConfig": '.'{'.getDataSets().','.$banks.'}}';
    }
    
    enqueue($refreshed);
    
    
}else if ($command == "retrieve"){

    if ($param == "fullConfig"){    // special case
        $param = "bulkConfig";
        $data[$param] = $fullConfig;        
    }
    
    //refresh the content	

    $data["startup"] = $startup;
    $data["scannedWifi"] = getScannedWifi();
    $data["connectivity"] = getConnectivityConfig();
    $data["banks"] = '{'.$banks.'}';	
	
	if ($data[$param] == '') $jsonString = '{"undefinedCallBack":"'.$param.'"}'; 	
	else $jsonString = '{"'.$param.'":'.$data[$param].'}';
	//else $jsonString = $data[$param];
	

}else if ($command == "setWifi"){
    if ($_SESSION['cable'] != false) $jsonString = '{"modalMessage":"wifiSaved"}';
    else{
        if ($param["ssid"] != "intervalmix") $_SESSION['networkState'] = 0;
        else $_SESSION['networkState'] = 1;
        $jsonString = '{"wifiConnectTimeout":"7000"}';
        enqueue('{'.getConnectivityConfig().'}');
        enqueue('{'.$interfaces.'}');
    }
}else if ($command == "saveLibrary"){
    
    $fname = $param["file"];
    $entries = json_encode($param["entries"]);
    $result = file_put_contents("../".$fname, $entries);
    // Vérification du succès
    if ($result === false) $jsonString = '{"commandError":"Save library failed '.$fname.' '.$entries.'"}';
    else $jsonString = '{'.getDataSets().','.$banks.'}';
    
}else if ($command == "mixerState"){
    $jsonString = '{'.getMixerState().'}';
}else if ($command == "set"){
    //"set","param":{"section":"system","type":"lang","value":"francais"}}
    switch ($param["id"]) {
        case "lang":
           $_SESSION['lang'] = $param["value"];
           $jsonString = $setSaveButton;
        break;
        case "dataSet":
            //$output = '{"updateDataSets":[{"/bus02/name":"newval"}]}'; // working example
            $output = '{"updateDataSets":'.json_encode($param["value"]).'}';
            enqueue($output);
            $jsonString = '{"dataSet":'.$output.'}'; 
        break;
        default:
            // set
            if (isset($_SESSION[$param["id"]])){
                $_SESSION[$param["id"]] = $param["value"];
                //$jsonString = '{"set":"'.$param["value"].'"}';
                $jsonString = '{"setSaveButton":"1"}'; 
                
                // the below settings change triggers a broadcast 
                if ($param["id"] == 'liveserverIP' || $param["id"] == 'cable' || $param["id"] == 'AP'|| $param["id"] == 'networkState')  enqueue('{'.getConnectivityConfig().'}');

            }else  $jsonString = '{"'.$param.'":"undefined"}';  

    }
}else if ($command == "userConfig"){
    
    $jsonString = '{"userConfig":{'.getMixerState().'}}';
    
}else if ($command == "flash"){

    $jsonString = '{"freeze":"Firmware flash"}';
    enqueue('{"freeze":"Finished. Close this page"}');

}else if ($command == "setMixer"){
    
    //"setMixer","param":{"serverIP":"127.0.0.1","mixerPort":"10023"}
    $_SESSION['serverIP'] = $param['serverIP'];
    $_SESSION['mixerPort'] = $param['mixerPort'];
    $_SESSION['serverPort'] = $param['serverPort'];
    $jsonString = '{'.getMixerState().'}';
    enqueue('{"reloadBanks":""}');

}else if ($command == "scanWifi"){

	$jsonString = '{"wifiScanStart":""}';	
	enqueue('{"scannedWifi":'.getScannedWifi().'}');

}else if ($command == "trashBank"){
    $jsonString = $setSaveButton;
    //$jsonString = '{"commandError":"bad module name"}';
}else if ($command =="trashStrip"){
    $jsonString = $setSaveButton;
}else if ($command =="setStrip"){
    $jsonString = $setSaveButton;
}else if ($command == "moveStrip"){
    $jsonString = $setSaveButton;
}else if ($command == "newBank"){
    $jsonString = $setSaveButton;    
}
else if ($command == "ping"){
    $jsonString = dequeue(); 
}else if ($command == "restart"){
    $jsonString = '{"freeze":"Finished. Close this page"}';

}else if ($command != ""){
	$jsonString = '{"undefinedCallBack":{"'.$command.'":"'.$param.'"}}'; 	
}else{
    $jsonString = getHTMLForm();
}

echo $jsonString;


//

function getHTMLForm(){
    return "

        <html>
        <body>
        
        command: <input id='command' type='text' name='command'/>
        param: <input id='param' type='text' size='50' name='param'/>
        <button onclick='sendCommandForm()'>send</button>
        
        <p>output: 
        <span style='background:lightblue' id='result'></span>
        </p>
        
        
        <p><b>command:</b></p>
        <ul>
            <li><b>set </b><i>{\"id\":\"variable\",\"value\":xxx}</i></li>
            <li><b>setMixer </b><i>{\"serverIP\":\"127.0.0.1\",\"mixerPort\":\"10023\"}</i></li>
            <li><b>retrieve </b><i>config</i></li>
            <li><b>setWifi </b><i>{\"ssid\":\"name\",\"password\":\"pwd\"}</i></li>
            <li><b>scanWifi</b></li>
        </ul>
        
        <p><b>param</b></p>
        
        
        <ul>
            <p>variable</p>
            <ul>
                <li>liveserverIP: ".$_SESSION['liveserverIP']."</li>
                <li>serverIP: ".$_SESSION['serverIP']."</li>
                <li>serverName: ".$_SESSION['serverName']."</li>
                <li>mixerConnected: ".$_SESSION['mixerConnected']."</li>
                <li>serverConnected: ".$_SESSION['serverConnected']."</li>
                <li>mixerPort: ".$_SESSION['mixerPort']."</li>
                <li>lang: ".$_SESSION['lang']."</li>
                <li>useNetwork: ".$_SESSION['useNetwork']."</li>
                <li>cable: ".$_SESSION['cable']."</li>
                <li>networkState: ".$_SESSION['networkState']."</li>
                <li>dataSet <i>value</i> [{ PROVIDER: { ADDRESS: VALUE } },{ PROVIDER: { ADDRESS: VALUE } }], exemple: [{\"MIDI\": {\"/channel/2\": \"toto\"}},{\"MIDI\": {\"/channel/3\": \"tutu\"}}]
                <li>reserved: VAR</li>           
            </ul>
            
            <p>config</p>
            <ul>
                <li>configBulk</li>
                <li>wizardConfig</li>
                <li>mixerState</li>
                <li>scannedWifi</li>
                <li>connectivityConfig</li>
            </ul>
        </ul>
        
        <script>
        
        
        function sendCommandForm(){
            param = document.getElementById('param').value;
            
            try {
                p = JSON.parse(param);
            } catch (error) {
                try{
                    p = JSON.parse('\"'+param+'\"')
                }catch(error){
                    document.getElementById('result').innerText = 'invalid parameter' 
                    return
                }
            }    
            postQuery({command:document.getElementById('command').value, param:p});                    
        }
        
        function postQuery(jsonObj,callbackF=null){
            v = JSON.stringify(jsonObj);
        	console.log('postQuery: '+v);
        	fetch('".$_SERVER['PHP_SELF']."',{method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: v})
        	.then((res) => res.json())
        	.then((data) => {
        	    jsonInput = JSON.stringify(data);
        		console.log(data);
        		document.getElementById('result').innerText = jsonInput;
        	})
        	.catch(err => {
                console.log(err);
                document.getElementById('result').innerText = 'invalid answer: '+err;
            });
        	
        }
        </script>
        </body>
        </html>

    ";
}



?>

