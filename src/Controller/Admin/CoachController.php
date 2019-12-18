<?php

namespace App\Controller\Admin;

use App\Entity\Diploma;
use App\Manager\Admin\UserManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Annotations\Prefix("admin")
 * @Annotations\NamePrefix("_admin")
 */
class CoachController extends AbstractFOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"adminGetCoachs"})
     * @Annotations\Get("/adverts")
     * @param Request       $request
     * @param UserManager $userManager
     *
     * @return JsonResponse
     */
    public function getCoachsAction(Request $request, UserManager $userManager)
    {
        return $userManager->adminCget();
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getCoach"})
     */
    public function getCoachAction($id)
    {

        $user = $this->getDoctrine()->getRepository('App:User')->find($id);

        $urls = array();

        if ($user->getImage()) {
            $imgUrl = $_SERVER['SERVER_NAME'] . "/uploads/images/" . $user->getImage()->getFilename() . "." . $user->getImage()->getExt();
            $urls['mainImg'] = $imgUrl;
        } else {
            $imgUrl = null;
        }
        if ($user->getDiploma()) {
            $diplomaUrl = $_SERVER['SERVER_NAME'] . "/uploads/diplomas/" . $user->getDiploma()->getFilename() . "." . $user->getDiploma()->getExt();
            $urls['diploma'] = $imgUrl;
        } else {
            $diplomaUrl = null;
        }
        foreach ($user->getSports() as $sport) {
            $sportId = $sport->getSport()->getId();
            $urls[$sportId] = array();
            foreach ($sport->getPictures() as $picture) {
                $picUrl = $_SERVER['SERVER_NAME'] . "/uploads/images/" . $picture->getFilename() . "." . $picture->getExt();
                array_push($urls[$sportId], $picUrl);
            }
        }

        if (!is_object($user)) {
            throw $this->createNotFoundException();
        }
        return array(
            'advert' => $user,
            'urls' => $urls,
        );
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getUserCoach"})
     */
    public function getUserCoachAction(Request $request, $id)
    {
        $user = $this->getDoctrine()->getRepository('App:User')->find($id);
        $users = $this->getDoctrine()->getRepository('App:User')->findbyUser($user);
        if (!is_object($users)) {
            throw $this->createNotFoundException();
        } else {
            $page = $request->query->get('page');
            if (isset($page)) {
                $perPage = $request->query->get('perPage');
                $paginator = $this->get('knp_paginator');
                if (!isset($perPage)) {
                    $perPage = 20;
                }
                $results = $paginator->paginate($users, $page, $perPage);
                return $results;
            } else {
                return $users;
            }
        }
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getStructure"})
     */
    public function getStructureCoachAction(Request $request, $id)
    {

        $structure = $this->getDoctrine()->getRepository('App:Structure')->find($id);
        $users = $this->getDoctrine()->getRepository('App:User')->findbyStructure($structure);
        if (!is_object($users)) {
            throw $this->createNotFoundException();
        } else {
            $page = $request->query->get('page');
            if (isset($page)) {
                $perPage = $request->query->get('perPage');
                $paginator = $this->get('knp_paginator');
                if (!isset($perPage)) {
                    $perPage = 20;
                }
                $results = $paginator->paginate($users, $page, $perPage);
                return $results;
            } else {
                return $users;
            }
        }
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "postCoach"})
     * @param Request             $request
     * @param SerializerInterface $serializer
     * @param ValidatorInterface  $validator
     *
     * @return array|\JMS\Serializer\scalar|mixed|object|string
     */
    public function postCoachAction(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {

        $data = json_decode($request->getContent(), true);

        $user = $this->getDoctrine()->getRepository('App:User')->find($data['user']);
        if (!is_object($user)) {
            return 'KO';
        }
        unset($data['user']);

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
        $user = $serializer->deserialize($dataEncode, 'Entity\Coach', 'json');
        $user->setStatut(0);
        $user->setEnabled(1);


        // add advert relation to user
        $user->setUser($user);
        $user->setFirstName($user->getFirstName());
        $user->setLastName($user->getLastName());

        // add translations relation
        /** @var CoachTranslation $translation */
        foreach ($user->getTranslations() as $translation) {
            $translation->setCoach($user);
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

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            throw new HttpException(400, 'Invalid advert: ' . json_encode($errors));
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
     * @Annotations\View(serializerGroups={"Default", "deleteCoach"})
     */
    public function deleteCoachAction($id)
    {

        $user = $this->getDoctrine()->getRepository('App:User')->find($id);
        if (!is_object($user)) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();
        $user->setEnabled(0);
        $em->merge($user);
        $em->flush();

        return "OK";
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "patchCoach"})
     */
    public function patchCoachAction(Request $request, $id)
    {

        $user = $this->getDoctrine()->getRepository('App:User')->find($id);

        $data = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();

        $type = $data['type'];
        $serializer = $this->get('serializer');

        if ($type == 'lang') {
            $user->setLanguages($data['languages']);
        } elseif ($type == 'desc') {
            foreach ($data['translations'] as $translation) {
                $editedTrans = $this->getDoctrine()->getRepository('App:UserTranslation')->findOneBy(array('locale' => $translation['locale'], 'advert' => $user));
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
                $diploma = new Diploma();
                $diploma->setCoach($user);
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
                    $removedSport = $this->getDoctrine()
                        ->getRepository('App:UserSport')
                        ->findOneBy(array('sport' => $sport, 'advert' => $user));
                    if (is_object($removedSport)) {
                        $user->removeSport($removedSport);
                        $removedSport->setCoach(null);
                    }
                } else {
                    $sportExist = $this->getDoctrine()->getRepository('App:Sport')->find($id);
                    $removedSport = $this->getDoctrine()
                        ->getRepository('App:UserSport')
                        ->findOneBy(array('sport' => $sportExist, 'advert' => $user));
                    if (is_object($removedSport)) {
                        $user->removeSport($removedSport);
                        $removedSport->setCoach(null);
                    }
                    $sport['id'] = $id;
                    $user = $this->addSport($sport, $user, $serializer);
                }
            }
        } elseif ($type == 'cities') {
            foreach ($user->getCities() as $city) {
                $user->removeCity($city);
            }
            foreach ($user->getMeetings() as $meeting) {
                $user->removeMeeting($meeting);
                $meeting->setCoach(null);
            }
            $user = $this->addCities($data['cities'], $user, $serializer);
        } elseif ($type == 'cancel') {
            $user->setCancel($data['cancel']);
        } elseif ($type == 'validation') {
            $user->setStatut($data['statut']);
        }
        $validator = $this->get('validator');
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            throw new HttpException(400, 'Invalid advert: ' . $errors);
        }

        $em->persist($user);
        $em->flush();

        //rename image with slug
        if ($type == 5 || $type == 8) {
            $user = $this->renameImages($user, $type);
        }

        $title = $this->get('app.params')->getTitles()[$user->getTitle()];


        return array(
            'email' => $user->getUser()->getEmail(),
            'advert' => $user,
            'title' => $title,
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
                $meeting->setCoach($user);
                $user->addMeeting($meeting);
            }
            $user->addCity($city);
            $city->addCoach($user);
        }
        return $user;
    }

    private function addDiploma($data, $user)
    {
        // create object
        if (is_null($user->getDiploma())) {
            $diploma = new diploma;
            $diploma->setCoach($user);
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
            $folder = $this->getParameter('folder_diploma');
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
        $image->setCoach($user);
        $user->setImage($image);

        $string = base64_decode($data);
        $folder = $this->container->getParameter('folder_image');
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
        $folder = $this->getParameter('folder_image');
        $diploma_folder = $this->getParameter('folder_diploma');
        $slug = $user->getSlug();

        if ($type == 'img') {
            $image = $user->getImage();
            if ($image) {
                $old_path = './' . $folder . "/" . $image->getFileName() . "." . $image->getExt();
                $new_path = './' . $folder . "/" . $slug . "." . $image->getExt();
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
                    $old_path = './' . $diploma_folder . "/" . $diploma->getFileName() . "." . $diploma->getExt();
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
                            $old_path = './' . $folder . "/" . $picture->getFileName() . "." . $picture->getExt();
                            $new_path = './' . $folder . "/" . $slug . "-" . $sport->getSport()->getSlug() . "-" . $loop . "." . $picture->getExt();
                            if (file_exists($old_path)) {
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

            $newSport = $serializer->deserialize(json_encode($data), 'Entity\CoachSport', 'json');
            $newSport->setCoach($user);
            $newSport->setSport($sport);
            $newSport->setOrderNumber(0);
            $user->addSport($newSport);

            // add translations relation
            foreach ($newSport->getTranslations() as $translation) {
                $translation->setCoachSport($newSport);
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
                $image->setFilename($user->getId() . "_" . $sport->getSlug() . "_" . $loop);
                $image->setExt("png");
                $image->setCoachSport($newSport);
                $newSport->addPicture($image);

                $string = base64_decode($picture);
                $folder = $this->container->getParameter('folder_image');
                $fileName = $image->getFilename();
                $fileExt = $image->getExt();

                $this->createFile($string, $fileName, $fileExt, $folder);
                $loop++;
            }

            return $user;
        }
    }
}
