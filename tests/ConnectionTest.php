<?php

namespace Adldap\Tests;

use Adldap\Connections\Ldap;

class ConnectionTest extends FunctionalTestCase
{
    /**
     * This tests that no exception is thrown when trying
     * to perform an LDAP method while suppressing errors.
     */
    public function testConnectionSuppressErrors()
    {
        $ldap = new Ldap();

        $ldap->suppressErrors();

        $this->assertFalse($ldap->bind('test', 'test'));
    }

    /**
     * This tests that an exception is thrown
     * when trying to perform an LDAP method while
     * showing errors.
     */
    public function testConnectionShowErrors()
    {
        $ldap = new Ldap();

        $ldap->showErrors();

        $ldap->connect('invalid-host-that-cannot-connect');

        // PHP emits an E_WARNING on failed bind when unsuppressed.
        // We convert warnings to exceptions or catch standard exception.
        $passes = false;

        set_error_handler(function ($errno, $errstr) {
            throw new \Exception($errstr);
        });

        try {
            $ldap->bind('test', 'test');
        } catch (\Throwable $e) {
            $passes = true;
        } finally {
            restore_error_handler();
        }

        $this->assertTrue($passes);
    }

    public function testEscapeManual()
    {
        $ldap = $this->mock('Adldap\Connections\Ldap')->makePartial();

        $ldap->shouldReceive('isEscapingSupported')->andReturn(false);

        $value = 'admin,ou=users*';

        $escaped = $ldap->escape($value);

        $this->assertEquals('\61\64\6d\69\6e\2c\6f\75\3d\75\73\65\72\73\2a', $escaped);
    }

    public function testEscapeManualFilter()
    {
        $ldap = $this->mock('Adldap\Connections\Ldap')->makePartial();

        $ldap->shouldReceive('isEscapingSupported')->andReturn(false);

        $value = 'admin,ou=users*';

        $escaped = $ldap->escape($value, '', 1);

        $this->assertEquals('admin,ou=users\2a', $escaped);
    }

    public function testEscapeManualDn()
    {
        $ldap = $this->mock('Adldap\Connections\Ldap')->makePartial();

        $ldap->shouldReceive('isEscapingSupported')->andReturn(false);

        $value = 'admin,ou=users*';

        $escaped = $ldap->escape($value, '', 2);

        $this->assertEquals('admin\2cou\3dusers*', $escaped);
    }

    public function testEscapeManualBothFilterAndDn()
    {
        $ldap = $this->mock('Adldap\Connections\Ldap')->makePartial();

        $ldap->shouldReceive('isEscapingSupported')->andReturn(false);

        $value = 'admin,ou=users*';

        $escaped = $ldap->escape($value, '', 3);

        $this->assertEquals('admin\2cou\3dusers\2a', $escaped);
    }
}
