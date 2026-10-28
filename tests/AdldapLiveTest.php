<?php

namespace Adldap\Tests;

use Adldap\Adldap;

class AdldapLiveTest extends FunctionalTestCase
{
    /**
     * This tests that the public georgia tech LDAP server
     * is up and running. This is useful for making sure a notice
     * is placed on the readme if it's no longer active.
     */
    public function testGeorgiaTechConnection()
    {
        $config = [
            'account_suffix' => "@gatech.edu",

            'domain_controllers' => ["whitepages.gatech.edu"],

            'base_dn' => 'dc=whitepages,dc=gatech,dc=edu',

            'admin_username' => null,

            'admin_password' => null,
        ];

        try {
            $ad = new Adldap($config);
            $this->assertTrue($ad->getLdapConnection()->isBound());
        } catch (\Throwable $e) {
            $this->markTestSkipped('Public Georgia Tech LDAP server is currently unreachable or offline.');
        }
    }
}
