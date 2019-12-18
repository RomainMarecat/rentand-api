<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FamilyController extends FOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"Default", "getFamilies"})
     * @param Request $request
     * @param Paginator $paginator
     * @param EntityManagerInterface $entityManager
     * @return array|object[]
     */
    public function getFamiliesAction(
        Request $request,
        Paginator $paginator,
        EntityManagerInterface $entityManager
    ) {
        $families = $entityManager->getRepository('App:Family')->findAll();
        if (!is_array($families)) {
            throw $this->createNotFoundException();
        }
        $page = $request->query->get('page');
        if (isset($page)) {
            $perPage = $request->query->get('perPage');
            if (!isset($perPage)) {
                $perPage = 20;
            }
            $families = $paginator->paginate($families, $page, $perPage);
        }
        return $families;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getFamily"})
     */
    public function getFamilyAction($id)
    {

        $family = $this->getDoctrine()->getRepository('App:Family')->find($id);
        if (!is_object($family)) {
            throw $this->createNotFoundException();
        }
        return $family;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "postFamily"})
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @return array|\JMS\Serializer\scalar|mixed|object
     */
    public function postFamilyAction(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ) {

        $data = json_decode($request->getContent(), true);
        $dataParent = $data['parent'];
        $dataSports = $data['sports'];
        unset($data['parent']);
        unset($data['sports']);
        $data = json_encode($data);

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
            $parent = $entityManager->getRepository('App:Family')->find($dataParent);
            if (is_object($parent)) {
                $family->setParent($parent);
                $parent->addChild($family);
            }
        }

        // add sports
        foreach ($dataSports as $dataSport) {
            $sport = $entityManager->getRepository('App:Sport')->find($dataSport);
            if (is_object($sport)) {
                $family->addSport($sport);
                $sport->addFamily($family);
            }
        }

        $entityManager->persist($family);
        $entityManager->flush();

        return $family;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "deleteFamily"})
     * @param EntityManagerInterface $entityManager
     * @param $id
     * @return string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteFamilyAction(EntityManagerInterface $entityManager,
                                       $id
    ) {

        $family = $this->getDoctrine()->getRepository('App:Family')->find($id);
        if (!is_object($family)) {
            throw $this->createNotFoundException();
        }

        $entityManager->remove($family);
        $entityManager->flush();

        return "FAMILY DELETED";
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "patchFamily"})
     * @param $id
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return object|null
     */
    public function patchFamilyAction(
        $id,
        Request $request,
        EntityManagerInterface $entityManager
    ) {

        $family = $this->getDoctrine()->getRepository('App:Family')->find($id);
        if (!is_object($family)) {
            throw $this->createNotFoundException();
        }

        $params = json_decode($request->getContent(), true);

        $validator = $this->get('validator');

        foreach ($params['translations'] as $locale => $translation) {
            $editedTranslation = $this->getDoctrine()->getRepository('App:FamilyTranslation')->findOneBy(array('locale' => $locale, 'family' => $family));
            $editedTranslation->setTitle($translation['title']);

            $entityManager->persist($editedTranslation);
        }

        if ($params['parent']) {
            $parent = $this->getDoctrine()->getRepository('App:Family')->find($params['parent']);
            if (!is_object($parent)) {
                throw $this->createNotFoundException();
            }
            $family->setParent($parent);
        }

        foreach ($params['sports'] as $sportId) {
            $sport = $this->getDoctrine()->getRepository('App:Sport')->find($sportId);
            if (is_object($sport)) {
                if (!$family->getSports()->contains($sport)) {
                    $family->addSport($sport);
                    $sport->addFamily($family);
                }
            }
        }

        foreach ($params['sportDel'] as $sportId) {
            $sport = $this->getDoctrine()->getRepository('App:Sport')->find($sportId);
            $family->removeSport($sport);
        }


        $errors = $validator->validate($family);
        if (count($errors) > 0) {
            throw new HttpException(400, 'Invalid family: ' . $errors);
        }

        $entityManager->persist($family);
        $entityManager->flush();

        return $family;
    }
}
