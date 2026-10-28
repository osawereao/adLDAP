<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

// Autoloader for Adldap
spl_autoload_register(function ($class) {
    $prefix = 'Adldap\\';
    $base_dir = __DIR__ . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

$results = [];
$hasErrors = false;

function runCheck($name, callable $fn) {
    global $results, $hasErrors;
    try {
        $fn();
        $results[] = "[PASS] $name";
    } catch (\Throwable $e) {
        $hasErrors = true;
        $results[] = "[FAIL] $name: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine();
    }
}

// Check 1: Instantiation without auto-connect
runCheck("Instantiate Adldap main class", function () {
    $ad = new \Adldap\Adldap([], null, false);
    if (!($ad instanceof \Adldap\Adldap)) {
        throw new \Exception("Adldap instance not created");
    }
});

// Check 2: Dynamic property creation on AbstractObject (PHP 8.2)
runCheck("Dynamic properties on AbstractObject (PHP 8.2 #[\AllowDynamicProperties])", function () {
    $cfg = new \Adldap\Objects\Configuration();
    $cfg->custom_prop = "value";
    if ($cfg->custom_prop !== "value") {
        throw new \Exception("Property value mismatch");
    }
});

// Check 3: Curly bracket string offset replacement in AdldapUsers (PHP 8.0+)
runCheck("AdldapUsers::encodePassword string offset handling", function () {
    $ad = new \Adldap\Adldap([], null, false);
    $users = new \Adldap\Classes\AdldapUsers($ad);
    $encoded = $users->encodePassword("mySecurePassword123!");
    if (empty($encoded)) {
        throw new \Exception("Encoded password is empty");
    }
});

// Check 4: UTF-8 encoding in AdldapUtils without deprecated utf8_encode (PHP 8.2)
runCheck("AdldapUtils::encode8Bit with non-ASCII characters", function () {
    $ad = new \Adldap\Adldap([], null, false);
    $utils = new \Adldap\Classes\AdldapUtils($ad);
    $text = "Hélène";
    $utils->encode8Bit($text, "description");
    if (empty($text)) {
        throw new \Exception("Text unexpectedly empty");
    }
});

// Check 5: Ldap connection class & escaping logic
runCheck("Ldap connection object methods & string escaping", function () {
    $conn = new \Adldap\Connections\Ldap();
    $escaped = $conn->escape("user,name=test*");
    if (empty($escaped)) {
        throw new \Exception("Escaped output is empty");
    }
    $conn->close();
});

// Check 6: Query builder & Operator
runCheck("Query Builder instantiation and where compilation", function () {
    $ad = new \Adldap\Adldap([], null, false);
    $search = $ad->search();
    $search->where('cn', '=', 'test');
    $query = $search->getQuery();
    
    // Adldap uses $connection->escape('test'). When escape flag is 0 or escapeManual is used,
    // each character is hex-escaped (\74\65\73\74), or with ldap_escape flags.
    // Verify that the query begins with (cn= and closes with )
    if (!preg_match('/^\(cn=.+\)$/', $query)) {
        throw new \Exception("Query compiled incorrectly: " . $query);
    }
});

echo "PHP Version: " . PHP_VERSION . "\n";
echo "LDAP Extension: " . (extension_loaded('ldap') ? 'LOADED' : 'NOT LOADED') . "\n";
echo str_repeat("=", 50) . "\n";
foreach ($results as $res) {
    echo $res . "\n";
}
echo str_repeat("=", 50) . "\n";
if ($hasErrors) {
    echo "STATUS: SOME TESTS FAILED\n";
    exit(1);
} else {
    echo "STATUS: ALL TESTS PASSED SUCCESSFULLY\n";
    exit(0);
}
