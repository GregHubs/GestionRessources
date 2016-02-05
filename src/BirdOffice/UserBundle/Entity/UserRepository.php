<?php

namespace BirdOffice\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;

class UserRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param string $role
     *
     * @return array
     */
    public function findByRole($role)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
            ->from('UserBundle:User', 'u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"'.$role.'"%');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param string $role
     *
     * @return array
     */
    public function findByManager($associate)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
            ->from('UserBundle:User', 'u')
            ->where('u.managedBy LIKE :manager')
            ->setParameter('manager', '%"'.$associate.'"%');

        return $qb->getQuery()->getResult();
    }
}