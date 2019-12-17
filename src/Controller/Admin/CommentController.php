<?php

namespace App\Controller\Admin;

use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;


class CommentController extends FOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"adminGetComments"})
     * @Annotations\Get("/comments")
     */
    public function getCommentsAction(Request $request)
    {
        return $this->get('admin.manager.comment')->adminCget();
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getComment"})
     */
    public function getCommentAction($id)
    {

        $comment = $this->getDoctrine()->getRepository('AppBundle:Comment')->find($id);
        if (!is_object($comment)) {
            throw $this->createNotFoundException();
        }
        return $comment;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getAdvertComment"})
     */
    public function getAdvertCommentAction($id)
    {

        $advert = $this->getDoctrine()->getRepository('AppBundle:Advert')->find($id);
        $comments = $this->getDoctrine()->getRepository('AppBundle:Comment')->findbyUser($advert);
        if (!is_object($comments)) {
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
     * @Annotations\View(serializerGroups={"Default", "postComment"})
     */
    public function postCommentAction(Request $request)
    {

        $data = json_decode($request->getContent(), true);
        $dataUser = $data['user'];
        unset($data['user']);
        $dataAdvert = $data['advert'];
        unset($data['advert']);

        $dataEncode = json_encode($data);

        $em = $this->getDoctrine()->getManager();

        $serializer = $this->get('serializer');
        $comment = $serializer->deserialize($dataEncode, 'Entity\Comment', 'json');

        // add user
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($dataUser);
        $comment->setUser($user);
        // add advert
        $advert = $this->getDoctrine()->getRepository('AppBundle:Advert')->find($dataAdvert);
        $comment->setAdvert($advert);

        $em->persist($comment);
        $em->flush();

        return $comment;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "deleteComment"})
     */
    public function deleteCommentAction($id)
    {

        $comment = $this->getDoctrine()->getRepository('AppBundle:Comment')->find($id);
        if (!is_object($comment)) {
            throw $this->createNotFoundException();
        }
        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($comment);
        $em->flush();

        return "COMMENT DELETED";
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "patchComments"})
     */
    public function patchCommentsAction($id, Request $request)
    {

        $comment = $this->getDoctrine()->getRepository('AppBundle:Comment')->find($id);

        if (!is_object($comment)) {
            throw $this->createNotFoundException();
        }

        $params = json_decode($request->getContent(), true);

        if (isset($params['grade'])) {
            $comment->setGrade($params['grade']);
        }
        if (isset($params['comment'])) {
            $comment->setComment($params['comment']);
        }
        if (isset($params['validated'])) {
            $comment->setValidated($params['validated']);
        }
        if (isset($params['status'])) {
            $comment->setStatus($params['status']);
        }

        $validator = $this->get('validator');
        $errors = $validator->validate($comment);
        if (count($errors) > 0) {
            throw new HttpException(400, 'Invalid comment: ' . $errors);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        $em->flush();

        return $comment;
    }
}
