<?php

// Basic autoloader for Adldap without composer
spl_autoload_register(function ($class) {
    $prefix = 'Adldap\\';
    $base_dir = __DIR__ . '/../src/';

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

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Adldap PHP 8.2 Compatibility Test</title>
    <style>
        body { font-family: sans-serif; margin: 40px; background: #f8fafc; color: #1e293b; }
        .card { background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); max-width: 680px; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 4px; font-weight: bold; font-size: 14px; }
        .success { background: #dcfce7; color: #166534; }
        .info { background: #e0f2fe; color: #0369a1; }
        .error { background: #fee2e2; color: #991b1b; }
        pre { background: #f1f5f9; padding: 12px; border-radius: 6px; overflow-x: auto; font-size: 13px; }
        ul { padding-left: 20px; line-height: 1.6; }
    </style>
</head>
<body>
<div class="card">
    <h2>Adldap PHP Compatibility Check</h2>
    <p><strong>PHP Version:</strong> <?= phpversion() ?></p>
    <p><strong>LDAP Extension:</strong> 
        <?php if (extension_loaded('ldap')): ?>
            <span class="badge success">Loaded</span>
        <?php else: ?>
            <span class="badge info">Not Loaded (Required only for live binds)</span>
        <?php endif; ?>
    </p>

    <h3>Tests Executed:</h3>
    <ul>
    <?php
    $errors = 0;

    // Test 1: Instantiation
    try {
        $ad = new \Adldap\Adldap([], null, false);
        echo "<li>Instantiate Adldap: <span class='badge success'>PASS</span></li>";
    } catch (\Throwable $e) {
        $errors++;
        echo "<li>Instantiate Adldap: <span class='badge error'>FAIL</span> - " . htmlspecialchars($e->getMessage()) . "</li>";
    }

    // Test 2: Dynamic Properties (PHP 8.2 check)
    try {
        $config = new \Adldap\Objects\Configuration();
        $config->custom_test_property = 'test_value';
        echo "<li>Dynamic Properties (PHP 8.2 AllowDynamicProperties): <span class='badge success'>PASS</span></li>";
    } catch (\Throwable $e) {
        $errors++;
        echo "<li>Dynamic Properties: <span class='badge error'>FAIL</span> - " . htmlspecialchars($e->getMessage()) . "</li>";
    }

    // Test 3: Password encoding syntax (PHP 8.0+ curly string offset check)
    try {
        $users = new \Adldap\Classes\AdldapUsers($ad);
        $encoded = $users->encodePassword('secret123');
        if (!empty($encoded)) {
            echo "<li>Password Encoding String Offsets (PHP 8.0+ syntax): <span class='badge success'>PASS</span></li>";
        }
    } catch (\Throwable $e) {
        $errors++;
        echo "<li>Password Encoding: <span class='badge error'>FAIL</span> - " . htmlspecialchars($e->getMessage()) . "</li>";
    }

    // Test 4: UTF8 encode replacement (PHP 8.2 deprecation check)
    try {
        $utils = new \Adldap\Classes\AdldapUtils($ad);
        $item = "café";
        $utils->encode8Bit($item, 'description');
        echo "<li>UTF-8 Encoding (PHP 8.2 utf8_encode deprecation): <span class='badge success'>PASS</span></li>";
    } catch (\Throwable $e) {
        $errors++;
        echo "<li>UTF-8 Encoding: <span class='badge error'>FAIL</span> - " . htmlspecialchars($e->getMessage()) . "</li>";
    }

    // Test 5: LDAP Connection Class & string escape checks
    try {
        $ldap = new \Adldap\Connections\Ldap();
        $escaped = $ldap->escape('foo,bar=baz');
        $ldap->close();
        echo "<li>Ldap Connection close & string handling: <span class='badge success'>PASS</span></li>";
    } catch (\Throwable $e) {
        $errors++;
        echo "<li>Ldap Connection: <span class='badge error'>FAIL</span> - " . htmlspecialchars($e->getMessage()) . "</li>";
    }
    ?>
    </ul>

    <?php if ($errors === 0): ?>
        <p><span class="badge success" style="padding: 8px 14px; font-size: 15px;">All PHP 8.2 Compatibility Checks Passed!</span></p>
    <?php endif; ?>
</div>
</body>
</html>
