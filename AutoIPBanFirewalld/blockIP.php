<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$logFilePath = '/var/log/nginx/access.log';

// Read the log file
$logContents = file_get_contents($logFilePath);

// Split log entries into an array
$logEntries = explode("\n", $logContents);

// Initialize variables
$ipCount = [];

echo "Starting...\n";

// Loop through log entries
foreach ($logEntries as $logEntry) {
    // Extract IP address from the log entry (you may need to adjust the pattern based on your log format)
    if (preg_match('/(\d+\.\d+\.\d+\.\d+)/', $logEntry, $matches)) {
        $ip = $matches[1];

        // Check if the IP has made more than 100 sequential requests
        if (!isset($ipCount[$ip])) {
            $ipCount[$ip] = 1;
        } else {
            $ipCount[$ip]++;
        }
    }
}

// Define a whitelist of IPs
$whitelistedIPs = ["118.159.189.166", "127.0.0.1"];

foreach ($ipCount as $ip => $count) {
    echo "IP: $ip, Requests: $count\n";

    // Check if the IP has made more than 100 total requests and is not whitelisted
    if ($count > 500 && !in_array($ip, $whitelistedIPs)) {
        // Block the IP address using firewall-cmd
        $blockCommand = 'sudo firewall-cmd --zone=public --add-rich-rule=\'rule family="ipv4" source address="' . $ip . '" drop\' --permanent';

        echo "Executing command: $blockCommand\n";

        // Execute the command and capture the return value
        $returnValue = 0;
        exec($blockCommand, $output, $returnValue);

        if ($returnValue !== 0) {
            echo "Error executing firewall-cmd command. Return value: $returnValue\n";
        } else {
            echo "Command output: " . implode("\n", $output) . "\n";
            echo "Blocked IP: $ip\n";
        }
    }
}

// Reload the firewall to apply the changes
$reloadCommand = 'sudo firewall-cmd --reload';

echo "Executing command: $reloadCommand\n";

// Execute the reload command and capture the return value
$reloadReturnValue = 0;
exec($reloadCommand, $reloadOutput, $reloadReturnValue);

if ($reloadReturnValue !== 0) {
    echo "Error executing firewall-cmd reload command. Return value: $reloadReturnValue\n";
} else {
    echo "Command output: " . implode("\n", $reloadOutput) . "\n";
    echo "Firewall reloaded successfully\n";
}

// Display all blocked IPs for further verification
$allBlocked = 'sudo firewall-cmd --list-all';
$allBlockedOutput = shell_exec($allBlocked . ' 2>&1');
echo "Blocked IPs: $allBlockedOutput\n";
?>




