<?php

namespace App\Tests\Entity;

use Tests\Component\Validator\Constraints\AdvertValidator;
use Entity\Advert;

class AdvertTest extends \PHPUnit_Framework_TestCase
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
     * @var Tests\Component\Validator\Constraints\AdvertValidator
     */
    private $validator;

    /**
     * @var Entity\Advert
     */
    private $entity;

    public function setUp()
    {
        $this->context = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContextInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->entity = new Advert();
        $this->validator = new AdvertValidator();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf('Entity\Advert', $this->entity);
    }

    public function testValidateId()
    {
        $this->assertEmpty($this->entity->getId());
    }

    public function testValidateTitle()
    {
        $this->constraint = new \Tests\Component\Validator\Type\IntegerType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['integer'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setTitle($value['value']);
                $this->validator->validate($this->entity->getTitle(), $this->constraint);
            }
        }
    }

    public function testValidateFirstname()
    {
        $this->constraint = new \Tests\Component\Validator\Type\StringType();
        $this->context->expects($this->exactly(0))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['string'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setFirstname($value['value']);
                $this->validator->validate($this->entity->getFirstname(), $this->constraint);
            }
        }
    }

    public function testValidateLastname()
    {
        $this->constraint = new \Tests\Component\Validator\Type\StringType();
        $this->context->expects($this->exactly(0))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['string'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setLastname($value['value']);
                $this->validator->validate($this->entity->getLastname(), $this->constraint);
            }
        }
    }

    public function testValidateBirthdate()
    {
        $this->constraint = new \Tests\Component\Validator\Type\DateType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['date'] as $key => $value) {
            if ($key >= 1) {
                $date = new \DateTime();
                $this->entity->setBirthdate($date::createFromFormat($value['format'], $value['value']));
                $this->validator->validate($this->entity->getBirthdate(), $this->constraint, $value['format']);
            }
        }
    }

    public function testValidateSlug()
    {
        $this->constraint = new \Tests\Component\Validator\Type\StringType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['string'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setSlug($value['value']);
                $this->validator->validate($this->entity->getSlug(), $this->constraint);
            }
        }
    }

    public function testValidateStatut()
    {
        $this->constraint = new \Tests\Component\Validator\Type\IntegerType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['integer'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setStatut($value['value']);
                $this->validator->validate($this->entity->getStatut(), $this->constraint);
            }
        }
    }

    public function testValidatePerimeter()
    {
        $this->constraint = new \Tests\Component\Validator\Type\IntegerType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['integer'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setPerimeter($value['value']);
                $this->validator->validate($this->entity->getPerimeter(), $this->constraint);
            }
        }
    }

    public function testValidatePlanning()
    {
        $this->constraint = new \Tests\Component\Validator\Type\StringType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['string'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setPlanning($value['value']);
                $this->validator->validate($this->entity->getPlanning(), $this->constraint);
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
        $this->constraint = new \Tests\Component\Validator\Type\ManytooneType();
        $this->constraint->setTargetEntity("\Entity\User");
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['manytoone'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setUser(new \Entity\User());
                $this->validator->validate($this->entity->getUser(), $this->constraint);
            }
        }
    }

    public function testValidateStructure()
    {
        $this->constraint = new \Tests\Component\Validator\Type\ManytooneType();
        $this->constraint->setTargetEntity("\Entity\Structure");
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['manytoone'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setStructure(new \Entity\Structure());
                $this->validator->validate($this->entity->getStructure(), $this->constraint);
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
                            $this->entity->addTranslation(new \Entity\AdvertTranslation());
                $this->validator->validate($this->entity->getTranslations(), $this->constraint);
                $this->entity->removeTranslation(new \Entity\AdvertTranslation());
                $this->validator->validate($this->entity->getTranslations(), $this->constraint);
            }
        }
    }

    public function testValidateComments()
    {
        $this->constraint = new \Tests\Component\Validator\Type\OnetomanyType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['onetomany'] as $key => $value) {
            if ($key >= 1) {
                            $this->entity->addComment(new \Entity\Comment());
                $this->validator->validate($this->entity->getComments(), $this->constraint);
                $this->entity->removeComment(new \Entity\Comment());
                $this->validator->validate($this->entity->getComments(), $this->constraint);
            }
        }
    }

    public function testValidateCity()
    {
        $this->constraint = new \Tests\Component\Validator\Type\ManytooneType();
        $this->constraint->setTargetEntity("\Entity\City");
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['manytoone'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setCity(new \Entity\City());
                $this->validator->validate($this->entity->getCity(), $this->constraint);
            }
        }
    }

    public function testValidateDiploma()
    {
        $this->constraint = new \Tests\Component\Validator\Type\OnetooneType();
        $this->constraint->setTargetEntity("\Entity\Diploma");
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['onetoone'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setDiploma(new \Entity\Diploma());
                $this->validator->validate($this->entity->getDiploma(), $this->constraint);
            }
        }
    }

    public function testValidateImage()
    {
        $this->constraint = new \Tests\Component\Validator\Type\OnetooneType();
        $this->constraint->setTargetEntity("\Entity\Image");
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['onetoone'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setImage(new \Entity\Image());
                $this->validator->validate($this->entity->getImage(), $this->constraint);
            }
        }
    }

    public function testValidateSports()
    {
        $this->constraint = new \Tests\Component\Validator\Type\OnetomanyType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['onetomany'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->addSport(new \Entity\AdvertSport());
                $this->validator->validate($this->entity->getSports(), $this->constraint);
                $this->entity->removeSport(new \Entity\AdvertSport());
                $this->validator->validate($this->entity->getSports(), $this->constraint);
            }
        }
    }

    public function testValidateMeetings()
    {
        $this->constraint = new \Tests\Component\Validator\Type\OnetomanyType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['onetomany'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->addMeeting(new \Entity\Meeting());
                $this->validator->validate($this->entity->getMeetings(), $this->constraint);
                $this->entity->removeMeeting(new \Entity\Meeting());
                $this->validator->validate($this->entity->getMeetings(), $this->constraint);
            }
        }
    }

    public function testValidateBookings()
    {
        $this->constraint = new \Tests\Component\Validator\Type\OnetomanyType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['onetomany'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->addBooking(new \Entity\Booking());
                $this->validator->validate($this->entity->getBookings(), $this->constraint);
                $this->entity->removeBooking(new \Entity\Booking());
                $this->validator->validate($this->entity->getBookings(), $this->constraint);
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
