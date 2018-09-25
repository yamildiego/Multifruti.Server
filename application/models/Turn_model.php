<?php

require_once(APPPATH . "models/Entidades/models.php");

class Turn_model extends CI_Model {

    var $em;

    function __construct() {
        parent::__construct();
        $this->em = $this->doctrine->em;
    }

    function get_last_round($battle) {
        $rounds_ids = array();

        foreach ($battle->getRounds() as $round)
            $rounds_ids[$round->getId()] = $round;

        ksort($rounds_ids);

        return count($rounds_ids) > 0 ? end($rounds_ids) : null;
    }

    function update_turn_battles($userId) {
        $battles = $this->battle_model->get_battles_active($userId);
        foreach ($battles as $battle)
            $this->update_turn_battle($battle->getId(), date_create()->format('Y-m-d H:i:s'));
    }

    function update_turn_battle($battleId, $dateActual) {
        $sql = "CALL updateTurnBattle(:battleId, :dateActual);";

        $stmt = $this->em->getConnection()->prepare($sql);

        $stmt->bindValue('battleId', $battleId);
        $stmt->bindValue('dateActual', $dateActual);
        $stmt->execute();
    }

}
