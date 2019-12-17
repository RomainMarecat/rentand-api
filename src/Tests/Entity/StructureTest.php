<?php

namespace App\Tests\Entity;

use Tests\Component\Validator\Constraints\StructureValidator;
use Entity\Structure;

class StructureTest extends \PHPUnit_Framework_TestCase
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
     * @var Tests\Component\Validator\Constraints\StructureValidator
     */
    private $validator;

    /**
     * @var Entity\Structure
     */
    private $entity;

    public function setUp()
    {
        $this->context = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContextInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->entity = new Structure();
        $this->validator = new StructureValidator();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf('Entity\Structure', $this->entity);
    }

    public function testValidateId()
    {
        $this->assertEmpty($this->entity->getId());
    }

    public function testValidateTitle()
    {
        $this->constraint = new \Tests\Component\Validator\Type\StringType();
        $this->context->expects($this->exactly(0))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['string'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setTitle($value['value']);
                $this->validator->validate($this->entity->getTitle(), $this->constraint);
            }
        }
    }

    public function testValidateEmail()
    {
        $this->constraint = new \Tests\Component\Validator\Type\StringType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['string'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setEmail($value['value']);
                $this->validator->validate($this->entity->getEmail(), $this->constraint);
            }
        }
    }

    public function testValidateCode()
    {
        $this->constraint = new \Tests\Component\Validator\Type\StringType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['string'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setCode($value['value']);
                $this->validator->validate($this->entity->getCode(), $this->constraint);
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

    public function testValidatePhone()
    {
        $this->constraint = new \Tests\Component\Validator\Type\OnetooneType();
        $this->constraint->setTargetEntity("\Entity\Phone");
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['onetoone'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setPhone(new \Entity\Phone());
                $this->validator->validate($this->entity->getPhone(), $this->constraint);
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

    public function testValidateAddress()
    {
        $this->constraint = new \Tests\Component\Validator\Type\OnetooneType();
        $this->constraint->setTargetEntity("\Entity\Address");
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['onetoone'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setAddress(new \Entity\Address());
                $this->validator->validate($this->entity->getAddress(), $this->constraint);
            }
        }
    }

    public function testValidateAdverts()
    {
        $this->constraint = new \Tests\Component\Validator\Type\OnetomanyType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['onetomany'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->addAdvert(new \Entity\Advert());
                $this->validator->validate($this->entity->getAdverts(), $this->constraint);
                $this->entity->removeAdvert(new \Entity\Advert());
                $this->validator->validate($this->entity->getAdverts(), $this->constraint);
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
