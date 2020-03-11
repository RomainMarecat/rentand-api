<?php

namespace App\Controller\Front;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;

class ProductController extends AbstractFOSRestController
{
    /**
     * Find cities
     *
     * @Annotations\View(serializerGroups={"product"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/products/{id}")
     * @param EntityManagerInterface $entityManager
     * @param string $id
     * @return mixed
     */
    public function getProductAction(EntityManagerInterface $entityManager, string $id)
    {
        return $entityManager->getRepository(Product::class)->getProduct($id);
    }
}
