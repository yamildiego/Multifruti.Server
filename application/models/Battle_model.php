<?php

require_once(APPPATH . "models/Entidades/models.php");

class Battle_model extends CI_Model {

    var $em;

    function __construct() {
        parent::__construct();
        $this->em = $this->doctrine->em;
    }

    function load($id) {
        return $this->em->find('Battle', $id);
    }

    function save($battle) {
        $this->em->persist($battle);
        $this->em->flush();
        return $battle;
    }

    function get_battle_with_opponent_active($userId, $opponentId) {
        $query = $this->em->getRepository('Battle')->createQueryBuilder('b');

        $today = new DateTime();
        $today->modify('- 3 days');

        $query->where('b.winner IS NULL')
                ->andwhere('b.userOne = :opponentId OR b.userTwo = :opponentId')
                ->andwhere('b.userOne = :userId OR b.userTwo = :userId')
                ->andwhere('b.lastGameDatetime >= :date')
                ->setParameter('opponentId', $opponentId)
                ->setParameter('userId', $userId)
                ->setParameter('date', $today->format('Y-m-d G:i:s'));

        return $query->getQuery()->getOneOrNullResult();
    }

    function get_battles_active($userId) {
        $query = $this->em->getRepository('Battle')->createQueryBuilder('b');

        $query->where('b.winner IS NULL')
                ->andwhere('b.userOne = :userId OR b.userTwo = :userId')
                ->setParameter('userId', $userId);
        return $query->getQuery()->getResult();
    }

    function get_battles_my_turn($userId) {
        $query = $this->em->getRepository('Battle')->createQueryBuilder('b');

        $today = new DateTime();
        $today->modify('- 3 days');

        $query->where('b.winner IS NULL')
                ->andwhere('(b.userOne = :userId AND b.approvedUserOne = TRUE) OR (b.userTwo = :userId AND  b.approvedUserTwo = TRUE)')
                ->setParameter('userId', $userId)
                ->andwhere('b.userTurn = :userTurnId')
                ->setParameter('userTurnId', $userId)
                ->andwhere('b.lastGameDatetime >= :date')
                ->setParameter('date', $today->format('Y-m-d G:i:s'));
        $query->orderBy('b.lastGameDatetime', 'ASC');

        return $query->getQuery()->getResult();
    }

    function get_battles_for_approval($userId) {
        $query = $this->em->getRepository('Battle')->createQueryBuilder('b');

        $today = new DateTime();
        $today->modify('- 3 days');

        $query->where('b.winner IS NULL')
                ->andwhere('(b.userOne = :userId AND b.approvedUserOne = FALSE) OR (b.userTwo = :userId AND  b.approvedUserTwo = FALSE)')
                ->setParameter('userId', $userId)
                ->andwhere('b.userTurn = :userTurnId')
                ->setParameter('userTurnId', $userId)
                ->andwhere('b.lastGameDatetime >= :date')
                ->setParameter('date', $today->format('Y-m-d G:i:s'));
        $query->orderBy('b.lastGameDatetime', 'ASC');

        return $query->getQuery()->getResult();
    }

    function get_battles_your_turn($userId) {
        $query = $this->em->getRepository('Battle')->createQueryBuilder('b');

        $today = new DateTime();
        $today->modify('- 3 days');

        $query->where('b.winner IS NULL')
                ->andwhere('(b.userOne = :userId AND b.approvedUserOne = TRUE) OR (b.userTwo = :userId AND b.approvedUserTwo = TRUE)')
                ->setParameter('userId', $userId)
                ->andwhere('b.userTurn <> :userTurnId')
                ->setParameter('userTurnId', $userId)
                ->andwhere('b.lastGameDatetime >= :date')
                ->setParameter('date', $today->format('Y-m-d G:i:s'));
        $query->orderBy('b.lastGameDatetime', 'ASC');

        return $query->getQuery()->getResult();
    }

    function get_battles_closed($userId) {
        $query = $this->em->getRepository('Battle')->createQueryBuilder('b');

        $today = new DateTime();
        $today->modify('- 5 days');

        $query->where('b.winner IS NOT NULL')
                ->andwhere('b.userOne = :userId OR b.userTwo = :userId')
                ->andwhere('b.lastGameDatetime >= :date')
                ->setParameter('userId', $userId)
                ->setParameter('date', $today->format('Y-m-d G:i:s'));
        $query->orderBy('b.lastGameDatetime', 'ASC');

        return $query->getQuery()->getResult();
    }

}
