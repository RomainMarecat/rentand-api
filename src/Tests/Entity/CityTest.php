<?php

namespace App\Tests\Entity;

use Tests\Component\Validator\Constraints\CityValidator;
use Entity\City;

class CityTest extends \PHPUnit_Framework_TestCase
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
     * @var Tests\Component\Validator\Constraints\CityValidator
     */
    private $validator;

    /**
     * @var Entity\City
     */
    private $entity;

    public function setUp()
    {
        $this->context = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContextInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->entity = new City();
        $this->validator = new CityValidator();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf('Entity\City', $this->entity);
    }

    public function testValidateId()
    {
        $this->assertEmpty($this->entity->getId());
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

    public function testValidateGoogleid()
    {
        $this->constraint = new \Tests\Component\Validator\Type\StringType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['string'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setGoogleid($value['value']);
                $this->validator->validate($this->entity->getGoogleid(), $this->constraint);
            }
        }
    }

    public function testValidateLng()
    {
        $this->constraint = new \Tests\Component\Validator\Type\FloatType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['float'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setLng($value['value']);
                $this->validator->validate($this->entity->getLng(), $this->constraint);
            }
        }
    }

    public function testValidateLat()
    {
        $this->constraint = new \Tests\Component\Validator\Type\FloatType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['float'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setLat($value['value']);
                $this->validator->validate($this->entity->getLat(), $this->constraint);
            }
        }
    }

    public function testValidateNorth()
    {
        $this->constraint = new \Tests\Component\Validator\Type\FloatType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['float'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setNorth($value['value']);
                $this->validator->validate($this->entity->getNorth(), $this->constraint);
            }
        }
    }

    public function testValidateSouth()
    {
        $this->constraint = new \Tests\Component\Validator\Type\FloatType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['float'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setSouth($value['value']);
                $this->validator->validate($this->entity->getSouth(), $this->constraint);
            }
        }
    }

    public function testValidateEast()
    {
        $this->constraint = new \Tests\Component\Validator\Type\FloatType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['float'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setEast($value['value']);
                $this->validator->validate($this->entity->getEast(), $this->constraint);
            }
        }
    }

    public function testValidateWest()
    {
        $this->constraint = new \Tests\Component\Validator\Type\FloatType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['float'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setWest($value['value']);
                $this->validator->validate($this->entity->getWest(), $this->constraint);
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

    public function testValidateAdvert()
    {
        $this->constraint = new \Tests\Component\Validator\Type\OnetomanyType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['onetomany'] as $key => $value) {
            if ($key >= 1) {
                            $this->entity->addAdvert(new \Entity\Advert());
                $this->validator->validate($this->entity->getAdvert(), $this->constraint);
                $this->entity->removeAdvert(new \Entity\Advert());
                $this->validator->validate($this->entity->getAdvert(), $this->constraint);
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
