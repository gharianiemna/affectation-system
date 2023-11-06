<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function add(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
        * @return Task[] Returns an array of Task objects
        */
        public function findByDifficulty($difficulty, $dateObj): array
        {
            $from = new \DateTime($dateObj . "00:00:00");
            $to = new \DateTime($dateObj . "23:59:59");
            return $this->createQueryBuilder('t')
                ->andWhere('t.difficulty = :difficulty')
                ->andWhere('t.user IS NULL')
                ->andWhere('t.startDate BETWEEN :from AND :to')
                ->setParameter('difficulty', $difficulty)
                ->setParameter('from', $from)
                ->setParameter('to', $to)
                ->orderBy('t.id', 'ASC')
                ->getQuery()
                ->getResult();
        }
        
        
    /**
     * @return Task[] Returns an array of Task objects for the current day.
     */
    public function findByUserToday($user, $dateObj): array
    {
        $from = new \DateTime($dateObj . "00:00:00");
        $to = new \DateTime($dateObj . "23:59:59");
        
        return $this->createQueryBuilder('t')
            ->andWhere('t.user = :user')
            ->andWhere('t.startDate BETWEEN :from AND :to')
            ->setParameter('user', $user->getId())
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult();
    }


    public function findByUserNow($user, $dateObj): array
    {
        $time = new \DateTime($dateObj );
        return $this->createQueryBuilder('t')
            ->andWhere('t.user = :user')
            ->andWhere('t.startDate = :from ')
            ->setParameter('user', $user->getId())
            ->setParameter('from', $time)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

//    public function findOneBySomeField($value): ?Task
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
