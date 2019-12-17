<?php

namespace App\Tests\Entity;

use Tests\Component\Validator\Constraints\UserValidator;
use Entity\User;

class UserTest extends \PHPUnit_Framework_TestCase
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
     * @var Tests\Component\Validator\Constraints\UserValidator
     */
    private $validator;

    /**
     * @var Entity\User
     */
    private $entity;

    public function setUp()
    {
        $this->context = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContextInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->entity = new User();
        $this->validator = new UserValidator();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf('Entity\User', $this->entity);
    }

    public function testValidateId()
    {
        $this->assertEmpty($this->entity->getId());
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

    public function testValidateNationality()
    {
        $this->constraint = new \Tests\Component\Validator\Type\StringType();
        $this->context->expects($this->exactly(0))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['string'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setNationality($value['value']);
                $this->validator->validate($this->entity->getNationality(), $this->constraint);
            }
        }
    }

    public function testValidateFacebookid()
    {
        $this->constraint = new \Tests\Component\Validator\Type\StringType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['string'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setFacebookid($value['value']);
                $this->validator->validate($this->entity->getFacebookid(), $this->constraint);
            }
        }
    }

    public function testValidateMangopayid()
    {
        $this->constraint = new \Tests\Component\Validator\Type\StringType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['string'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setMangopayid($value['value']);
                $this->validator->validate($this->entity->getMangopayid(), $this->constraint);
            }
        }
    }

    public function testValidateMangopaywalletid()
    {
        $this->constraint = new \Tests\Component\Validator\Type\StringType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['string'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setMangopaywalletid($value['value']);
                $this->validator->validate($this->entity->getMangopaywalletid(), $this->constraint);
            }
        }
    }

    public function testValidateAdmincomment()
    {
        $this->constraint = new \Tests\Component\Validator\Type\TextType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['text'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->setAdmincomment($value['value']);
                $this->validator->validate($this->entity->getAdmincomment(), $this->constraint);
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

    public function testValidateVouchers()
    {
        $this->constraint = new \Tests\Component\Validator\Type\OnetomanyType();
        $this->context->expects($this->exactly($this->constraint->getNbInvalidate()))
            ->method('addViolation')
            ->with($this->constraint->getMessage(), array());

        $this->validator->initialize($this->context);
        foreach ($this->constraint->getValues()['onetomany'] as $key => $value) {
            if ($key >= 1) {
                $this->entity->addVoucher(new \Zeemono\VoucherBundle\Entity\VouchersUsers());
                $this->validator->validate($this->entity->getVouchers(), $this->constraint);
                $this->entity->removeVoucher(new \Zeemono\VoucherBundle\Entity\VouchersUsers());
                $this->validator->validate($this->entity->getVouchers(), $this->constraint);
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
