<?php

require_once(APPPATH . "models/Entidades/models.php");

class Round_model extends CI_Model {

    var $em;

    function __construct() {
        parent::__construct();
        $this->em = $this->doctrine->em;
    }

    function load($roundId) {
        return $this->em->find('Round', $roundId);
    }

    function save($round) {
        $this->em->persist($round);
        $this->em->flush();
        return $round;
    }

    function get_last_round($battle_id) {
        $battle = $this->battle_model->load($battle_id);
        $round_result = null;

        if ($battle != null) {
            $rounds = $battle->getRounds();
            $max_id = 0;
            foreach ($rounds as $round) {
                if ($round->getId() > $max_id) {
                    $max_id = $round->getId();
                    $round_result = $round;
                }
            }
        }
        return $round_result;
    }

    function get_next_round_id($battle, $round_actual_id) {
        $rounds_ids = array();

        foreach ($battle->getRounds() as $round)
            $rounds_ids[$round->getId()] = $round->getId();

        ksort($rounds_ids);

        $round_id_next = null;
        $flag = false;
        foreach ($rounds_ids as $round_id) {
            if ($round_id == $round_actual_id && $round_id_next === null)
                $flag = true;
            elseif ($flag === true) {
                $round_id_next = $round_id;
                $flag = false;
            }
        }
        return $round_id_next;
    }

    function get_back_round_id($battle, $round_actual_id) {
        $rounds_ids = array();
        foreach ($battle->getRounds() as $round)
            $rounds_ids[] = $round->getId();

        krsort($rounds_ids);

        $round_id_back = null;
        $flag = false;
        foreach ($rounds_ids as $round_id) {
            if ($round_id == $round_actual_id && $round_id_back === null)
                $flag = true;
            elseif ($flag === true) {
                $flag = false;
                $round_id_back = $round_id;
            }
        }
        return $round_id_back;
    }

}
