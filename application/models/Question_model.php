<?php

require_once(APPPATH . "models/Entidades/models.php");

class Question_model extends CI_Model {

    var $em;

    function __construct() {
        parent::__construct();
        $this->em = $this->doctrine->em;
    }

    function load($id) {
        return $this->em->find('Question', $id);
    }

    function get_five_question_random($letter) {
        $sql = "CALL getQuestions(?, @JSON_RESULT);";
        $stmt = $this->em->getConnection()->prepare($sql);
        $stmt->bindParam(1, $letter, PDO::PARAM_STR);
        $stmt->execute();

        $stmt = $this->em->getConnection()->query("SELECT @JSON_RESULT");
        $elements = json_decode($stmt->fetchColumn(), true);

        $questionResult = array();

        shuffle($elements);
        foreach ($elements as $element) {
            if (is_array($element)) {
                shuffle($element);
                $questionResult[] = $this->load($element[0]);
            } else
                $questionResult[] = $this->load($element);
        }
        return $questionResult;
    }

    function get_answer($answer_text, $letter, $question) {
        $condition = ($question->getConditionQuery() == null) ? '  AND a.question_id = :question_id ' : $question->getConditionQuery();

        $sql = "SELECT a.id, a.text, levenshtein(a.text, :answer_text) AS distance FROM game_answer a WHERE SUBSTRING(a.text,1,1) = :letter AND levenshtein(a.text, :answer_text) < 2 " . $condition . " ORDER BY distance";
        $stmt = $this->em->getConnection()->prepare($sql);
        $stmt->bindValue("answer_text", utf8_decode($answer_text));
        $stmt->bindValue("letter", $letter);
        $stmt->bindValue("question_id", $question->getId());
        $stmt->execute();
        $elements = $stmt->fetchAll();

        return (count($elements) > 0) ? $elements[0] : null;
    }

}
