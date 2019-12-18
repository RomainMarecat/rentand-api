<?php

namespace App\Controller\Front;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;

class CommentController extends AbstractFOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"Default", "getCommentsByStatus"})
     * @Annotations\Get("/comments/status/{status}")
     */
    public function getCommentsByStatusAction($status)
    {
        $comments = $this->getDoctrine()
            ->getRepository('App:Comment')
            ->findCommentsByStatus($status);

        return array('comments' => $comments);
    }

}
