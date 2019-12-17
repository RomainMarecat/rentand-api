<?php

namespace App\Controller\Front;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations;

class CommentController extends FOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"Default", "getCommentsByStatus"})
     * @Annotations\Get("/comments/status/{status}")
     */
    public function getCommentsByStatusAction($status)
    {
        $comments = $this->getDoctrine()
            ->getRepository('AppBundle:Comment')
            ->findCommentsByStatus($status);

        return array('comments' => $comments);
    }

}
