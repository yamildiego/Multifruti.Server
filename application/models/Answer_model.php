<?php

require_once(APPPATH . "models/Entidades/models.php");

class Answer_model extends CI_Model {

    var $em;

    function __construct() {
        parent::__construct();
        $this->em = $this->doctrine->em;
    }

    function get_answer($round_id, $question_id, $answer_text, $user_id) {
        $answer = null;
        if ($round_id != null && $question_id != null) {

            $round = $this->round_model->load($round_id);
            $question = $this->question_model->load($question_id);

            if ($question_id == $round->getQuestionOne()->getId())
                $current = 1;
            elseif ($question_id == $round->getQuestionTwo()->getId())
                $current = 2;
            elseif ($question_id == $round->getQuestionThree()->getId())
                $current = 3;
            elseif ($question_id == $round->getQuestionFour()->getId())
                $current = 4;
            elseif ($question_id == $round->getQuestionFive()->getId())
                $current = 5;

            if (!empty($round) && !empty($question)) {
                switch ($current) {
                    case 1: {
                            $answer = $this->_check_question($current, $answer_text, $round, $round->getQuestionOne(), $question_id, $user_id);
                            break;
                        }
                    case 2: {
                            $answer = $this->_check_question($current, $answer_text, $round, $round->getQuestionTwo(), $question_id, $user_id);
                            break;
                        }
                    case 3: {
                            $answer = $this->_check_question($current, $answer_text, $round, $round->getQuestionThree(), $question_id, $user_id);
                            break;
                        }
                    case 4: {
                            $answer = $this->_check_question($current, $answer_text, $round, $round->getQuestionFour(), $question_id, $user_id);
                            break;
                        }
                    case 5: {
                            $answer = $this->_check_question($current, $answer_text, $round, $round->getQuestionFive(), $question_id, $user_id);
                            break;
                        }
                    default:
                        break;
                }
            }
        }

        return $answer;
    }

    function _check_question($current, $answer_text, $round, $question, $question_id, $user_id) {
        $answer = null;

        if ($question->getId() == $question_id) {
            if ($user_id == $round->getBattle()->getUserOne()->getId())
                $answer = $this->_execute_query($current, 1, $round, $answer_text, $question);
            elseif ($user_id == $round->getBattle()->getUserTwo()->getId())
                $answer = $this->_execute_query($current, 2, $round, $answer_text, $question);
        }

        return $answer;
    }

    function _execute_query($current, $user, $round, $answer_text, $question) {
        $answer = null;
        $text_answer = "";
        $text_user = "";

        switch ($current) {
            case 1:$text_answer = "One";
                break;
            case 2:$text_answer = "Two";
                break;
            case 3:$text_answer = "Three";
                break;
            case 4:$text_answer = "Four";
                break;
            case 5:$text_answer = "Five";
                break;
            default:
                break;
        }

        switch ($user) {
            case 1:$text_user = "One";
                break;
            case 2:$text_user = "Two";
                break;
            default:
                break;
        }

        $getAnswer = "getAnswer" . $text_answer . "User" . $text_user;
        $setAnswer = "setAnswer" . $text_answer . "User" . $text_user;
        $setAnswerText = "setAnswerText" . $text_answer . "User" . $text_user;
        $setPointsAnswer = "setPointsAnswer" . $text_answer . "User" . $text_user;

        if ($round->$getAnswer() != null)
            $answer = $round->$getAnswer();
        else {
            $answerNew = $this->question_model->get_answer($answer_text, $round->getLetter(), $question);
            $round->$setAnswerText($answer_text);
            $round = $this->round_model->save($round);

            if ($answerNew != null && isset($answerNew['id'])) {
                $ans = $this->load($answerNew['id']);
                $round->$setAnswer($ans);
                $round->$setPointsAnswer($ans->getPoint());
                $ans->setUsed($ans->getUsed() + 1);
                $ans = $this->save($ans);
                $round = $this->round_model->save($round);
            }
            $answer = $round->$getAnswer();
        }

        return $answer;
    }

    function get_text_number_answer($questionId, $round) {
        $textNumberAnswer = "";
        switch ($questionId) {
            case $round->getQuestionOne()->getId(): {
                    $textNumberAnswer = "One";
                    break;
                }
            case $round->getQuestionTwo()->getId(): {
                    $textNumberAnswer = "Two";
                    break;
                }
            case $round->getQuestionThree()->getId(): {
                    $textNumberAnswer = "Three";
                    break;
                }
            case $round->getQuestionFour()->getId(): {
                    $textNumberAnswer = "Four";
                    break;
                }
            case $round->getQuestionFive()->getId(): {
                    $textNumberAnswer = "Five";
                    break;
                }
        }

        return $textNumberAnswer;
    }

    function get_answer_ramdon($questionId, $letter, $condition = null) {
        if ($condition == null)
            $cond = "AND a.question_id = :question_id";
        else
            $cond = $condition;

        $sql = "SELECT a.id FROM game_answer a WHERE SUBSTRING(a.text,1,1) = lower(:letter) " . $cond . " ORDER BY RAND() LIMIT 1";
        $stmt = $this->em->getConnection()->prepare($sql);
        $stmt->bindValue("letter", $letter);
        if ($condition == null)
            $stmt->bindValue("question_id", $questionId);
        $stmt->execute();
        $elements = $stmt->fetchAll();

        return (count($elements) > 0) ? $this->load($elements[0]) : null;
    }

    function get_answer_suggest($questionId, $roundId, $userId) {
        $query = $this->em->getRepository('AnswerSuggest')
                ->createQueryBuilder('a');

        $query->where('a.user = :userId')
                ->andWhere('a.round = :roundId')
                ->andWhere('a.question = :questionId')
                ->setParameter('userId', $userId)
                ->setParameter('roundId', $roundId)
                ->setParameter('questionId', $questionId);
        return $query->getQuery()->getOneOrNullResult();
    }

    function load($answerId) {
        return $this->em->find('Answer', $answerId);
    }

    function save($answer) {
        $this->em->persist($answer);
        $this->em->flush();
        return $answer;
    }

}
