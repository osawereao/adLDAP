<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Account Suffix
    |--------------------------------------------------------------------------
    |
    | The account suffix for your Active Directory domain (e.g. '@corp.local').
    |
    */
    'account_suffix' => env('ADLDAP_ACCOUNT_SUFFIX', ''),

    /*
    |--------------------------------------------------------------------------
    | Domain Controllers
    |--------------------------------------------------------------------------
    |
    | The domain controllers to connect to. You can provide an array of multiple
    | domain controllers to balance the queries amongst multiple servers.
    |
    */
    'domain_controllers' => array_filter(
        array_map('trim', explode(',', env('ADLDAP_CONTROLLERS', '127.0.0.1')))
    ),

    /*
    |--------------------------------------------------------------------------
    | Base Distinguished Name (DN)
    |--------------------------------------------------------------------------
    |
    | The base DN for your domain (e.g. 'dc=corp,dc=local').
    |
    */
    'base_dn' => env('ADLDAP_BASE_DN', ''),

    /*
    |--------------------------------------------------------------------------
    | Administrator Credentials
    |--------------------------------------------------------------------------
    |
    | The credentials of an account with administrative/search privileges.
    | Leave blank if your LDAP server supports anonymous binding.
    |
    */
    'admin_username' => env('ADLDAP_ADMIN_USER', ''),
    'admin_password' => env('ADLDAP_ADMIN_PASS', ''),

    /*
    |--------------------------------------------------------------------------
    | Port & SSL / TLS Settings
    |--------------------------------------------------------------------------
    |
    | The port used to communicate with your domain controllers (Default: 389,
    | SSL: 636). If use_ssl is enabled, ad_port will default to 636.
    |
    */
    'ad_port' => (int) env('ADLDAP_PORT', 389),
    'use_ssl' => (bool) env('ADLDAP_USE_SSL', false),
    'use_tls' => (bool) env('ADLDAP_USE_TLS', false),

    /*
    |--------------------------------------------------------------------------
    | Group & Referral Settings
    |--------------------------------------------------------------------------
    |
    | recursive_groups: Recursively lookup group memberships.
    | follow_referrals: Whether to follow LDAP referrals automatically.
    | real_primarygroup: Whether to resolve the real primary group.
    |
    */
    'recursive_groups'  => (bool) env('ADLDAP_RECURSIVE_GROUPS', true),
    'follow_referrals'  => (int) env('ADLDAP_FOLLOW_REFERRALS', 0),
    'real_primarygroup' => (bool) env('ADLDAP_REAL_PRIMARY_GROUP', true),

];
