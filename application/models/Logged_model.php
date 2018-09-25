<?php

require_once(APPPATH . "models/Entidades/models.php");

class Logged_model extends CI_Model {

    var $em;

    function __construct() {
        parent::__construct();
        $this->em = $this->doctrine->em;
    }

    function get_opponent($user_id) {
        $battles = $this->battle_model->get_battles_active($user_id);
        $opponens_ids = array();
        $subquery = "u.id <> :my_id ";
        if ($battles != null)
            foreach ($battles as $battle) {
                if ($battle->getUserOne()->getId() != $user_id && $battle->getUserTwo()->getId() == $user_id) {
                    $subquery .= " AND u.id <>  " . $battle->getUserOne()->getId();
                    $opponens_ids[] = $battle->getUserOne()->getId();
                } elseif ($battle->getUserTwo()->getId() != $user_id && $battle->getUserOne()->getId() == $user_id) {
                    $subquery .= " AND u.id <>  " . $battle->getUserTwo()->getId();
                    $opponens_ids[] = $battle->getUserTwo()->getId();
                }
            }

        $sql = "SELECT id FROM game_user u WHERE " . $subquery . " ORDER BY RAND() LIMIT 1";

        $stmt = $this->em->getConnection()->prepare($sql);
        $stmt->bindValue("my_id", $user_id);
        $stmt->execute();
        $elements = $stmt->fetchAll();

        $opponent = (count($elements) > 0) ? $elements[0] : null;

        return ($opponent == null) ? null : $this->user_model->load($opponent['id']);
    }

    function get_ranking_global($offset) {
        $query = $this->em->getRepository('User')
                ->createQueryBuilder('u')
                ->where('u.experience > 0 ')
                ->orderBy('u.experience', 'DESC')
                ->setFirstResult($offset)
                ->setMaxResults(10);
        return $query->getQuery()->getResult();
    }

    function get_total_points($roundId, $user) {
        if ($roundId != 0 && $roundId != null && ($user == 'points_user_one' || $user == 'points_user_two')) {
            try {
                $sql = "SELECT " . $user . " FROM game_round r WHERE r.id = " . $roundId . ";";
                $query = $this->db->query($sql);
                return $query->result()[0]->$user;
            } catch (Exception $ex) {
                return 0;
            }
        } else
            return 0;
    }

}
