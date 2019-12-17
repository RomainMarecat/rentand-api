<?php

namespace App\Tests\Entity;

use Tests\Component\Validator\Constraints\AddressValidator;
use Entity\Address;

class AddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Symfony\Component\Validator\Constraint
     */
    private $constraint;

    /**
     * @var Symfony\Component\Validator\ExecutionContextInterface
     */
    private $context;

    /**
     * @var Tests\Component\Validator\Constraints\AddressValidator
     */
    private $validator;

    /**
     * @var Entity\Address
     */
    private $entity;

    public function setUp()
    {
        $this->context = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContextInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->entity = new Address();
        $this->validator = new AddressValidator();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf('Entity\Address', $this->entity);
    }

    public function testValidateId()
    {
        $this->assertEmpty($this->entity->getId());
    }

    public function testValidateStreet()
    {
        $this->constraint = new \Tests\Component\Validator\Type\StringType();
        $this->context->expects($this->exactly(0))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['string'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setStreet($value['value']);
                $this->validator->validate($this->entity->getStreet(), $this->constraint);
            }
        }
    }

    public function testValidatePostalcode()
    {
        $this->constraint = new \Tests\Component\Validator\Type\StringType();
        $this->context->expects($this->exactly(0))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['string'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setPostalcode($value['value']);
                $this->validator->validate($this->entity->getPostalcode(), $this->constraint);
            }
        }
    }

    public function testValidateCity()
    {
        $this->constraint = new \Tests\Component\Validator\Type\StringType();
        $this->context->expects($this->exactly(0))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['string'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setCity($value['value']);
                $this->validator->validate($this->entity->getCity(), $this->constraint);
            }
        }
    }

    public function testValidateCountry()
    {
        $this->constraint = new \Tests\Component\Validator\Type\StringType();
        $this->context->expects($this->exactly(0))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['string'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setCountry($value['value']);
                $this->validator->validate($this->entity->getCountry(), $this->constraint);
            }
        }
    }

    public function testValidateCreatedat()
    {
        $this->constraint = new \Tests\Component\Validator\Type\DatetimeType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['datetime'] as $key => $value) {
            if ($key >= 1) {
                $date = new \DateTime();
                $this->entity->setCreatedat($date::createFromFormat($value['format'], $value['value']));
                $this->validator->validate($this->entity->getCreatedat(), $this->constraint, $value['format']);
            }
        }
    }

    public function testValidateUpdatedat()
    {
        $this->constraint = new \Tests\Component\Validator\Type\DatetimeType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['datetime'] as $key => $value) {
            if ($key >= 1) {
                $date = new \DateTime();
                $this->entity->setUpdatedat($date::createFromFormat($value['format'], $value['value']));
                $this->validator->validate($this->entity->getUpdatedat(), $this->constraint, $value['format']);
            }
        }
    }

    public function testValidateUser()
    {
        $this->constraint = new \Tests\Component\Validator\Type\OnetooneType();
        $this->constraint->setTargetEntity("\Entity\User");
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['onetoone'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setUser(new \Entity\User());
                $this->validator->validate($this->entity->getUser(), $this->constraint);
            }
        }
    }

    public function testValidateStructure()
    {
        $this->constraint = new \Tests\Component\Validator\Type\OnetooneType();
        $this->constraint->setTargetEntity("\Entity\Structure");
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['onetoone'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setStructure(new \Entity\Structure());
                $this->validator->validate($this->entity->getStructure(), $this->constraint);
            }
        }
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->constraint);
        unset($this->entity);
        unset($this->context);
        unset($this->validator);
    }
}
