<?php

namespace Adldap\Tests\Classes;

use Adldap\Tests\FunctionalTestCase;

class AdldapUsersTest extends FunctionalTestCase
{
    /**
     * @var \Mockery\MockInterface
     */
    protected $connectionMock;

    /**
     * @var \Mockery\Mock
     */
    protected $adMock;

    /**
     * @var \Mockery\Mock
     */
    protected $userClassMock;

    public $stubUser = [
        'username' => 'jdoe',
        'firstname' => 'John',
        'surname' => 'Doe',
        'email' => 'jdoe@acme.com',
        'container' => [],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->connectionMock = $this->newConnectionMock();

        $this->adMock = $this->mock('Adldap\Adldap')->makePartial();

        $this->userClassMock = $this->mock('Adldap\Classes\AdldapUsers')->makePartial();

        $this->adMock->setLdapConnection($this->connectionMock);

        $this->userClassMock->__construct($this->adMock);
    }

    public function testUserAuthenticate()
    {
        $this->connectionMock->shouldReceive('bind')->once()->andReturn(true);

        $this->assertTrue($this->userClassMock->authenticate('username', 'password'));
    }

    public function testUserAll()
    {
        $this->connectionMock
            ->shouldReceive('search')->once()->andReturn(true)
            ->shouldReceive('getEntries')->once()->andReturn([]);

        $this->assertEquals([], $this->userClassMock->all());
    }

    public function testUserFind()
    {
        $this->connectionMock
            ->shouldReceive('search')->once()->andReturn(true)
            ->shouldReceive('getEntries')->once()->andReturn([
                'count' => 1,
                0 => [
                    'count' => 1,
                    'cn' => [
                        'count' => 1,
                        0 => 'John Doe',
                    ],
                ],
            ]);

        $this->assertEquals(['cn' => 'John Doe'], $this->userClassMock->find('jdoe'));
    }

    public function testUserPasswordExpiry()
    {
        $this->connectionMock
            ->shouldReceive('search')->twice()->andReturn(true)
            ->shouldReceive('getEntries')->twice()->andReturn(
                [
                    'count' => 1,
                    0 => [
                        'count' => 2,
                        'pwdlastset' => [
                            'count' => 1,
                            0 => '130700000000000000',
                        ],
                        'useraccountcontrol' => [
                            'count' => 1,
                            0 => '512',
                        ],
                    ],
                ],
                [
                    'count' => 1,
                    0 => [
                        'count' => 1,
                        'maxpwdage' => [
                            'count' => 1,
                            0 => '-37108517437440',
                        ],
                    ],
                ]
            );

        $expected = [
            'expires' => true,
            'has_expired' => false,
            'expiry_timestamp' => '1426462914',
            'expiry_formatted' => '2015-03-15 23:41:54',
        ];

        $this->assertEquals($expected, $this->userClassMock->passwordExpiry('jdoe'));
    }
}
