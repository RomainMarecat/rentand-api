<?php

namespace App\Controller\Admin;

use App\Manager\Admin\CommentManager;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\Paginator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;


class CommentController extends FOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"adminGetComments"})
     * @Annotations\Get("/comments")
     * @param Request $request
     * @param CommentManager $commentManager
     * @return JsonResponse
     */
    public function getCommentsAction(Request $request, CommentManager $commentManager)
    {
        return $commentManager->adminCget();
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getComment"})
     */
    public function getCommentAction($id)
    {

        $comment = $this->getDoctrine()->getRepository('App:Comment')->find($id);
        if (!is_object($comment)) {
            throw $this->createNotFoundException();
        }
        return $comment;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getAdvertComment"})
     * @param Request $request
     * @param Paginator $paginator
     * @param $id
     * @return
     */
    public function getAdvertCommentAction(
        Request $request,
        Paginator $paginator,
        $id
    ) {

        $user = $this->getDoctrine()->getRepository('App:Advert')->find($id);
        $comments = $this->getDoctrine()->getRepository('App:Comment')->findbyUser($user);
        if (!is_object($comments)) {
            throw $this->createNotFoundException();
        } else {
            $page = $request->query->get('page');
            if (isset($page)) {
                $perPage = $request->query->get('perPage');
                if (!isset($perPage)) {
                    $perPage = 20;
                }
                return $paginator->paginate($comments, $page, $perPage);
            } else {
                return $comments;
            }
        }
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "postComment"})
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return
     */
    public function postCommentAction(
        Request $request,
        SerializerInterface $serializer
    ) {

        $data = json_decode($request->getContent(), true);
        $dataUser = $data['user'];
        unset($data['user']);
        $dataAdvert = $data['advert'];
        unset($data['advert']);

        $dataEncode = json_encode($data);

        $em = $this->getDoctrine()->getManager();

        $comment = $serializer->deserialize($dataEncode, 'Entity\Comment', 'json');

        // add user
        $user = $this->getDoctrine()->getRepository('App:User')->find($dataUser);
        $comment->setUser($user);
        // add advert
        $user = $this->getDoctrine()->getRepository('App:Advert')->find($dataAdvert);
        $comment->setAdvert($user);

        $em->persist($comment);
        $em->flush();

        return $comment;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "deleteComment"})
     * @param EntityManagerInterface $entityManager
     * @param $id
     * @return string
     */
    public function deleteCommentAction(
        EntityManagerInterface $entityManager,
        $id
    ) {

        $comment = $this->getDoctrine()->getRepository('App:Comment')->find($id);
        if (!is_object($comment)) {
            throw $this->createNotFoundException();
        }
        $entityManager->remove($comment);
        $entityManager->flush();

        return "COMMENT DELETED";
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "patchComments"})
     * @param $id
     * @param Request $request
     * @return object|null
     */
    public function patchCommentsAction(
        $id,
        Request $request
    ) {

        $comment = $this->getDoctrine()->getRepository('App:Comment')->find($id);

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
