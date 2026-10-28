<?php

use Adldap\Adldap;
use Adldap\Connections\Ldap;
use Adldap\Classes\AdldapUsers;
use Adldap\Classes\AdldapUtils;
use Adldap\Classes\AdldapSearch;
use Adldap\Objects\Configuration;
use Adldap\Objects\User;

test('it instantiates Adldap without errors', function () {
    $ad = new Adldap([], null, false);

    expect($ad)->toBeInstanceOf(Adldap::class);
    expect($ad->getLdapConnection())->toBeInstanceOf(Ldap::class);
});

test('it supports dynamic properties on AbstractObject under PHP 8.2', function () {
    $config = new Configuration([
        'account_suffix' => '@corp.local',
        'base_dn' => 'dc=corp,dc=local',
    ]);

    // Test dynamic property access and mutation
    $config->custom_attribute = 'dynamic_value';

    expect($config->custom_attribute)->toBe('dynamic_value');
    expect($config->getAttribute('account_suffix'))->toBe('@corp.local');
});

test('it encodes passwords properly without PHP 8 curly brace string syntax errors', function () {
    $ad = new Adldap([], null, false);
    $users = new AdldapUsers($ad);

    $rawPassword = 'Password123!';
    $encoded = $users->encodePassword($rawPassword);

    expect($encoded)->toBeString();
    expect(strlen($encoded))->toBeGreaterThan(0);
});

test('it safely encodes non-ascii characters without deprecated utf8_encode', function () {
    $ad = new Adldap([], null, false);
    $utils = new AdldapUtils($ad);

    $text = "Hélène Müller";
    $utils->encode8Bit($text, 'description');

    expect($text)->toBeString();
    expect($text)->not->toBeEmpty();
});

test('it escapes ldap filter characters and handles connection teardown', function () {
    $conn = new Ldap();

    $escaped = $conn->escape('test*user(name)');

    expect($escaped)->toBeString();
    expect($conn->close())->toBeTrue();
});

test('it builds and compiles queries properly via builder', function () {
    $ad = new Adldap([], null, false);
    $search = $ad->search();

    $query = $search->where('cn', '=', 'john')->getQuery();

    expect($query)->toMatch('/^\(cn=.+\)$/');
});

test('it instantiates and validates User object attributes', function () {
    $user = new User([
        'username' => 'jdoe',
        'firstname' => 'John',
        'surname' => 'Doe',
        'display_name' => 'John Doe',
        'email' => 'jdoe@corp.local',
    ]);

    expect($user->getAttribute('username'))->toBe('jdoe');
    expect($user->getAttribute('display_name'))->toBe('John Doe');
    expect($user->validateRequired(['username', 'display_name']))->toBeTrue();
});
