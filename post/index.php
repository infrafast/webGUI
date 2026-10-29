<?php

/////////////////////////////////////////////
// Session state
//
// The real ESP32 persists these values in its configuration. The mock keeps
// them in the PHP session so each browser can drive its own simulated device.
/////////////////////////////////////////////
session_start();

$sessionDefaults = [
    'mixerConnected' => 'disconnected',
    'serverConnected' => 'true',
    'serverName' => 'auto',
    'serverIP' => '0.0.0.0',
    'mixerPort' => '0',
    'serverPort' => '10023',
    'lang' => 'francais',
    'liveserverIP' => '127.0.0.1',
    'useNetwork' => 1,
    'cable' => 1,
    'networkState' => 1,
    'AP' => 0,
    'pushDelay' => 550,
    'nextStepQueue' => [],
    'progress' => 0,
    'discovery' => 'manual',
];
foreach ($sessionDefaults as $name => $default) {
    if (!array_key_exists($name, $_SESSION)) {
        $_SESSION[$name] = $default;
    }
}

// Only catalogue files are advertised. The *-user.json files are optional
// overlays loaded by the web client and are intentionally not listed here.
$supportedLanguages = ['deutsch', 'english', 'espanol', 'francais', 'italiano', 'portugues'];
$availableLanguages = array_values(array_filter(
    $supportedLanguages,
    static function ($language) {
        return is_file(__DIR__ . '/../' . $language . '.json');
    }
));
$_SESSION['lang'] = strtolower(trim((string)$_SESSION['lang']));
if (!in_array($_SESSION['lang'], $availableLanguages, true)) {
    $_SESSION['lang'] = in_array('francais', $availableLanguages, true)
        ? 'francais'
        : ($availableLanguages[0] ?? 'english');
}

/////////////////////////////////////////////
// Small protocol helpers
/////////////////////////////////////////////
function jsonEncode($value): string
{
    $encoded = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($encoded === false) {
        throw new RuntimeException('Unable to encode mock response: ' . json_last_error_msg());
    }
    return $encoded;
}

function response(string $key, $value): string
{
    return jsonEncode([$key => $value]);
}

function requestPayload(): array
{
    $raw = file_get_contents('php://input');
    $decoded = is_string($raw) && trim($raw) !== '' ? json_decode($raw, true) : null;
    if (is_array($decoded)) {
        return $decoded;
    }
    return is_array($_POST) ? $_POST : [];
}

function parameter(array $request, string $name, $default = null)
{
    if (!array_key_exists($name, $request)) {
        return $default;
    }
    $value = $request[$name];
    if (is_string($value)) {
        $decoded = json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
    }
    return $value;
}

///////////////////////////////////////////// 
//         GLOBAL JSON VARIABLES
///////////////////////////////////////////// 


$doNothing = '{}';
$setSaveButton = '{"setSaveButton":"1"}';


require __DIR__ . '/fixtures.php';

$startup = jsonEncode([
    'langs' => $availableLanguages,
    'maxPOSTsize' => 4096,
    'safeScanfSpace' => '^',
    'sysexLibFile' => 'sysexlib.json',
    'oscLibFile' => 'osclib.json',
    'wizard' => false,
    'lang' => $_SESSION['lang'],
    'configNames' => ['default'],
    'startupUpdate' => true
]);

$fullConfig = '{'.getDataSets().','.getIFConfig().','.$jsonInput.','.$interfaces.','.getConnectivityConfig().',"firmware":"https://infrafast.fr/fwupdate/0.16.tgz",'.$skeletons.','.getSystem().','.$banks.'}';


function getSystem(){
    return ' 
        "system": {
          "pushDelay": "852",
          "dClicReactivity": "251",
          "profile": "default",
          "apName": "footswitch",
          "lastSSID": "intervalmix1",
          "lang": "'.$_SESSION['lang'].'",
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
    return '"connectivity":{"useNetwork":'.flag($_SESSION['useNetwork']).',"cable":'.flag($_SESSION['cable']).',"localIP":"192.168.0.25","networkState":'.flag($_SESSION['networkState']).',"AP":'.flag($_SESSION['AP']).', "gatewayIP":"192.168.0.254","lastSSID": "intervalmix"}';
}

function flag($value): int
{
    return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
}

// The frontend only needs the mixer connection fields for this mock.
function getMixerState(): string
{
    return '"serverIP":'.jsonEncode((string)$_SESSION['serverIP']).
        ',"mixerPort":'.jsonEncode((string)$_SESSION['mixerPort']).
        ',"serverPort":'.jsonEncode((string)$_SESSION['serverPort']).
        ',"serverName":'.jsonEncode((string)$_SESSION['serverName']);
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
function dequeue(): string
{
    $_SESSION['progress'] = ((int)$_SESSION['progress'] + 10) % 101;
    if (!empty($_SESSION['nextStepQueue'])) {
        return (string)array_shift($_SESSION['nextStepQueue']);
    }

    return jsonEncode([
        'deviceState' => [
            'sysOSC' => [
                'state' => 'syncing',
                'progress' => $_SESSION['progress'],
                'enabled' => true,
                'details' => 'Syncing data',
                'led' => 'green',
            ],
        ],
    ]);
}

function enqueue(string $item): void
{
    // Keep a bounded queue so a forgotten browser poll cannot exhaust the session.
    $_SESSION['nextStepQueue'][] = $item;
    if (count($_SESSION['nextStepQueue']) > 32) {
        array_shift($_SESSION['nextStepQueue']);
    }
}


///////////////////////////////////////////// 
//         GET PAGE CALL PARAMETERS
/////////////////////////////////////////////

$request = requestPayload();
$command = trim((string)($request['command'] ?? ''));
$param = parameter($request, 'param', null);
$data = [];
$jsonString = $doNothing;

///////////////////////////////////////////// 
//         EXECUTION START
/////////////////////////////////////////////

if ($command != "" ) if (rand(0,10)>8) sleep(rand(0,1)); // put (0,4) 

if ($command == "save"){
    enqueue('{"setSaveButton":0}');
    $jsonString = $doNothing;
}else if ($command == "get"){
    $param = is_array($param) ? $param : [];
    switch ($param['color'] ?? '') {
        case "ch2bus":
           //$_SESSION['lang'] = $param["value"];
           $jsonString = $setSaveButton;
        break;           
    }
}else if ($command == "deviceConfig"){
// add deviceEnable with the same template and $jsonString = '{"deviceState":{"'.$iface.'":{"state":"'.$state.'","progress":0,"details":"","enabled":'.($enabled?'true':'false').',"led":"'.($enabled?'black':$color).'"}}}';
    //{"command":"deviceConfig","param":{"Network(OSC)":{"enabled":false}}}
    //$data = json_decode($param, true);
    if (!is_array($param) || $param === []) {
        $jsonString = response('commandError', 'deviceConfig expects an interface object');
    } else {
    // Récupérer la première clé (ici "Network(OSC)")
    $iface = (string)array_key_first($param);
    $enabled = !empty($param[$iface]['enabled']);
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
    }
    
}else if ($command == "retrieve"){

    if ($param === "fullConfig") {
        $param = "bulkConfig";
        $data[$param] = $fullConfig;
    }

    $data["startup"] = $startup;
    $data["scannedWifi"] = getScannedWifi();
    $data["connectivity"] = getConnectivityConfig();
    $data["banks"] = '{'.$banks.'}';

    if (!is_string($param) || !array_key_exists($param, $data)) {
        $jsonString = response('undefinedCallBack', (string)$param);
    } else {
        // These values are already JSON fragments, matching the ESP32 protocol.
        $jsonString = '{"'.addslashes($param).'":'.$data[$param].'}';
    }
	

}else if ($command == "setWifi"){
    $param = is_array($param) ? $param : [];
    if (flag($_SESSION['cable'])) $jsonString = '{"modalMessage":"wifiSaved"}';
    else{
        if (($param["ssid"] ?? '') != "intervalmix") $_SESSION['networkState'] = 0;
        else $_SESSION['networkState'] = 1;
        $jsonString = '{"wifiConnectTimeout":"7000"}';
        enqueue('{'.getConnectivityConfig().'}');
        enqueue('{'.$interfaces.'}');
    }
}else if ($command == "saveLibrary"){
    
    $param = is_array($param) ? $param : [];
    $fname = basename((string)($param['file'] ?? ''));
    $entries = jsonEncode($param['entries'] ?? []);
    $validName = (bool)preg_match('/^[A-Za-z0-9_-]+\.json$/', $fname);
    $result = $validName ? file_put_contents(__DIR__ . '/../' . $fname, $entries, LOCK_EX) : false;
    if ($result === false) {
        $jsonString = response('commandError', 'Save library failed: ' . $fname);
    } else {
        $jsonString = '{'.getDataSets().','.$banks.'}';
    }
    
}else if ($command == "mixerState"){
    $jsonString = '{'.getMixerState().'}';
 }else if ($command == "set"){
    // The web client uses this command for both language and simulator controls.
    $param = is_array($param) ? $param : [];
    $id = (string)($param['id'] ?? '');
    $value = $param['value'] ?? null;

    if ($id === 'lang') {
        $requestedLanguage = strtolower(trim((string)$value));
        if (in_array($requestedLanguage, $availableLanguages, true)) {
            $_SESSION['lang'] = $requestedLanguage;
        }
        $jsonString = $setSaveButton;
    } elseif ($id === 'dataSet') {
        $output = response('updateDataSets', $value);
        enqueue($output);
        $jsonString = response('dataSet', json_decode($output, true));
    } elseif (array_key_exists($id, $_SESSION)) {
        $flagSettings = ['useNetwork', 'cable', 'networkState', 'AP'];
        $_SESSION[$id] = in_array($id, $flagSettings, true) ? flag($value) : $value;
        $jsonString = $setSaveButton;

        // These settings change the state broadcast by the real device.
        if (in_array($id, ['liveserverIP', 'cable', 'AP', 'networkState', 'useNetwork'], true)) {
            enqueue('{'.getConnectivityConfig().'}');
        }
    } else {
        $jsonString = response('commandError', 'Unknown simulator setting: ' . $id);
    }

}else if ($command == "userConfig"){
    
    $jsonString = '{"userConfig":{'.getMixerState().'}}';
    
}else if ($command == "flash"){

    $jsonString = '{"freeze":"Firmware flash"}';
    enqueue('{"freeze":"Finished. Close this page"}');

}else if ($command == "setMixer"){
    // setMixer, {"serverIP":"127.0.0.1","mixerPort":"10023"}
    $param = is_array($param) ? $param : [];
    if (!isset($param['serverIP'], $param['mixerPort'], $param['serverPort'])) {
        $jsonString = response('commandError', 'setMixer requires serverIP, mixerPort and serverPort');
    } else {
        $_SESSION['serverIP'] = (string)$param['serverIP'];
        $_SESSION['mixerPort'] = (string)$param['mixerPort'];
        $_SESSION['serverPort'] = (string)$param['serverPort'];
        $jsonString = '{'.getMixerState().'}';
        enqueue('{"reloadBanks":""}');
    }

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
}else if ($command == "reset"){
    // Reset only this browser session to the same defaults used at startup.
    foreach ($sessionDefaults as $name => $default) {
        $_SESSION[$name] = $default;
    }
    $_SESSION['lang'] = in_array('francais', $availableLanguages, true)
        ? 'francais' : ($availableLanguages[0] ?? 'english');
    $jsonString = response('reset', true);

}else if ($command == "restart"){
    $jsonString = '{"freeze":"Finished. Close this page"}';

}else if ($command != ""){
    $jsonString = response('undefinedCallBack', [$command => $param]);
}else{
    $jsonString = getHTMLForm();
}

if ($command !== '') {
    header('Content-Type: application/json; charset=utf-8');
}
echo $jsonString;

//

function getHTMLForm(): string
{
    $endpoint = htmlspecialchars($_SERVER['PHP_SELF'] ?? 'index.php', ENT_QUOTES, 'UTF-8');
    $state = [
        'useNetwork' => flag($_SESSION['useNetwork']),
        'cable' => flag($_SESSION['cable']),
        'networkState' => flag($_SESSION['networkState']),
        'AP' => flag($_SESSION['AP']),
    ];

    // This page is deliberately small: it is a diagnostic panel for the mock,
    // not a second copy of the production web application.
    ob_start();
    ?>
    <!doctype html>
    <html lang="en">
    <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>ESP32 mock controls</title>
      <style>
        body { font: 15px system-ui, sans-serif; max-width: 900px; margin: 2rem auto; padding: 0 1rem; }
        fieldset { margin: 1rem 0; padding: 1rem; }
        label { display: inline-flex; gap: .45rem; margin: .35rem 1rem .35rem 0; }
        input, select, button { font: inherit; padding: .35rem .5rem; }
        #result { display: block; white-space: pre-wrap; overflow-wrap: anywhere; background: #eef6ff; padding: .75rem; min-height: 2rem; }
        .error { color: #a00; }
      </style>
    </head>
    <body>
      <h1>ESP32 simulator</h1>
      <p>These controls change only this browser session.</p>

      <fieldset>
        <legend>Network state</legend>
        <?php foreach ($state as $name => $enabled): ?>
          <label>
            <input type="checkbox" data-setting="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" <?= $enabled ? 'checked' : '' ?>>
            <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>
          </label>
        <?php endforeach; ?>
        <button type="button" id="reset">Reset session</button>
      </fieldset>

      <fieldset>
        <legend>Raw command</legend>
        <label>Command <input id="command" autocomplete="off"></label>
        <label>Parameter <input id="param" size="50" autocomplete="off" placeholder='{"id":"cable","value":false}'></label>
        <button type="button" id="send">Send</button>
      </fieldset>

      <p><strong>Response</strong></p>
      <code id="result" aria-live="polite"></code>

      <script>
        const endpoint = <?= jsonEncode($endpoint) ?>;
        const result = document.getElementById('result');

        async function postCommand(command, param = null) {
          result.classList.remove('error');
          try {
            const response = await fetch(endpoint, {
              method: 'POST',
              headers: {'Content-Type': 'application/json'},
              body: JSON.stringify({command, param})
            });
            const text = await response.text();
            let payload;
            try { payload = JSON.parse(text); } catch { throw new Error('Invalid JSON response: ' + text); }
            if (!response.ok) throw new Error('HTTP ' + response.status);
            result.textContent = JSON.stringify(payload, null, 2);
            return payload;
          } catch (error) {
            result.classList.add('error');
            result.textContent = error.message;
            return null;
          }
        }

        document.querySelectorAll('[data-setting]').forEach((control) => {
          control.addEventListener('change', () => postCommand('set', {
            id: control.dataset.setting,
            value: control.checked ? 1 : 0
          }));
        });
        document.getElementById('send').addEventListener('click', () => {
          const command = document.getElementById('command').value.trim();
          const raw = document.getElementById('param').value.trim();
          if (!command) { result.textContent = 'Command is required'; return; }
          let param = raw;
          if (raw) { try { param = JSON.parse(raw); } catch { /* send plain strings too */ } }
          postCommand(command, param);
        });
        document.getElementById('reset').addEventListener('click', () => {
          if (!confirm('Reset this simulator session?')) return;
          postCommand('reset');
          window.location.reload();
        });
      </script>
    </body>
    </html>
    <?php
    return (string)ob_get_clean();
}

?>
