<?php

require_once(APPPATH . "models/Entidades/models.php");

class Common_Functions_model extends CI_Model {

    var $em;
    var $letters;

    function __construct() {
        parent::__construct();
        $this->em = $this->doctrine->em;
        $this->letters = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        $this->lettersProba = array('A' => 6, 'B' => 4, 'C' => 6, 'D' => 5, 'E' => 5, 'F' => 5, 'G' => 5, 'H' => 5, 'I' => 3, 'J' => 4, 'K' => 2, 'L' => 5, 'M' => 6, 'N' => 6, 'O' => 5, 'P' => 5, 'Q' => 1, 'R' => 3, 'S' => 4, 'T' => 5, 'U' => 4, 'V' => 2, 'W' => 1, 'X' => 1, 'Y' => 1, 'Z' => 1);
    }

    function get_time_difference($startedDatetime) {
        $interval = $startedDatetime->diff(date_create());
        $year = (int) ($interval->format('%y') >= 1) ? 1 : 0;
        $moth = (int) ($interval->format('%m') >= 1) ? 1 : 0;
        $day = (int) $interval->format('%d');
        $hour = (int) $interval->format('%h');
        $minute = (int) $interval->format('%i');
        $secod = (int) $interval->format('%s');

        $day_total = ($year * 365) + ($moth * 30) + $day;
        return ($day_total * 86400) + ($hour * 3600) + ($minute * 60) + $secod;
    }

    function get_time_difference_negative($startedDatetime) {
        $interval = date_create()->diff($startedDatetime);
        $year = (int) $interval->format('%y');
        $moth = (int) $interval->format('%m');
        $day = (int) $interval->format('%d');
        $hour = (int) $interval->format('%h');
        $minute = (int) $interval->format('%i');
        $secod = (int) $interval->format('%s');

        $day_total = ($year * 365) + ($moth * 30) + $day;
        $secod_total = ($day_total * 86400) + ($hour * 3600) + ($minute * 60) + $secod;

        return ($startedDatetime > date_create()) ? (-1 * $secod_total) : $secod_total;
    }

    function get_number_with_letter($letter) {
        $size = 0;
        $find = false;
        while ($size < count($this->letters) && $find == false) {
            if ($letter == $this->letters[$size])
                $find = true;
            else
                $size++;
        }

        return $size;
    }

    function get_letter_with_number($number) {
        return $this->letters[$number];
    }

    function get_number_round($rounds, $round_id) {
        $arrayGetNumberRound = array();
        foreach ($rounds as $tempRound)
            $arrayGetNumberRound[$tempRound->getId()] = $tempRound->getId();

        ksort($arrayGetNumberRound);

        $number_round = 0;
        $number = 0;
        foreach ($arrayGetNumberRound as $key => $value) {
            $number++;
            if ($key == $round_id)
                $number_round = $number;
        }
        return $number_round;
    }

    function get_marker($battle, $user_id) {
        $userActual = ($battle->getUserOne()->getId() == $user_id) ? "One" : "Two";
        $opponetActual = ($battle->getUserOne()->getId() !== $user_id) ? "One" : "Two";

        $opponetNumber = 0;
        $userNumber = 0;

        $getPointsUser = "getPointsUser" . $userActual;
        $getPointsOpponet = "getPointsUser" . $opponetActual;
        foreach ($battle->getRounds() as $round) {
            if ($round->getStartedDatetimeUserOne() != null && $round->getStartedDatetimeUserTwo() != null)
                if ($round->$getPointsUser() > $round->$getPointsOpponet())
                    $userNumber++;
                elseif ($round->$getPointsUser() < $round->$getPointsOpponet())
                    $opponetNumber++;
                elseif ($round->$getPointsUser() == $round->$getPointsOpponet()) {
                    $userNumber++;
                    $opponetNumber++;
                }
        }
        return $userNumber . ' - ' . $opponetNumber;
    }

    function get_data_show($round, $number_text_question, $user) {
        $result = array();

        $getAnswer = "getAnswer" . $number_text_question . "User" . $user;
        $getAnswerText = "getAnswerText" . $number_text_question . "User" . $user;
        $getPointsAnswer = "getPointsAnswer" . $number_text_question . "User" . $user;

        if ($round->$getAnswer() != null)
            $result['answerText'] = utf8_encode(ucfirst(strtolower($round->$getAnswer()->getText())));
        else
            $result['answerText'] = $round->$getAnswerText();

        $result['score'] = $round->$getPointsAnswer();
        return $result;
    }

    function url_exists($url = NULL) {
        if (empty($url))
            return false;

        $options['http'] = array('method' => "HEAD", 'ignore_errors' => 1, 'max_redirects' => 0);
        $body = @file_get_contents($url, NULL, stream_context_create($options));

        if (isset($http_response_header)) {
            sscanf($http_response_header[0], 'HTTP/%*d.%*d %d', $httpcode);

            $accepted_response = array(200, 301, 302);

            return in_array($httpcode, $accepted_response);
        } else
            return false;
    }

    function get_lives_genereted($startedDatetime) {
        return $this->truncate($this->get_time_difference_negative($startedDatetime) / 3600);
    }

    function truncate($val, $f = "0") {
        if (($p = strpos($val, '.')) !== false)
            $val = floatval(substr($val, 0, $p + 1 + $f));
        return $val;
    }

    function get_rand_letter() {
        $rest = array();
        foreach ($this->lettersProba as $key => $value)
            for ($index = 0; $index < $value; $index++)
                $rest[] = $key;
        shuffle($rest);

        return $this->get_number_with_letter($rest[0]);
    }

}
