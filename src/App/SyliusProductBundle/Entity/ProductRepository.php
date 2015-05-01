<?php


namespace App\SyliusProductBundle\Entity;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository as BaseProductRepository;


class ProductRepository extends BaseProductRepository
{
    
    public function findUniqueProducts()
    {
        $queryBuilder = parent::getCollectionQueryBuilder()  
            ->join('product.variants', 'variant')
            ->add('groupBy', 'product.name, variant.price');
        $result = $queryBuilder
            ->getQuery()
            ->getResult();

        return $result;
    }
    
    public function findByGroupId($groupId)
    {
        $queryBuilder = parent::getCollectionQueryBuilder() 
            ->andWhere('product.group = :id')
                ->setParameter('id', $groupId);
        $result = $queryBuilder
            ->getQuery()
            ->getResult();

        return $result;
    }
    
    public function findActiveProducts()
    {
        $queryBuilder = parent::getCollectionQueryBuilder() 
            ->andWhere('product.deletedAt is null');
        $result = $queryBuilder
            ->getQuery()
            ->getResult();

        return $result;
    }
    
    public function findActiveProductsIterate()
    {
        $queryBuilder = parent::getCollectionQueryBuilder() 
            ->andWhere('product.deletedAt is null');
        return $queryBuilder->getQuery()->iterate();
    }
    
    public function findDeletedProducts()
    {
        $queryBuilder = parent::getCollectionQueryBuilder() 
            ->andWhere('product.deletedAt is not null');
        $result = $queryBuilder
            ->getQuery()
            ->getResult();

        return $result;
    }
}
