<?php

namespace App\Tests\Entity;

use Tests\Component\Validator\Constraints\AdvertSportValidator;
use Entity\AdvertSport;

class AdvertSportTest extends \PHPUnit_Framework_TestCase
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
     * @var Tests\Component\Validator\Constraints\AdvertSportValidator
     */
    private $validator;

    /**
     * @var Entity\AdvertSport
     */
    private $entity;

    public function setUp()
    {
        $this->context = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContextInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->entity = new AdvertSport();
        $this->validator = new AdvertSportValidator();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf('Entity\AdvertSport', $this->entity);
    }

    public function testValidateId()
    {
        $this->assertEmpty($this->entity->getId());
    }

    public function testValidateOrdernumber()
    {
        $this->constraint = new \Tests\Component\Validator\Type\IntegerType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['integer'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setOrdernumber($value['value']);
                $this->validator->validate($this->entity->getOrdernumber(), $this->constraint);
            }
        }
    }

    public function testValidateAdvert()
    {
        $this->constraint = new \Tests\Component\Validator\Type\ManytooneType();
        $this->constraint->setTargetEntity("\Entity\Advert");
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['manytoone'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setAdvert(new \Entity\Advert());
                $this->validator->validate($this->entity->getAdvert(), $this->constraint);
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

    public function testValidatePictures()
    {
        $this->constraint = new \Tests\Component\Validator\Type\OnetomanyType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['onetomany'] as $key => $value) {
            if ($key >= 1) {
                            $this->entity->addPicture(new \Entity\Image());
                $this->validator->validate($this->entity->getPictures(), $this->constraint);
                $this->entity->removePicture(new \Entity\Image());
                $this->validator->validate($this->entity->getPictures(), $this->constraint);
                    }
        }
    }

    public function testValidateTranslations()
    {
        $this->constraint = new \Tests\Component\Validator\Type\OnetomanyType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['onetomany'] as $key => $value) {
            if ($key >= 1) {
                            $this->entity->addTranslation(new \Entity\AdvertSportTranslation());
                $this->validator->validate($this->entity->getTranslations(), $this->constraint);
                $this->entity->removeTranslation(new \Entity\AdvertSportTranslation());
                $this->validator->validate($this->entity->getTranslations(), $this->constraint);
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
