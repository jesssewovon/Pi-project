<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    // /**
    //  * @return Produit[] Returns an array of Produit objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Produit
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findProduits($mot, $categorie, $dateDeb, $dateFin): ?array
    {
        $builder = $this->createQueryBuilder('p');
        if ($mot != '') {
            $builder->andWhere('p.nom like :val or p.description like :val')
            ->setParameter('val', '%'.$mot.'%');
        }
        if (!is_null($categorie)) {
            $builder->andWhere('p.categorie = :val')
            ->setParameter('val', $categorie);
        }
        if (!is_null($dateDeb)) {
            $builder->andWhere('p.datePublication >= :dateDeb')
            ->setParameter('dateDeb', $dateDeb->format('Y-m-d').' 00:00:00');
        }
        if (!is_null($dateFin)) {
            $builder->andWhere('p.datePublication <= :dateFin')
            ->setParameter('dateFin', $dateFin->format('Y-m-d').' 23:59:59');
        }
        return $builder->getQuery()->getResult();
        ;
    }

    public function finding($num_page, $mot, $categorie, $dateDeb, $dateFin): ?array
    {
        $prod_per_page = 40;

        $builder = $this->createQueryBuilder('p')
            ->select('p.id, p.nom, p.prix, p.photo, p.description, p.datePublication, categorie.libelle, TIMESTAMPDIFF(YEAR, p.datePublication,  CURRENT_TIMESTAMP()) as annee, TIMESTAMPDIFF(MONTH, p.datePublication,  CURRENT_TIMESTAMP()) as mois, TIMESTAMPDIFF(DAY, p.datePublication,  CURRENT_TIMESTAMP()) as jour, TIMESTAMPDIFF(HOUR, p.datePublication,  CURRENT_TIMESTAMP()) as heure, TIMESTAMPDIFF(MINUTE, p.datePublication,  CURRENT_TIMESTAMP()) as minute, TIMESTAMPDIFF(SECOND, p.datePublication,  CURRENT_TIMESTAMP()) as seconde')
            // ->orderBy('p.id', 'DESC')
            ->orderBy('p.datePublication', 'DESC')
            ->innerJoin('p.categorie', 'categorie');
        if ($mot != '') {
            $builder->andWhere('p.nom like :mot or p.description like :mot')
            ->setParameter('mot', '%'.$mot.'%');
        }
        if (!is_null($categorie)) {
            $builder->andWhere('p.categorie = :cat')
            ->setParameter('cat', $categorie);
        }
        if (!is_null($dateDeb)) {
            $builder->andWhere('p.datePublication >= :dateDeb')
            ->setParameter('dateDeb', $dateDeb->format('Y-m-d').' 00:00:00');
        }
        if (!is_null($dateFin)) {
            $builder->andWhere('p.datePublication <= :dateFin')
            ->setParameter('dateFin', $dateFin->format('Y-m-d').' 23:59:59');
        }

        $all = $builder->getQuery()->getResult();
        $resultat = $builder->setMaxResults($prod_per_page)
            ->setFirstResult(($num_page - 1) * $prod_per_page)
            ->getQuery()
            ->getResult()
        ;
        return ['all' =>$all, 'resultat' => $resultat]
        ;
    }

    public function rech($num_page): ?array
    {
        return $this->createQueryBuilder('p')
            ->select('p.nom, p.prix, p.photo, p.description, p.datePublication, categorie.libelle')
            ->orderBy('p.id', 'DESC')
            ->innerJoin('p.categorie', 'categorie')
            ->setMaxResults(6)
            ->setFirstResult(($num_page - 1) * 6)
            ->getQuery()
            ->getResult()
        ;
    }
}
