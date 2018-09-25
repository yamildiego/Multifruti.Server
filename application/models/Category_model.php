<?php

require_once(APPPATH . "models/Entidades/models.php");

class Category_model extends CI_Model {

    var $em;

    function __construct() {
        parent::__construct();
        $this->em = $this->doctrine->em;
    }

    function load($id) {
        return $this->em->find('Category', $id);
    }

    function save($categorySuggest) {
        $this->em->persist($categorySuggest);
        $this->em->flush();
        return $categorySuggest;
    }

}
