<?php

namespace App\Controller\Front;

use App\Entity\Comment;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;

class CommentController extends AbstractFOSRestController
{
    /**
     * Find comments by status
     *
     * @Annotations\View(serializerGroups={"comments"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/comments/status/{status}")
     * @param $status
     * @return Comment[]
     */
    public function getCommentsByStatusAction(string $status)
    {
        return $this->getDoctrine()
            ->getRepository(Comment::class)
            ->findCommentsByStatus($status);
    }
}
