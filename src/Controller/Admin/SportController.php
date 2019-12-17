<?php

namespace App\Controller\Admin;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use Entity\Sport;
use Entity\SportTranslation;
use FOS\RestBundle\Controller\Annotations;

class SportController extends FOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"Default", "getSports"})
     */
    public function getSportsAction(Request $request)
    {
        $sports = $this->getDoctrine()->getManager()->getRepository('AppBundle:Sport')->findByLevel(0);
        if (!is_array($sports)) {
            throw $this->createNotFoundException();
        }
        $page = $request->query->get('page');
        if (isset($page)) {
            $perPage = $request->query->get('perPage');
            $paginator = $this->get('knp_paginator');
            if (!isset($perPage)) {
                $perPage = 20;
            }
            $results = $paginator->paginate($sports, $page, $perPage);
            return $results;
        }
        return $sports;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getSport"})
     */
    public function getSportAction($id)
    {

        $sport = $this->getDoctrine()->getRepository('AppBundle:Sport')->find($id);
        if (!is_object($sport)) {
            throw $this->createNotFoundException();
        }
        return $sport;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "postSport"})
     */
    public function postSportAction(Request $request)
    {

        $data = $request->getContent();

        $em = $this->getDoctrine()->getManager();

        $serializer = $this->get('serializer');
        $sport = $serializer->deserialize($data, 'Entity\Sport', 'json');
        $sport->setLevel(0);

        // add translations relation
        foreach ($sport->getTranslations() as $translation) {
            $translation->setSport($sport);
            if ($translation->getlocale() == 'en') {
                $sport->setName($translation->getTitle());
            }
        }

        // add specialities
        $specialities = json_decode($data, true)['specialities'];
        foreach ($specialities as $speciality) {
            $newSpe = new Sport;
            $newSpe->setLevel(1);
            $newSpe->setParent($sport);
            $sport->addChild($newSpe);
            foreach ($speciality['translations'] as $locale => $translation) {
                $newSpeTrans = new SportTranslation;
                $newSpeTrans->setSport($newSpe);
                $newSpe->addTranslation($newSpeTrans);
                $newSpeTrans->setTitle($translation['title']);
                $newSpeTrans->setLocale($locale);
                if ($newSpeTrans->getlocale() == 'en') {
                    $newSpe->setName($newSpeTrans->getTitle());
                }
            }
        }

        $em->persist($sport);
        $em->flush();

        return $sport;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "deleteSport"})
     */
    public function deleteSportAction($id)
    {

        $sport = $this->getDoctrine()->getRepository('AppBundle:Sport')->find($id);

        if (!is_object($sport)) {
            throw $this->createNotFoundException();
        }
        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($sport);
        $em->flush();

        return "SPORT DELETED";
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "patchSport"})
     */
    public function patchSportAction($id, Request $request)
    {

        $sport = $this->getDoctrine()->getRepository('AppBundle:Sport')->find($id);

        $params = json_decode($request->getContent(), true);

        $em = $this->getDoctrine()->getManager();

        $validator = $this->get('validator');

        foreach ($params['translations'] as $locale => $translation) {
            $editedTranslation = $this->getDoctrine()->getRepository('AppBundle:SportTranslation')->findOneBy(array('locale' => $locale, 'sport' => $sport));
            $editedTranslation->setTitle($translation['title']);
            $editedTranslation->setSearch($translation['search']);

            $em->persist($editedTranslation);
        }

        // edit specialities
        $specialities = $params['specialities'];
        foreach ($specialities as $speciality) {
            if (!isset($speciality['id'])) {
                $newSpe = new Sport;
                $newSpe->setLevel(1);
                $newSpe->setParent($sport);
                $sport->addChild($newSpe);

                foreach ($speciality['translations'] as $locale => $translation) {
                    $newSpeTrans = new SportTranslation;
                    $newSpeTrans->setSport($newSpe);
                    $newSpe->addTranslation($newSpeTrans);
                    $newSpeTrans->setTitle($translation['title']);
                    $newSpeTrans->setLocale($locale);
                    if ($newSpeTrans->getlocale() == 'en') {
                        $newSpe->setName($newSpeTrans->getTitle());
                    }
                }
            } else {
                $editedSpe = $this->getDoctrine()->getRepository('AppBundle:Sport')->find($speciality['id']);
                foreach ($speciality['translations'] as $locale => $newTranslation) {
                    $oldTrans = $editedSpe->getTranslations();
                    foreach ($oldTrans as $translation) {
                        if ($translation->getLocale() == $locale) {
                            $translation->setTitle($newTranslation['title']);
                        }
                    }
                    if ($newTranslation['locale'] == 'en') {
                        $editedSpe->setName($newTranslation['title']);
                    }
                }
            }
        }
        foreach ($params['speDel'] as $specialityId) {
            $speciality = $this->getDoctrine()->getRepository('AppBundle:Sport')->find($specialityId);
            $em->remove($speciality);
        }


        $errors = $validator->validate($sport);
        if (count($errors) > 0) {
            throw new HttpException(400, 'Invalid sport: '.$errors);
        }

        $em->persist($sport);
        $em->flush();

        return $sport;
    }
}
