<?php

namespace Adldap\Tests;

class AdldapObjectTest extends FunctionalTestCase
{
    protected function newAbstractObjectMock()
    {
        return $this->mock('Adldap\Objects\AbstractObject');
    }

    public function testObjectSetAttribute()
    {
        $object = $this->newAbstractObjectMock()->makePartial();

        $object->setAttribute('key', 'value');

        $this->assertEquals('value', $object->getAttribute('key'));
    }

    public function testObjectSetAttributes()
    {
        $object = $this->newAbstractObjectMock()->makePartial();

        $attributes = [
            'first' => 'First Value',
            'second' => 'Second Value',
        ];

        $object->setAttributes($attributes);

        $this->assertEquals($attributes, $object->getAttributes());
    }

    public function testObjectHasAttribute()
    {
        $object = $this->newAbstractObjectMock()->makePartial();

        $object->setAttribute('first', 'First Value');

        $this->assertTrue($object->hasAttribute('first'));
        $this->assertFalse($object->hasAttribute('second'));
    }

    public function testObjectCountAttributes()
    {
        $object = $this->newAbstractObjectMock()->makePartial();

        $attributes = [
            'first' => 'First Value',
            'second' => 'Second Value',
        ];

        $object->setAttributes($attributes);

        $this->assertEquals(2, $object->countAttributes());
    }

    public function testObjectValidateRequiredPass()
    {
        $object = $this->newAbstractObjectMock()->makePartial();

        $required = [
            'required'
        ];

        $object->setRequired($required);

        $object->setAttribute('required', 'Pass');

        $this->assertTrue($object->validateRequired());
    }

    public function testObjectValidateRequiredFailure()
    {
        $object = $this->newAbstractObjectMock()->makePartial();

        $required = [
            'required'
        ];

        $object->setRequired($required);

        $this->expectException('Adldap\Exceptions\AdldapException');

        $object->validateRequired();
    }

    public function testObjectValidateRequiredOnlyPass()
    {
        $object = $this->newAbstractObjectMock()->makePartial();

        $required = [
            'required',
            'another',
        ];

        $object->setRequired($required);

        $object->setAttribute('required', 'Pass');

        $this->assertTrue($object->validateRequired(['required']));
    }

    public function testObjectValidateRequiredOnlyFailure()
    {
        $object = $this->newAbstractObjectMock()->makePartial();

        $required = [
            'required',
            'another',
        ];

        $object->setRequired($required);

        $object->setAttribute('required', 'Pass');

        $this->expectException('Adldap\Exceptions\AdldapException');

        $object->validateRequired(['another']);
    }
}
