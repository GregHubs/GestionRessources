<?php

namespace BirdOffice\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Types\Type;


class DayRepository extends \Doctrine\ORM\EntityRepository
{
    public function findByMonth($month, $user)
    {
        $db = $this->getEntityManager()
            ->createQueryBuilder()
            ->from('UserBundle:Day', 'p')
            ->addSelect('p.id')
            ->where("DATE_FORMAT(p.startDate, '%m') = :month")
          //  ->andWhere('p.user Like :user')
            ->setParameter('month', $month);
         //   ->setParameter('user', '%"'.$user.'"%');

        return $db->getQuery()->getResult();

    }


}