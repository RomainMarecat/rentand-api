<?php

namespace App\Tests\Entity;

use Tests\Component\Validator\Constraints\AdvertSportTranslationValidator;
use Entity\AdvertSportTranslation;

class AdvertSportTranslationTest extends \PHPUnit_Framework_TestCase
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
     * @var Tests\Component\Validator\Constraints\AdvertSportTranslationValidator
     */
    private $validator;

    /**
     * @var Entity\AdvertSportTranslation
     */
    private $entity;

    public function setUp()
    {
        $this->context = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContextInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->entity = new AdvertSportTranslation();
        $this->validator = new AdvertSportTranslationValidator();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf('Entity\AdvertSportTranslation', $this->entity);
    }

    public function testValidateId()
    {
        $this->assertEmpty($this->entity->getId());
    }

    public function testValidateLocale()
    {
        $this->constraint = new \Tests\Component\Validator\Type\StringType();
        $this->context->expects($this->exactly(0))
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

    public function testValidateHandisport()
    {
        $this->constraint = new \Tests\Component\Validator\Type\StringType();
        $this->context->expects($this->exactly(0))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['string'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setHandisport($value['value']);
                $this->validator->validate($this->entity->getHandisport(), $this->constraint);
            }
        }
    }

    public function testValidateDescription()
    {
        $this->constraint = new \Tests\Component\Validator\Type\TextType();
        $this->context->expects($this->exactly(0))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['text'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setDescription($value['value']);
                $this->validator->validate($this->entity->getDescription(), $this->constraint);
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

    public function testValidateAdvertsport()
    {
        $this->constraint = new \Tests\Component\Validator\Type\ManytooneType();
        $this->constraint->setTargetEntity("\Entity\AdvertSport");
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['manytoone'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setAdvertsport(new \Entity\AdvertSport());
                $this->validator->validate($this->entity->getAdvertsport(), $this->constraint);
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
