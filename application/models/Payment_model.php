<?php

require_once(APPPATH . "models/Entidades/models.php");

class Payment_model extends CI_Model {

    var $em;

    function __construct() {
        parent::__construct();
        $this->em = $this->doctrine->em;
    }

    function load($answerId) {
        return $this->em->find('Payment', $answerId);
    }

    function save($answer) {
        $this->em->persist($answer);
        $this->em->flush();
        return $answer;
    }

}
