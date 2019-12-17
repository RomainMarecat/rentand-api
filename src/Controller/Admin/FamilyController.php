<?php

namespace App\Controller\Admin;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use Entity\Family;
use Entity\FamilyTranslation;
use FOS\RestBundle\Controller\Annotations;

class FamilyController extends FOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"Default", "getFamilies"})
     */
    public function getFamiliesAction(Request $request)
    {
        $families = $this->getDoctrine()->getManager()->getRepository('AppBundle:Family')->findAll();
        if (!is_array($families)) {
            throw $this->createNotFoundException();
        }
        $page = $request->query->get('page');
        if (isset($page)) {
            $perPage = $request->query->get('perPage');
            $paginator = $this->get('knp_paginator');
            if (!isset($perPage)) {
                $perPage = 20;
            }
            // return $families;
            $results = $paginator->paginate($families, $page, $perPage);
            // return $results;
        }
        return $families;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getFamily"})
     */
    public function getFamilyAction($id)
    {

        $family = $this->getDoctrine()->getRepository('AppBundle:Family')->find($id);
        if (!is_object($family)) {
            throw $this->createNotFoundException();
        }
        return $family;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "postFamily"})
     */
    public function postFamilyAction(Request $request)
    {

        $data = json_decode($request->getContent(), true);
        $dataParent = $data['parent'];
        $dataSports = $data['sports'];
        unset($data['parent']);
        unset($data['sports']);
        $data = json_encode($data);

        $em = $this->getDoctrine()->getManager();

        $serializer = $this->get('serializer');
        $family = $serializer->deserialize($data, 'Entity\Family', 'json');

        // add translations relation
        foreach ($family->getTranslations() as $translation) {
            $translation->setFamily($family);
            if ($translation->getlocale() == 'en') {
                if ($translation->getTitle() != '') {
                    $family->setName($translation->getTitle());
                } else {
                    $family->setName('no_slug');
                }
            }
        }

        // add parent
        if (!is_null($dataParent)) {
            $parent = $this->getDoctrine()->getManager()->getRepository('AppBundle:Family')->find($dataParent);
            if (is_object($parent)) {
                $family->setParent($parent);
                $parent->addChild($family);
            }
        }

        // add sports
        foreach ($dataSports as $dataSport) {
            $sport = $this->getDoctrine()->getManager()->getRepository('AppBundle:Sport')->find($dataSport);
            if (is_object($sport)) {
                $family->addSport($sport);
                $sport->addFamily($family);
            }
        }

        $em->persist($family);
        $em->flush();

        return $family;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "deleteFamily"})
     */
    public function deleteFamilyAction($id)
    {

        $family = $this->getDoctrine()->getRepository('AppBundle:Family')->find($id);
        if (!is_object($family)) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($family);
        $em->flush();

        return "FAMILY DELETED";
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "patchFamily"})
     */
    public function patchFamilyAction($id, Request $request)
    {

        $family = $this->getDoctrine()->getRepository('AppBundle:Family')->find($id);
        if (!is_object($family)) {
            throw $this->createNotFoundException();
        }

        $params = json_decode($request->getContent(), true);

        $em = $this->getDoctrine()->getManager();

        $validator = $this->get('validator');

        foreach ($params['translations'] as $locale => $translation) {
            $editedTranslation = $this->getDoctrine()->getRepository('AppBundle:FamilyTranslation')->findOneBy(array('locale' => $locale, 'family' => $family));
            $editedTranslation->setTitle($translation['title']);

            $em->persist($editedTranslation);
        }

        if ($params['parent']) {
            $parent = $this->getDoctrine()->getRepository('AppBundle:Family')->find($params['parent']);
            if (!is_object($parent)) {
                throw $this->createNotFoundException();
            }
            $family->setParent($parent);
        }

        foreach ($params['sports'] as $sportId) {
            $sport = $this->getDoctrine()->getRepository('AppBundle:Sport')->find($sportId);
            if (is_object($sport)) {
                if (!$family->getSports()->contains($sport)) {
                    $family->addSport($sport);
                    $sport->addFamily($family);
                }
            }
        }

        foreach ($params['sportDel'] as $sportId) {
            $sport = $this->getDoctrine()->getRepository('AppBundle:Sport')->find($sportId);
            $family->removeSport($sport);
        }


        $errors = $validator->validate($family);
        if (count($errors) > 0) {
            throw new HttpException(400, 'Invalid family: '.$errors);
        }

        $em->persist($family);
        $em->flush();

        return $family;
    }
}
