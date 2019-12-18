<?php

namespace App\Controller\Admin;

use App\Entity\AdvertTranslation;
use Doctrine\ORM\EntityManagerInterface;
use Entity\Sport;
use Entity\SportTranslation;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SportController extends FOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"Default", "getSports"})
     * @param Request $request
     * @param Paginator $paginator
     * @param EntityManagerInterface $entityManager
     * @return array|\Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function getSportsAction(Request $request,
                                    Paginator $paginator,
                                    EntityManagerInterface $entityManager
    ) {
        $sports = $entityManager->getRepository('App:Sport')->findByLevel(0);
        if (!is_array($sports)) {
            throw $this->createNotFoundException();
        }
        $page = $request->query->get('page');
        if (isset($page)) {
            $perPage = $request->query->get('perPage');
            if (!isset($perPage)) {
                $perPage = 20;
            }
            return $paginator->paginate($sports, $page, $perPage);
        }
        return $sports;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getSport"})
     * @param $id
     * @param EntityManagerInterface $entityManager
     * @return object|null
     */
    public function getSportAction($id, EntityManagerInterface $entityManager)
    {

        $sport = $entityManager->getRepository('App:Sport')->find($id);
        if (!is_object($sport)) {
            throw $this->createNotFoundException();
        }
        return $sport;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "postSport"})
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @return array|\JMS\Serializer\scalar|mixed|object
     */
    public function postSportAction(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ) {

        $data = $request->getContent();

        $sport = $serializer->deserialize($data, 'Entity\Sport', 'json');
        $sport->setLevel(0);

        // add translations relation
        /** @var SportTranslation $translation */
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

        $entityManager->persist($sport);
        $entityManager->flush();

        return $sport;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "deleteSport"})
     * @param $id
     * @param EntityManagerInterface $entityManager
     * @return string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteSportAction($id, EntityManagerInterface $entityManager)
    {

        $sport = $this->getDoctrine()->getRepository('App:Sport')->find($id);

        if (!is_object($sport)) {
            throw $this->createNotFoundException();
        }
        $entityManager->remove($sport);
        $entityManager->flush();

        return "SPORT DELETED";
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "patchSport"})
     * @param $id
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @return object|null
     */
    public function patchSportAction(
        $id,
        Request $request,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ) {

        $sport = $this->getDoctrine()->getRepository('App:Sport')->find($id);

        $params = json_decode($request->getContent(), true);

        foreach ($params['translations'] as $locale => $translation) {
            $editedTranslation = $this->getDoctrine()->getRepository('App:SportTranslation')->findOneBy(array('locale' => $locale, 'sport' => $sport));
            $editedTranslation->setTitle($translation['title']);
            $editedTranslation->setSearch($translation['search']);

            $entityManager->persist($editedTranslation);
        }

        // edit specialities
        $specialities = $params['specialities'];
        foreach ($specialities as $speciality) {
            if (!isset($speciality['id'])) {
                $newSpe = new Sport();
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
                $editedSpe = $this->getDoctrine()->getRepository('App:Sport')->find($speciality['id']);
                foreach ($speciality['translations'] as $locale => $newTranslation) {
                    $oldTrans = $editedSpe->getTranslations();
                    /** @var AdvertTranslation $translation */
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
            $speciality = $this->getDoctrine()->getRepository('App:Sport')->find($specialityId);
            $entityManager->remove($speciality);
        }


        $errors = $validator->validate($sport);
        if (count($errors) > 0) {
            throw new HttpException(400, 'Invalid sport: ' . json_encode($errors));
        }

        $entityManager->persist($sport);
        $entityManager->flush();

        return $sport;
    }
}
