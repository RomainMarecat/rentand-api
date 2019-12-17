<?php

namespace App\Controller\Admin;

use Entity\Diploma;
use Entity\Image;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @Annotations\Prefix("admin")
 * @Annotations\NamePrefix("_admin")
 */
class AdvertController extends FOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"adminGetAdverts"})
     * @Annotations\Get("/adverts")
     */
    public function getAdvertsAction(Request $request)
    {
        return $this->get('admin.manager.advert')->adminCget();
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getAdvert"})
     */
    public function getAdvertAction($id)
    {

        $advert = $this->getDoctrine()->getRepository('AppBundle:Advert')->find($id);

        $urls = array();

        if ($advert->getImage()) {
            $imgUrl = $_SERVER['SERVER_NAME'] . "/uploads/images/" . $advert->getImage()->getFilename() . "." . $advert->getImage()->getExt();
            $urls['mainImg'] = $imgUrl;
        } else {
            $imgUrl = null;
        }
        if ($advert->getDiploma()) {
            $diplomaUrl = $_SERVER['SERVER_NAME'] . "/uploads/diplomas/" . $advert->getDiploma()->getFilename() . "." . $advert->getDiploma()->getExt();
            $urls['diploma'] = $imgUrl;
        } else {
            $diplomaUrl = null;
        }
        foreach ($advert->getSports() as $sport) {
            $sportId = $sport->getSport()->getId();
            $urls[$sportId] = array();
            foreach ($sport->getPictures() as $picture) {
                $picUrl = $_SERVER['SERVER_NAME'] . "/uploads/images/" . $picture->getFilename() . "." . $picture->getExt();
                array_push($urls[$sportId], $picUrl);
            }
        }

        if (!is_object($advert)) {
            throw $this->createNotFoundException();
        }
        return array(
            'advert' => $advert,
            'urls' => $urls,
        );
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getUserAdvert"})
     */
    public function getUserAdvertAction(Request $request, $id)
    {

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        $adverts = $this->getDoctrine()->getRepository('AppBundle:Advert')->findbyUser($user);
        if (!is_object($adverts)) {
            throw $this->createNotFoundException();
        } else {
            $page = $request->query->get('page');
            if (isset($page)) {
                $perPage = $request->query->get('perPage');
                $paginator = $this->get('knp_paginator');
                if (!isset($perPage)) {
                    $perPage = 20;
                }
                $results = $paginator->paginate($adverts, $page, $perPage);
                return $results;
            } else {
                return $adverts;
            }
        }
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getStructure"})
     */
    public function getStructureAdvertAction(Request $request, $id)
    {

        $structure = $this->getDoctrine()->getRepository('AppBundle:Structure')->find($id);
        $adverts = $this->getDoctrine()->getRepository('AppBundle:Advert')->findbyStructure($structure);
        if (!is_object($adverts)) {
            throw $this->createNotFoundException();
        } else {
            $page = $request->query->get('page');
            if (isset($page)) {
                $perPage = $request->query->get('perPage');
                $paginator = $this->get('knp_paginator');
                if (!isset($perPage)) {
                    $perPage = 20;
                }
                $results = $paginator->paginate($adverts, $page, $perPage);
                return $results;
            } else {
                return $adverts;
            }
        }
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "postAdvert"})
     */
    public function postAdvertAction(Request $request)
    {

        $data = json_decode($request->getContent(), true);

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($data['user']);
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
        $serializer = $this->get('serializer');
        $advert = $serializer->deserialize($dataEncode, 'Entity\Advert', 'json');
        $advert->setStatut(0);
        $advert->setEnabled(1);


        // add advert relation to user
        $advert->setUser($user);
        $advert->setFirstName($user->getFirstName());
        $advert->setLastName($user->getLastName());

        // add translations relation
        foreach ($advert->getTranslations() as $translation) {
            $translation->setAdvert($advert);
        }

        // add cities to advert depending if city already exists or not
        $advert = $this->addCities($dataCities, $advert, $serializer);

        //add diploma to advert
        $advert = $this->addDiploma($dataDiploma, $advert);

        //add image to advert
        if ($dataImage) {
            $advert = $this->addImage($dataImage, $advert);
        }

        // add sports to advert
        foreach ($dataSports as $sport) {
            $advert = $this->addSport($sport, $advert, $serializer);
        }

        $validator = $this->get('validator');
        $errors = $validator->validate($advert);
        if (count($errors) > 0) {
            throw new HttpException(400, 'Invalid advert: ' . $errors);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($advert);
        $em->flush();

        //rename image with slug
        $advert = $this->renameImages($advert, 'img');
        $advert = $this->renameImages($advert, 'diploma');
        $advert = $this->renameImages($advert, 'sports');

        return $advert;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "deleteAdvert"})
     */
    public function deleteAdvertAction($id)
    {

        $advert = $this->getDoctrine()->getRepository('AppBundle:Advert')->find($id);
        if (!is_object($advert)) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();
        $advert->setEnabled(0);
        $em->merge($advert);
        $em->flush();

        return "OK";
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "patchAdvert"})
     */
    public function patchAdvertAction(Request $request, $id)
    {

        $advert = $this->getDoctrine()->getRepository('AppBundle:Advert')->find($id);

        $data = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();

        $type = $data['type'];
        $serializer = $this->get('serializer');

        if ($type == 'lang') {
            $advert->setLanguages($data['languages']);
        } elseif ($type == 'desc') {
            foreach ($data['translations'] as $translation) {
                $editedTrans = $this->getDoctrine()->getRepository('AppBundle:AdvertTranslation')->findOneBy(array('locale' => $translation['locale'], 'advert' => $advert));
                $editedTrans->setDescription1($translation['description1']);
                $editedTrans->setDescription2($translation['description2']);
                $editedTrans->setDescription3($translation['description3']);
                $em->persist($editedTrans);
            }
        } elseif ($type == 'img') {
            if ($advert->getImage()) {
                $em->remove($advert->getImage());
                $em->flush();
            }
            $advert = $this->addImage($data['image'], $advert);
        } elseif ($type == 'title') {
            $advert->setTitle($data['title']);
        } elseif ($type == 'diploma') {
            if ($advert->getDiploma() == null) {
                $diploma = new diploma;
                $diploma->setAdvert($advert);
                $advert->setDiploma($diploma);
            }
            $advert->getDiploma()->setTitle($data['diploma']);
        } elseif ($type == 'proCard') {
            $advert = $this->addDiploma($data, $advert);
        } elseif ($type == 'passions') {
            $advert->setPassions($data['passions']);
        } elseif ($type == 'sports') {
            foreach ($data['sports'] as $id => $sport) {
                if ($sport['id'] == 'remove') {
                    $sport = $this->getDoctrine()->getRepository('AppBundle:Sport')->find($id);
                    $removedSport = $this->getDoctrine()
                        ->getRepository('AppBundle:AdvertSport')
                        ->findOneBy(array('sport' => $sport, 'advert' => $advert));
                    if (is_object($removedSport)) {
                        $advert->removeSport($removedSport);
                        $removedSport->setAdvert(null);
                    }
                } else {
                    $sportExist = $this->getDoctrine()->getRepository('AppBundle:Sport')->find($id);
                    $removedSport = $this->getDoctrine()
                        ->getRepository('AppBundle:AdvertSport')
                        ->findOneBy(array('sport' => $sportExist, 'advert' => $advert));
                    if (is_object($removedSport)) {
                        $advert->removeSport($removedSport);
                        $removedSport->setAdvert(null);
                    }
                    $sport['id'] = $id;
                    $advert = $this->addSport($sport, $advert, $serializer);
                }
            }
        } elseif ($type == 'cities') {
            foreach ($advert->getCities() as $city) {
                $advert->removeCity($city);
            }
            foreach ($advert->getMeetings() as $meeting) {
                $advert->removeMeeting($meeting);
                $meeting->setAdvert(null);
            }
            $advert = $this->addCities($data['cities'], $advert, $serializer);
        } elseif ($type == 'cancel') {
            $advert->setCancel($data['cancel']);
        } elseif ($type == 'validation') {
            $advert->setStatut($data['statut']);
        }
        $validator = $this->get('validator');
        $errors = $validator->validate($advert);
        if (count($errors) > 0) {
            throw new HttpException(400, 'Invalid advert: ' . $errors);
        }

        $em->persist($advert);
        $em->flush();

        //rename image with slug
        if ($type == 5 || $type == 8) {
            $advert = $this->renameImages($advert, $type);
        }

        $title = $this->get('app.params')->getTitles()[$advert->getTitle()];


        return array(
            'email' => $advert->getUser()->getEmail(),
            'advert' => $advert,
            'title' => $title,
        );
    }

    private function addCities($data, $advert, $serializer)
    {
        foreach ($data as $dataCity) {
            $dataMeetings = $dataCity['meetings'];
            unset($dataCity['meetings']);
            $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneByGoogleId($dataCity['googleId']);
            if (!is_object($city)) {
                $city = $serializer->deserialize(json_encode($dataCity), 'Entity\City', 'json');
                $em = $this->getDoctrine()->getManager();
                $em->persist($city);
            }
            foreach ($dataMeetings as $dataMeeting) {
                $meeting = $serializer->deserialize(json_encode($dataMeeting), 'Entity\Meeting', 'json');
                $meeting->setCity($city);
                $meeting->setAdvert($advert);
                $advert->addMeeting($meeting);
            }
            $advert->addCity($city);
            $city->addAdvert($advert);
        }
        return $advert;
    }

    private function addDiploma($data, $advert)
    {
        // create object
        if (is_null($advert->getDiploma())) {
            $diploma = new diploma;
            $diploma->setAdvert($advert);
            $advert->setDiploma($diploma);
        } else {
            $diploma = $advert->getDiploma();
        }

        // Add Title
        if (isset($data['title'])) {
            $diploma->setTitle($data['title']);
        }

        // add pro card
        if (isset($data['file_name'])) {
            $ext = explode(".", $data['file_name'])[1];
            $diploma->setFilename($advert->getUser()->getId());
            $diploma->setExt($ext);
            $string = base64_decode(urldecode($data['file']));
            $folder = $this->getParameter('folder_diploma');
            $fileName = $diploma->getFilename();
            $fileExt = $diploma->getExt();

            $this->createFile($string, $fileName, $fileExt, $folder);
        }


        return $advert;
    }

    private function addImage($data, $advert)
    {
        $image = new image;
        $image->setFilename($advert->getUser()->getId());
        $image->setExt("png");
        $image->setAdvert($advert);
        $advert->setImage($image);

        $string = base64_decode($data);
        $folder = $this->container->getParameter('folder_image');
        $fileName = $image->getFilename();
        $fileExt = $image->getExt();

        $this->createFile($string, $fileName, $fileExt, $folder);

        return $advert;
    }

    private function createFile($string, $fileName, $fileExt, $folder)
    {

        // create file
        $pathFile = './' . $folder . "/" . $fileName . "." . $fileExt;
        file_put_contents($pathFile, $string);
        return true;
    }

    private function renameImages($advert, $type = null)
    {

        $em = $this->getDoctrine()->getManager();
        $folder = $this->getParameter('folder_image');
        $diploma_folder = $this->getParameter('folder_diploma');
        $slug = $advert->getSlug();

        if ($type == 'img') {
            $image = $advert->getImage();
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
            $diploma = $advert->getDiploma();
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
            foreach ($advert->getSports() as $sport) {
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

        return $advert;
    }


    private function addSport($data, $advert, $serializer)
    {
        $sport = $this->getDoctrine()->getRepository('AppBundle:Sport')->find($data['id']);

        if (is_object($sport)) {
            unset($data['id']);
            $dataPictures = $data['pictures'];
            unset($data['pictures']);
            $dataSpecialities = $data['specialities'];
            unset($data['specialities']);

            $newSport = $serializer->deserialize(json_encode($data), 'Entity\AdvertSport', 'json');
            $newSport->setAdvert($advert);
            $newSport->setSport($sport);
            $newSport->setOrderNumber(0);
            $advert->addSport($newSport);

            // add translations relation
            foreach ($newSport->getTranslations() as $translation) {
                $translation->setAdvertSport($newSport);
            }

            foreach ($dataSpecialities as $newSpeciality) {
                $speciality = $this->getDoctrine()->getRepository('AppBundle:Sport')->find($newSpeciality);
                if (is_object($speciality)) {
                    $newSport->addSpeciality($speciality);
                }
            }

            $loop = 1;
            foreach ($dataPictures as $picture) {
                $image = new image;
                $image->setFilename($advert->getId() . "_" . $sport->getSlug() . "_" . $loop);
                $image->setExt("png");
                $image->setAdvertSport($newSport);
                $newSport->addPicture($image);

                $string = base64_decode($picture);
                $folder = $this->container->getParameter('folder_image');
                $fileName = $image->getFilename();
                $fileExt = $image->getExt();

                $this->createFile($string, $fileName, $fileExt, $folder);
                $loop++;
            }

            return $advert;
        }
    }
}
