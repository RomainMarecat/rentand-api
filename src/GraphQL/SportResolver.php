<?php

namespace App\GraphQL\Resolver;

use App\Entity\Sport;
use Doctrine\ORM\EntityManagerInterface;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class SportResolver implements ResolverInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(ResolveInfo $info, $value, Argument $args)
    {
        $method = $info->fieldName;
        return $this->$method($value, $args);
    }

    public function resolve(string $id): Sport
    {
        return $this->entityManager->find(Sport::class, $id);
    }

    public function name(Sport $sport): string
    {
        return $sport->getName();
    }

    public function slug(Sport $sport): string
    {
        return $sport->getSlug();
    }

    public function id(Sport $sport): string
    {
        return $sport->getId();
    }
}
