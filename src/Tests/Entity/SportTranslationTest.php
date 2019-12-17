<?php

namespace App\Tests\Entity;

use Tests\Component\Validator\Constraints\SportTranslationValidator;
use Entity\SportTranslation;

class SportTranslationTest extends \PHPUnit_Framework_TestCase
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
     * @var Tests\Component\Validator\Constraints\SportTranslationValidator
     */
    private $validator;

    /**
     * @var Entity\SportTranslation
     */
    private $entity;

    public function setUp()
    {
        $this->context = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContextInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->entity = new SportTranslation();
        $this->validator = new SportTranslationValidator();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf('Entity\SportTranslation', $this->entity);
    }

    public function testValidateId()
    {
        $this->assertEmpty($this->entity->getId());
    }

    public function testValidateLocale()
    {
        $this->constraint = new \Tests\Component\Validator\Type\StringType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['string'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setLocale($value['value']);
                $this->validator->validate($this->entity->getLocale(), $this->constraint);
            }
        }
    }

    public function testValidateTitle()
    {
        $this->constraint = new \Tests\Component\Validator\Type\StringType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
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

    public function testValidateSearch()
    {
        $this->constraint = new \Tests\Component\Validator\Type\StringType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['string'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setSearch($value['value']);
                $this->validator->validate($this->entity->getSearch(), $this->constraint);
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

    public function testValidateSport()
    {
        $this->constraint = new \Tests\Component\Validator\Type\ManytooneType();
        $this->constraint->setTargetEntity("\Entity\Sport");
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['manytoone'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setSport(new \Entity\Sport());
                $this->validator->validate($this->entity->getSport(), $this->constraint);
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
