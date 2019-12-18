<?php

namespace App\Controller\Restricted;

use Entity\Address;
use Entity\Comment;
use Entity\Diploma;
use Entity\Image;
use Entity\Offer;
use Entity\Phone;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MeController extends FOSRestController
{

    /**
     * @Annotations\View(serializerGroups={"Default", "getMe"})
     */
    public function getMeAction()
    {
        try {
            $user = $this->getUser();

            if (!is_object($user)) {
                throw $this->createNotFoundException();
            }

            return $user;
        } catch (\Exception $e) {
            $this->get('logger')->info('error', array('message' => $e->getMessage()));
        }
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getAdvertComplete"})
     */
    public function getAdvertIscompleteAction()
    {
        $user = $this->getUser();
        /*
         * 0 : No profil created
         * 1 : Profil waiting
         * 2 : Profil validated
         */
        if (!is_object($user)) {
            throw $this->createNotFoundException();
        }

        $users = $user->getAdverts();
        $res = array(
            'status' => 0,
            'picture' => ""
        );
        foreach ($users as $item) {
            if ($item->getStatut() == 1 && $item->getEnabled()) {
                if (is_object($item->getImage())) {
                    $img = $item->getImage()->getFilename() . "." . $item->getImage()->getExt();
                } else {
                    $img = "";
                }
                return array(
                    'status' => 2,
                    'picture' => $img
                );
            } else if ($item->getEnabled()) {
                $res["status"] = 1;
            }
        }

        return $res;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "patchMe"})
     */
    public function patchMeAction(Request $request)
    {

        $user = $this->getUser();

        if (!is_object($user)) {
            throw $this->createNotFoundException();
        }

        $params = json_decode($request->getContent(), true);
        if (isset($params['type'])) {
            if (!is_object($user->getAddress())) {
                $address = new address;
                $address->setUser($user);
                $user->setAddress($address);
            }
            if (!is_object($user->getPhone())) {
                $phone = new phone;
                $phone->setUser($user);
                $user->setPhone($phone);
            }
        }

        if (isset($params['firstName'])) {
            $user->setFirstName($params['firstName']);
        }
        if (isset($params['lastName'])) {
            $user->setLastName($params['lastName']);
        }
        if (isset($params['gender'])) {
            $user->setGender($params['gender']);
        }
        if (isset($params['birthdate'])) {
            $user->setBirthdate(new \DateTime($params['birthdate']['date']));
        }
        if (isset($params['nationality'])) {
            $user->setNationality($params['nationality']);
        }
        if (isset($params['newsletter'])) {
            $user->setNewsletter($params['newsletter']);
        }
        if (isset($params['address'])) {
            $user->getAddress()->setStreet($params['address']['street']);
            $user->getAddress()->setPostalCode($params['address']['postalCode']);
            $user->getAddress()->setCity($params['address']['city']);
            $user->getAddress()->setCountry($params['address']['country']);
        }
        if (isset($params['phone'])) {
            $user->getPhone()->setNumber($params['phone']['number']);
            $user->getPhone()->setCountryCode($params['phone']['countryCode']);
            $user->getPhone()->setCountryNumber($params['phone']['countryNumber']);
        }
        if (isset($params['facebookId'])) {
            $user->setFacebookId($params['facebookId']);
        }
        if (isset($params['mangopayId'])) {
            $user->setMangopayId($params['mangopayId']);
        }
        if (isset($params['planning'])) {
            $user->setPlanningId($params['planning']);
        }
        if (isset($params['planningToken'])) {
            $user->setPlanningToken($params['planningToken']);
        }

        $validator = $this->get('validator');
        $userManager = $this->get('fos_user.user_manager');

        $validator = $this->get('validator');
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            throw new HttpException(400, 'Invalid user: ' . $errors);
        }
        $userManager->updateUser($user);

        return $user;
    }


    /**
     * @Annotations\View(serializerGroups={"Default", "getMyAdverts"})
     */
    public function getMyAdvertsAction()
    {

        $user = $this->getUser();

        if (!is_object($user)) {
            throw $this->createNotFoundException();
        }

        $users = $user->getAdverts();
        $users = $users->map(function ($item) {
            // return $item->getEnabled();
            if ($item->getEnabled()) {
                return $item;
            }
        });

        $params = $this->get('app.params')->getTitles();

        return array(
            'adverts' => $users,
            'params' => $params);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getAdvert"})
     */
    public function getAdvertAction($id)
    {

        $user = $this->getDoctrine()->getRepository('App:Advert')->find($id);
        if (!is_object($user)) {
            throw $this->createNotFoundException();
        }
        return $user;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getMyBookings"})
     */
    public function getMyBookingsAction()
    {
        /**
         * UGLY UGLY UGLY !
         * @todo repository App:Booking request with partial {booking, courses, user}
         * @since return $user->getBookings();
         */
        $user = $this->getUser();

        if (!is_object($user)) {
            throw $this->createNotFoundException();
        }
        return $this->get('manager.booking')->getByUser();
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getMyPayments"})
     */
    public function getMyPaymentsAction()
    {
        /**
         * UGLY UGLY UGLY !
         * @todo repository App:Booking request with partial {booking, courses, user}
         * @since return $user->getBookings();
         */
        $user = $this->getUser();

        if (!is_object($user)) {
            throw $this->createNotFoundException();
        }
        return $this->get('manager.booking')->getPaymentsMono();
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getBooking"})
     */
    public function getBookingAction($id)
    {
        $booking = $this->getDoctrine()->getRepository('App:Booking')->find($id);
        if (!is_object($booking)) {
            throw $this->createNotFoundException();
        }
        return $booking;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "postAdvert"})
     */
    public function postAdvertAction(Request $request)
    {

        $user = $this->getUser();

        $data = json_decode($request->getContent(), true);
        $dataCities = $data['cities'];
        unset($data['cities']);
        $dataDiploma = $data['diploma'];
        unset($data['diploma']);
        $dataSports = $data['sports'];
        unset($data['sports']);
        $dataImage = $data['image'];
        unset($data['image']);

        $em = $this->getDoctrine()->getManager();

        // add new advert without city nor sports
        $dataEncode = json_encode($data);
        $serializer = $this->get('serializer');
        $user = $serializer->deserialize($dataEncode, 'Entity\Advert', 'json');
        $user->setStatut(0);
        $user->setEnabled(1);

        // add advert relation to user
        $user->setUser($user);
        $user->setFirstName($user->getFirstName());
        $user->setLastName($user->getLastName());

        // add translations relation
        foreach ($user->getTranslations() as $translation) {
            $translation->setAdvert($user);
        }

        // add cities to advert depending if city already exists or not
        $user = $this->addCities($dataCities, $user, $serializer);

        //add diploma to advert
        $user = $this->addDiploma($dataDiploma, $user);

        //add image to advert
        if ($dataImage) {
            $user = $this->addImage($dataImage, $user);
        }

        // add sports to advert
        foreach ($dataSports as $sport) {
            $user = $this->addSport($sport, $user, $serializer);
        }

        $validator = $this->get('validator');
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            throw new HttpException(400, 'Invalid advert: ' . $errors);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        //rename image with slug
        $user = $this->renameImages($user, 'img');
        $user = $this->renameImages($user, 'diploma');
        $user = $this->renameImages($user, 'sports');

        return $user;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "patchAdvert"})
     */
    public function patchAdvertAction(Request $request, $id)
    {

        $user = $this->getUser();
        $user = $this->getDoctrine()->getRepository('App:Advert')->find($id);
        if (($user->getUser() != $user) && ($user->getStructure() != $user->getStructure())) {
            throw new HttpException(400, 'Invalid access');
        }

        $data = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();

        $type = $data['type'];
        $serializer = $this->get('serializer');

        if ($type == 'lang') {
            $user->setLanguages($data['languages']);
        } elseif ($type == 'desc') {
            foreach ($data['translations'] as $translation) {
                $editedTrans = $this->getDoctrine()->getRepository('App:AdvertTranslation')->findOneBy(array('locale' => $translation['locale'], 'advert' => $user));
                $editedTrans->setDescription1($translation['description1']);
                $editedTrans->setDescription2($translation['description2']);
                $editedTrans->setDescription3($translation['description3']);
                $em->persist($editedTrans);
            }
        } elseif ($type == 'img') {
            if ($user->getImage()) {
                $em->remove($user->getImage());
                $em->flush();
            }
            $user = $this->addImage($data['image'], $user);
        } elseif ($type == 'title') {
            $user->setTitle($data['title']);
        } elseif ($type == 'diploma') {
            if ($user->getDiploma() == null) {
                $diploma = new diploma;
                $diploma->setAdvert($user);
                $user->setDiploma($diploma);
            }
            $user->getDiploma()->setTitle($data['diploma']);
        } elseif ($type == 'proCard') {
            $user = $this->addDiploma($data, $user);
        } elseif ($type == 'passions') {
            $user->setPassions($data['passions']);
        } elseif ($type == 'sports') {
            foreach ($data['sports'] as $id => $sport) {
                if ($sport['id'] == 'remove') {
                    $sport = $this->getDoctrine()->getRepository('App:Sport')->find($id);
                    $removedSport = $this->getDoctrine()->getRepository('App:AdvertSport')->findOneBy(array('sport' => $sport, 'advert' => $user));
                    if (is_object($removedSport)) {
                        $user->removeSport($removedSport);
                        $removedSport->setAdvert(null);
                    }
                } else {
                    $sportExist = $this->getDoctrine()->getRepository('App:Sport')->find($id);
                    $removedSport = $this->getDoctrine()->getRepository('App:AdvertSport')->findOneBy(array('sport' => $sportExist, 'advert' => $user));
                    if (is_object($removedSport)) {
                        $user->removeSport($removedSport);
                        $removedSport->setAdvert(null);
                    }
                    $sport['id'] = $id;
                    $user = $this->addSport($sport, $user, $serializer);
                }
            }
        } elseif ($type == 'cities') {
            foreach ($user->getMeetings() as $meeting) {
                $user->removeMeeting($meeting);
                $meeting->setAdvert(null);
            }
            foreach ($user->getCities() as $city) {
                $user->removeCity($city);
            }
            $user = $this->addCities($data['cities'], $user, $serializer);
        } elseif ($type == 'cancel') {
            $user->setCancel($data['cancel']);
        }

        $validator = $this->get('validator');
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            throw new HttpException(400, 'Invalid advert: ' . $errors);
        }

        $em->persist($user);
        $em->flush();

        //rename image with slug
        if ($type == 'sports' || $type == 'img') {
            $user = $this->renameImages($user, $type);
        }

        return array();
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "deleteAdvert"})
     */
    public function deleteAdvertAction($id)
    {

        $user = $this->getUser();
        $user = $this->getDoctrine()->getRepository('App:Advert')->find($id);
        if (!is_object($user)) {
            throw $this->createNotFoundException();
        }
        if (($user->getUser() != $user) && ($user->getStructure()->getUser() != $user)) {
            throw new HttpException(400, 'Invalid access');
        }

        $em = $this->getDoctrine()->getManager();
        $user->setEnabled(0);
        $em->merge($user);
        $em->flush();

        return $user;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "postCheckphone"})
     */
    public function postCheckphoneAction($code)
    {

        $user = $this->getUser();
        $phone = $user->getPhone();

        if ($code == $phone->getToken()) {
            $phone->setChecked(true);
            $em = $this->getDoctrine()->getManager();
            $em->persist($phone);
            $em->flush();
            return true;
        }
        return false;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getToken"})
     */
    public function getTokenAction()
    {

        $user = $this->getUser();

        return array(
            'country' => $user->getPhone()->getCountryNumber(),
            'number' => $user->getPhone()->getNumber(),
            'token' => $user->getPhone()->getToken()
        );
    }

    private function addCities($data, $user, $serializer)
    {
        foreach ($data as $dataCity) {
            $dataMeetings = $dataCity['meetings'];
            unset($dataCity['meetings']);
            $city = $this->getDoctrine()->getRepository('App:City')->findOneByGoogleId($dataCity['googleId']);
            if (!is_object($city)) {
                $city = $serializer->deserialize(json_encode($dataCity), 'Entity\City', 'json');
                $em = $this->getDoctrine()->getManager();
                $em->persist($city);
            }
            foreach ($dataMeetings as $dataMeeting) {
                $meeting = $serializer->deserialize(json_encode($dataMeeting), 'Entity\Meeting', 'json');
                $meeting->setCity($city);
                $meeting->setAdvert($user);
                $user->addMeeting($meeting);
            }
            $user->addCity($city);
            $city->addAdvert($user);
        }
        return $user;
    }

    private function addDiploma($data, $user)
    {
        // create object
        if (is_null($user->getDiploma())) {
            $diploma = new diploma;
            $diploma->setAdvert($user);
            $user->setDiploma($diploma);
        } else {
            $diploma = $user->getDiploma();
        }

        // Add Title
        if (isset($data['title'])) {
            $diploma->setTitle($data['title']);
        }

        // add pro card
        if (isset($data['file_name'])) {
            $ext = explode(".", $data['file_name'])[1];
            $diploma->setFilename($user->getUser()->getId());
            $diploma->setExt($ext);
            $string = base64_decode(urldecode($data['file']));
            $folder = $this->getParameter('folder_temp');
            $fileName = $diploma->getFilename();
            $fileExt = $diploma->getExt();

            $this->createFile($string, $fileName, $fileExt, $folder);
        }


        return $user;
    }

    private function addImage($data, $user)
    {
        $image = new image;
        $image->setFilename($user->getUser()->getId());
        $image->setExt("png");
        $image->setAdvert($user);
        $user->setImage($image);

        $string = base64_decode($data);
        $folder = $this->container->getParameter('folder_temp');
        $fileName = $image->getFilename();
        $fileExt = $image->getExt();

        $this->createFile($string, $fileName, $fileExt, $folder);

        return $user;
    }

    private function createFile($string, $fileName, $fileExt, $folder)
    {

        // create file
        $pathFile = './' . $folder . "/" . $fileName . "." . $fileExt;
        file_put_contents($pathFile, $string);
        return true;
    }

    private function renameImages($user, $type = null)
    {

        $em = $this->getDoctrine()->getManager();
        $folder_temp = $this->getParameter('folder_temp');
        $diploma_folder = $this->getParameter('folder_diploma');
        $folder_image = $this->getParameter('folder_image');
        $slug = $user->getSlug();

        if ($type == 'img') {
            $image = $user->getImage();
            if ($image) {
                $old_path = './' . $folder_temp . "/" . $image->getFileName() . "." . $image->getExt();
                $new_path = './' . $folder_image . "/" . $slug . "." . $image->getExt();
                if (file_exists($new_path)) {
                    unlink($new_path);
                }
                rename($old_path, $new_path);
                $image->setFilename($slug);
                $em->persist($image);
            }
        }

        if ($type === 'diploma') {
            $diploma = $user->getDiploma();
            if ($diploma) {
                if (!is_null($diploma->getFileName()) and !is_null($diploma->getExt())) {
                    $old_path = './' . $folder_temp . "/" . $diploma->getFileName() . "." . $diploma->getExt();
                    $new_path = './' . $diploma_folder . "/" . $slug . "." . $diploma->getExt();
                    if (file_exists($new_path)) {
                        unlink($new_path);
                    }
                    rename($old_path, $new_path);
                    $diploma->setFilename($slug);
                }

                $em->persist($diploma);
            }
        }

        if ($type == 'sports') {
            foreach ($user->getSports() as $sport) {
                if ($sport->getPictures() != null) {
                    $loop = 1;
                    foreach ($sport->getPictures() as $picture) {
                        if ($sport->getSport() != null) {
                            $old_path = './' . $folder_temp . "/" . $picture->getFileName() . "." . $picture->getExt();
                            $new_path = './' . $folder_image . "/" . $slug . "-" . $sport->getSport()->getSlug() . "-" . $loop . "." . $picture->getExt();
                            if (file_exists($old_path) && ($old_path != $new_path)) {
                                if (file_exists($new_path)) {
                                    unlink($new_path);
                                }
                                rename($old_path, $new_path);
                                $picture->setFilename($slug . "-" . $sport->getSport()->getSlug() . "-" . $loop);
                                $em->persist($picture);
                            }
                            $loop++;
                        }
                    }
                }
            }
        }

        $em->flush();

        return $user;
    }


    private function addSport($data, $user, $serializer)
    {
        $sport = $this->getDoctrine()->getRepository('App:Sport')->find($data['id']);

        if (is_object($sport)) {
            unset($data['id']);
            $dataPictures = $data['pictures'];
            unset($data['pictures']);
            $dataSpecialities = $data['specialities'];
            unset($data['specialities']);

            $newSport = $serializer->deserialize(json_encode($data), 'Entity\AdvertSport', 'json');
            $newSport->setAdvert($user);
            $newSport->setSport($sport);
            $newSport->setOrderNumber(0);
            $user->addSport($newSport);

            // add translations relation
            foreach ($newSport->getTranslations() as $translation) {
                $translation->setAdvertSport($newSport);
            }

            foreach ($dataSpecialities as $newSpeciality) {
                $speciality = $this->getDoctrine()->getRepository('App:Sport')->find($newSpeciality);
                if (is_object($speciality)) {
                    $newSport->addSpeciality($speciality);
                }
            }

            $loop = 1;
            foreach ($dataPictures as $picture) {
                $image = new image;
                $image->setFilename($user->getUser()->getId() . "_" . $sport->getSlug() . "_" . $loop);
                $image->setExt("png");
                $image->setAdvertSport($newSport);
                $newSport->addPicture($image);

                $string = base64_decode($picture);
                $folder = $this->container->getParameter('folder_temp');
                $fileName = $image->getFilename();
                $fileExt = $image->getExt();

                $this->createFile($string, $fileName, $fileExt, $folder);
                $loop++;
            }

            return $user;
        }
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "postComment"})
     */
    public function postCommentsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $data = json_decode($request->getContent(), true);
        if (!isset($data['advert']) || !isset($data['grade']) || !isset($data['comment'])) {
            throw $this->createNotFoundException();
        }

        $user = $this->getUser();
        $user = $this->getDoctrine()->getRepository('App:Advert')->find($data['advert']);
        $comment = new Comment();
        if (!is_object($comment) || !is_object($user) || !is_object($user)) {
            throw $this->createNotFoundException();
        }

        /* Setting comment */
        $comment->setUser($user);
        $comment->setAdvert($user);
        $comment->setGrade($data['grade']);
        $comment->setComment($data['comment']);
        $comment->setValidated(0);

        /* Saving comment */
        $em->persist($comment);
        $em->flush();

        return $comment;
    }
}
