<?php

namespace AppBundle\Repository;

/**
 * IntervencionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class IntervencionRepository extends \Doctrine\ORM\EntityRepository
{
    public function getQb()
    {
        return $this->createQueryBuilder('interv');
    }

    public function getUltimasIntervencionesByPozo($pozo)
    {
        $qb = $this->getQb()
            ->innerJoin('interv.pozo', 'pozo')
            ->where('pozo = :pozo')
            ->orderBy('interv.fecha', 'DESC')
            ->orderBy('interv.fechaCreacion', 'DESC');

        $qb->setParameter('pozo', $pozo);

        return $qb;
    }

    public function getUltimasIntervencionesByEquipo($equipo)
    {
        $qb = $this->getQb()
            ->innerJoin('interv.equipo', 'equipo')
            ->where('equipo = :equipo')
            ->orderBy('interv.fecha', 'DESC')
            ->orderBy('interv.fechaCreacion', 'DESC');

        $qb->setParameter('equipo', $equipo);

        return $qb;
    }

    public function getUltimaIntervencionByEquipo($equipo)
    {
        $intervencionesQb = $this->getUltimasIntervencionesByEquipo($equipo);

        $intervencionesQb->setMaxResults(1);

        return $intervencionesQb;
    }


    public function getUltimaIntervencionByPozo($pozo)
    {
        $intervencionesQb = $this->getUltimasIntervencionesByPozo($pozo);

        $intervencionesQb->setMaxResults(1);

        return $intervencionesQb;
    }

    public function getIntervencionesByEquipos($equipos)
    {
        $qb = $this->getQb()
            ->innerJoin('interv.equipo', 'equipo')
            ->where('equipo IN (:equipos)')
            ->orderBy('interv.fecha', 'DESC')
            ->orderBy('interv.fechaCreacion', 'DESC');

        $qb->setParameter('equipos', $equipos);

        return $qb;
    }

    public function getIntervencionesCerradasByEquipo($equipos)
    {
        $qb = $this->getIntervencionesByEquipos($equipos);

        $qb->andWhere('interv.accion = 1');

        return $qb;
    }

    public function getIntervencionByEquipoyFecha($equipo, $fecha)
    {
        $qb = $this->getIntervencionesByEquipos($equipo);

        $qb->andWhere('interv.fecha = :fecha');

        $qb->setParameter('fecha', $fecha);

        return $qb;
    }

    public function getUltimaIntervencionAperturaByEquipoyFecha($equipo, $fecha)
    {
        $qb = $this->getIntervencionByEquipoyFecha($equipo, $fecha);

        $qb->andWhere('interv.accion = 0');

        $qb->setMaxResults(1);

        return $qb;
    }

    public function getIntervencionCierreByEquipoYFechaApertura($equipo, $fechaApertura)
    {

        // SELECT * from intervencion
        // where equipo_id = 1
        // and `fecha`> '2017-07-20 15:32:00'
        // and accion = 1
        // order by fecha ASC
        // LIMIT 1
        $qb = $this->getQb()
            ->andWhere('interv.fecha > :fecha_apertura')
            ->andWhere('interv.equipo = :equipo')
            ->andWhere('interv.accion = 1')
            ->orderBy('interv.fecha', 'ASC');

        $qb->setParameter('fecha_apertura', $fechaApertura);
        $qb->setParameter('equipo', $equipo);

        $qb->setMaxResults(1);

        return $qb;
    }

    public function getIntervencionAnterior($equipo, $fecha)
    {
        return $this->getUltimaIntervencionByEquipo($equipo)->andWhere('interv.fecha < :fecha')->setParameter('fecha', $fecha);
    }
}
