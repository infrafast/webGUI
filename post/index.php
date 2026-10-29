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
    'dClicReactivity' => 251,
    'faderSpeed' => 157,
    'profile' => 'default',
    'apName' => 'footswitch',
    'hostname' => 'footswitch',
    'lastSSID' => 'intervalmix1',
    'startupUpdate' => 1,
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

// The control panel is driven by this catalogue instead of duplicating the
// firmware command list in JavaScript. Keep it aligned with parseJsonInput().
$commandCatalog = ['version' => 1, 'commands' => []];
$catalogPath = __DIR__ . '/commands.json';
if (is_file($catalogPath)) {
    $catalog = json_decode((string)file_get_contents($catalogPath), true);
    if (is_array($catalog) && isset($catalog['commands']) && is_array($catalog['commands'])) {
        $commandCatalog = $catalog;
    }
}
$commandCatalog['languages'] = $availableLanguages;

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


function getSystem(): string
{
    // Keep this shape identical to the firmware's system object while making
    // values changed from the control panel visible in the next fullConfig.
    return '"system":' . jsonEncode([
        'pushDelay' => (string)$_SESSION['pushDelay'],
        'dClicReactivity' => (string)$_SESSION['dClicReactivity'],
        'profile' => (string)$_SESSION['profile'],
        'apName' => (string)$_SESSION['apName'],
        'lastSSID' => (string)$_SESSION['lastSSID'],
        'lang' => (string)$_SESSION['lang'],
        'faderSpeed' => (string)$_SESSION['faderSpeed'],
        'ssid' => [
            'intervalmix1' => 'CHANGE_ME_WIFI_PASSWORD',
            'infrafast' => 'CHANGE_ME_WIFI_PASSWORD',
            'intervalmix' => 'CHANGE_ME_WIFI_PASSWORD',
            'IpTch' => 'CHANGE_ME_WIFI_PASSWORD',
        ],
        'buttonsAssignements' => [
            'left' => [3, 1, 5, 8],
            'right' => [2, 1, 4, 9],
            'tap' => [6, 7, 8, 10],
        ],
        'shortcuts' => [
            'shortcut1' => ['type' => 'jump', 'properties' => ['/strip/id/midi4,strip02']],
            'shortcut2' => ['type' => 'macro', 'properties' => ['/action/FF', '/macro/name/macro1']],
            'shortcut3' => ['type' => 'advanced', 'properties' => ['/command//ch/level/03 off']],
        ],
    ]);
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
// QUEUE MANAGEMENT
//
// Normal webApp requests use the PHP session queue, like the ESP32's own
// connection. Requests from the simulator control panel are additionally
// copied to a short-lived shared queue so a separate webApp tab can observe
// them through its regular ping polling. No webApp or firmware change is
// required for this bridge.
/////////////////////////////////////////////
function sharedQueuePath(): string
{
    return sys_get_temp_dir() . '/interval-esp32-mock-' . sha1(__DIR__) . '.json';
}

function sharedQueueUpdate(callable $update)
{
    $handle = fopen(sharedQueuePath(), 'c+');
    if ($handle === false) {
        return null;
    }
    try {
        if (!flock($handle, LOCK_EX)) {
            return null;
        }
        rewind($handle);
        $contents = stream_get_contents($handle);
        $queue = json_decode($contents ?: '[]', true);
        if (!is_array($queue)) {
            $queue = [];
        }
        $result = $update($queue);
        rewind($handle);
        ftruncate($handle, 0);
        fwrite($handle, jsonEncode($queue));
        fflush($handle);
        flock($handle, LOCK_UN);
        return $result;
    } finally {
        fclose($handle);
    }
}

function enqueueShared(string $item): void
{
    sharedQueueUpdate(static function (array &$queue) use ($item): void {
        $queue[] = $item;
        if (count($queue) > 64) {
            $queue = array_slice($queue, -64);
        }
    });
}

function dequeueShared(): ?string
{
    return sharedQueueUpdate(static function (array &$queue): ?string {
        return empty($queue) ? null : (string)array_shift($queue);
    });
}

function dequeue(): string
{
    $_SESSION['progress'] = ((int)$_SESSION['progress'] + 10) % 101;
    $shared = dequeueShared();
    if ($shared !== null) {
        return $shared;
    }
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
$isControlRequest = isset($_SERVER['HTTP_X_ESP32_SIMULATOR_CONTROL'])
    && $_SERVER['HTTP_X_ESP32_SIMULATOR_CONTROL'] === '1';
$command = trim((string)($request['command'] ?? ''));
$param = parameter($request, 'param', null);
$queueStart = count($_SESSION['nextStepQueue']);
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
    $interfaceValues = $param[$iface] ?? [];
    $enabled = is_array($interfaceValues) && !empty($interfaceValues['enabled']);
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
	

}else if ($command == "stopWifi"){
    $_SESSION['networkState'] = 0;
    $jsonString = $setSaveButton;
    enqueue('{'.getConnectivityConfig().'}');

}else if ($command == "deviceEnable"){
    $param = is_array($param) ? $param : [];
    $iface = (string)array_key_first($param);
    if ($iface === '' || !array_key_exists($iface, $param)) {
        $jsonString = response('commandError', 'deviceEnable expects an interface');
    } else {
        $jsonString = $setSaveButton;
        enqueue('{'.$interfaces.'}');
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
    
}else if ($command == "renameBank"){
    // Bank persistence is represented by the save acknowledgement in the mock.
    $jsonString = $setSaveButton;

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

// A control-panel request has already received its direct response. Copy that
// response for the real webApp, which will consume it on its next ping.
if ($isControlRequest && $command !== '' && $command !== 'ping' && $command !== 'reset') {
    // Preserve the real ordering: immediate reply first, deferred events next.
    enqueueShared($jsonString);
    $deferred = array_slice($_SESSION['nextStepQueue'], $queueStart);
    foreach ($deferred as $item) {
        enqueueShared((string)$item);
    }
    $_SESSION['nextStepQueue'] = array_slice($_SESSION['nextStepQueue'], 0, $queueStart);
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
        #communicationLog { white-space: pre-wrap; overflow-wrap: anywhere; background: #101820; color: #d8f3dc; padding: .75rem; min-height: 10rem; max-height: 32rem; overflow: auto; }
        .log-out { color: #9ad1d4; }
        .log-in { color: #f6bd60; }
        .log-error { color: #f28482; }
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
        <legend>ESP32 command</legend>
        <label>Command
          <select id="command">
            <?php foreach (($commandCatalog['commands'] ?? []) as $entry): ?>
              <option value="<?= htmlspecialchars((string)($entry['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars((string)($entry['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
              </option>
            <?php endforeach; ?>
          </select>
        </label>
        <p id="commandDescription"></p>
        <div id="paramFields"></div>
        <details>
          <summary>Raw JSON parameter (advanced)</summary>
          <textarea id="rawParam" rows="6" cols="70" placeholder='{"id":"cable","value":false}'></textarea>
          <p>When filled, this value is sent as-is and overrides the generated fields.</p>
        </details>
        <button type="button" id="send">Send command</button>
      </fieldset>

      <fieldset>
        <legend>Communication log</legend>
        <label><input type="checkbox" id="prettyJson" checked> Pretty JSON</label>
        <button type="button" id="clearLog">Clear log</button>
        <pre id="communicationLog" aria-live="polite" aria-label="Communication log"></pre>
      </fieldset>

      <script>
        const endpoint = <?= jsonEncode($endpoint) ?>;
        let catalog = <?= jsonEncode($commandCatalog) ?> || {commands: []};
        const communicationLog = document.getElementById('communicationLog');
        const prettyJson = document.getElementById('prettyJson');
        const commandSelect = document.getElementById('command');
        const paramFields = document.getElementById('paramFields');
        const description = document.getElementById('commandDescription');
        const logEntries = [];

        function displayData(data, pretty) {
          if (typeof data !== 'string') return JSON.stringify(data, null, pretty ? 2 : 0);
          try {
            const parsed = JSON.parse(data);
            return pretty ? JSON.stringify(parsed, null, 2) : JSON.stringify(parsed);
          } catch {
            return data;
          }
        }

        function renderLog() {
          communicationLog.replaceChildren();
          logEntries.forEach((entry) => {
            const line = document.createElement('span');
            line.className = entry.direction === 'OUT' ? 'log-out' : (entry.direction === 'IN' ? 'log-in' : 'log-error');
            const header = `[${entry.timestamp}] ${entry.direction}` + (entry.status ? ` HTTP ${entry.status}` : '');
            line.textContent = `${header}\n${displayData(entry.data, prettyJson.checked)}\n\n`;
            communicationLog.appendChild(line);
          });
          communicationLog.scrollTop = communicationLog.scrollHeight;
        }

        function logData(direction, data, status = '') {
          logEntries.push({timestamp: new Date().toISOString(), direction, data, status});
          // Keep the diagnostic page useful during long polling sessions.
          if (logEntries.length > 200) logEntries.shift();
          renderLog();
        }

        async function postCommand(command, param = null) {
          const request = {command, param};
          logData('OUT', request);
          try {
            const response = await fetch(endpoint, {
              method: 'POST',
              headers: {'Content-Type': 'application/json', 'X-ESP32-Simulator-Control': '1'},
              body: JSON.stringify(request)
            });
            const text = await response.text();
            logData('IN', text, response.status);
            if (!response.ok) throw new Error('HTTP ' + response.status);
            try { return JSON.parse(text); }
            catch { throw new Error('Invalid JSON response'); }
          } catch (error) {
            logData('ERROR', error.message);
            return null;
          }
        }

        document.querySelectorAll('[data-setting]').forEach((control) => {
          control.addEventListener('change', () => postCommand('set', {
            id: control.dataset.setting,
            value: control.checked ? 1 : 0
          }));
        });
        function addField(name, type = 'text', value = '') {
          const label = document.createElement('label');
          label.textContent = name;
          let input;
          if (type === 'textarea') {
            input = document.createElement('textarea');
            input.rows = 5;
            input.cols = 60;
          } else if (type === 'select') {
            input = document.createElement('select');
          } else {
            input = document.createElement('input');
            input.type = type === 'boolean' ? 'checkbox' : type;
            if (type === 'boolean') input.checked = Boolean(value);
          }
          input.dataset.parameter = name;
          if (type !== 'boolean' && type !== 'select') input.value = value;
          label.appendChild(input);
          paramFields.appendChild(label);
          return input;
        }

        function selectedCommand() {
          return catalog && Array.isArray(catalog.commands) ? catalog.commands.find((entry) => entry.name === commandSelect.value) : null;
        }

        function renderParameters() {
          const entry = selectedCommand();
          paramFields.replaceChildren();
          description.textContent = entry ? entry.description : '';
          if (!entry || entry.mode === 'none') return;
          if (entry.mode === 'select') {
            const select = addField('param', 'select');
            entry.values.forEach((value) => select.add(new Option(value, value)));
            return;
          }
          if (entry.mode === 'set') {
            const id = addField('id', 'select');
            entry.ids.forEach((value) => id.add(new Option(value, value)));
            addField('value', 'text');
            return;
          }
          if (entry.mode === 'interfaceToggle') {
            const iface = addField('interface', 'select');
            (catalog.interfaces || []).forEach((value) => iface.add(new Option(value, value)));
            addField('enabled', 'boolean', true);
            return;
          }
          if (entry.mode === 'interfaceJson') {
            const iface = addField('interface', 'select');
            (catalog.interfaces || []).forEach((value) => iface.add(new Option(value, value)));
            addField('values', 'textarea', '{}');
            return;
          }
          if (entry.mode === 'wifi') {
            addField('ssid', 'text', 'intervalmix');
            addField('password', 'password');
            return;
          }
          if (entry.mode === 'mixer') {
            addField('serverIP', 'text', '127.0.0.1');
            addField('mixerPort', 'number', '10023');
            addField('serverPort', 'number', '10023');
            return;
          }
          if (entry.mode === 'library') {
            const file = addField('file', 'select');
            ['sysexlib.json','osclib.json'].forEach((value) => file.add(new Option(value, value)));
            addField('entries', 'textarea', '{}');
            return;
          }
          if (entry.mode === 'json') {
            addField('json', 'textarea', JSON.stringify(entry.example || {}, null, 2));
          }
        }

        function generatedParameter() {
          const entry = selectedCommand();
          if (!entry || entry.mode === 'none') return null;
          const values = Object.fromEntries([...paramFields.querySelectorAll('[data-parameter]')].map((input) => [input.dataset.parameter, input.type === 'checkbox' ? input.checked : input.value]));
          if (entry.mode === 'select') return values.param;
          if (entry.mode === 'set') return {id: values.id, value: ['useNetwork','cable','networkState','AP'].includes(values.id) ? ['true','1','on'].includes(String(values.value).toLowerCase()) : values.value};
          if (entry.mode === 'interfaceToggle') return {[values.interface]: values.enabled};
          if (entry.mode === 'interfaceJson') return {[values.interface]: JSON.parse(values.values || '{}')};
          if (entry.mode === 'json') return JSON.parse(values.json || '{}');
          if (entry.mode === 'wifi') return {ssid: values.ssid, password: values.password};
          if (entry.mode === 'mixer') return {serverIP: values.serverIP, mixerPort: values.mixerPort, serverPort: values.serverPort};
          if (entry.mode === 'library') return {file: values.file, entries: JSON.parse(values.entries || '{}')};
          return null;
        }

        async function initializeCommandCatalogue() {
          // The PHP page embeds the catalogue, while the relative fetch makes
          // the panel resilient to a stale PHP cache or a moved checkout.
          if (!catalog || !Array.isArray(catalog.commands) || catalog.commands.length === 0) {
            try {
              const response = await fetch('commands.json', {cache: 'no-store'});
              if (response.ok) {
                const loaded = await response.json();
                // Never replace a usable catalogue with null or malformed data.
                if (loaded && Array.isArray(loaded.commands) && loaded.commands.length > 0) {
                  catalog = loaded;
                }
              }
            } catch (error) {
              logData('ERROR', 'Unable to load commands.json: ' + error.message);
            }
          }
          if (!catalog || !Array.isArray(catalog.commands) || catalog.commands.length === 0) {
            description.textContent = 'Command catalogue unavailable';
            logData('ERROR', 'No command catalogue entry was loaded');
            return;
          }
          commandSelect.replaceChildren();
          catalog.commands.forEach((entry) => commandSelect.add(new Option(entry.name, entry.name)));
          renderParameters();
        }

        commandSelect.addEventListener('change', renderParameters);
        initializeCommandCatalogue();
        prettyJson.addEventListener('change', renderLog);
        document.getElementById('clearLog').addEventListener('click', () => {
          logEntries.length = 0;
          renderLog();
        });

        document.getElementById('send').addEventListener('click', () => {
          const entry = selectedCommand();
          const raw = document.getElementById('rawParam').value.trim();
          if (!entry) { logData('ERROR', 'No command catalogue entry'); return; }
          try {
            postCommand(entry.name, raw ? JSON.parse(raw) : generatedParameter());
          } catch (error) {
            logData('ERROR', 'Invalid parameter JSON: ' + error.message);
          }
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
