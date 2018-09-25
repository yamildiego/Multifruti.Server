<?php

require APPPATH . 'libraries/REST_Controller.php';

class logged extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('battle_model');
        $this->load->model('round_model');
        $this->load->model('common_functions_model');
        $this->load->model('logged_model');
        $this->load->model('turn_model');
        $this->load->model('question_model');
        $this->load->model('answer_model');
        $this->load->model('category_model');
    }

    public function getLoginStatus_get() {
        $data = array('status' => null);

        if ($this->session->userdata('logout') === 'no-logged') {
            $data['status'] = 'no-logged';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        } else
            $user = $this->_existsControlUser();

        $lives = $user->getLives();
        $timer = 0;

        $constantMaxLife = 3;

        if ($user->getLastGameDatetime() != null && $user->getLives() < $constantMaxLife && $lives != 1000) {
            $secondTotal = $this->common_functions_model->get_time_difference_negative($user->getLastGameDatetime());
            $lifeGenered = $this->truncate($secondTotal / 3600);
            if (($lifeGenered + $user->getLives()) < $constantMaxLife) {
                $lives = $lifeGenered + $user->getLives();
                $timer = 3600 - ($secondTotal - ($lifeGenered) * 3600);
            } else
                $lives = $constantMaxLife;
        }elseif ($lives == 1000)
            $lives = -10;

        $data['data'] = array(
            'userId' => $user->getId(),
            'id' => ($user->getUserIdFb() == null || $user->getUserIdFb() == "") ? 0 : $user->getUserIdFb(),
            'first_name' => utf8_encode($user->getName()),
            'last_name' => utf8_encode($user->getLastName()),
            'is_premium' => $user->getIsPremium(),
            'picture' => array('data' => array('url' => utf8_encode($user->getImage()))),
            'cover' => array('source' => utf8_encode($user->getCover())),
            'lives' => $lives,
            'experience' => $user->getExperience(),
            'timer' => $timer,
            'coins' => $user->getCoins());

        $data['status'] = 'OK';

        $this->response($data, REST_Controller::HTTP_OK); // OK (200) 
    }

    function getBattles_get() {
        $user = $this->_existsControlUser();
        $this->turn_model->update_turn_battles($user->getId());

        $battles_my_turn = $this->battle_model->get_battles_my_turn($user->getId());
        $battles_for_approval = $this->battle_model->get_battles_for_approval($user->getId());
        $battles_your_turn = $this->battle_model->get_battles_your_turn($user->getId());
        $battles_finished = $this->battle_model->get_battles_closed($user->getId());
        $data_battles_my_turn = array();
        $data_battles_for_approval = array();
        $data_battles_your_turn = array();
        $data_battles_finished = array();

        foreach ($battles_my_turn as $battle_my_turn) {
            $userOpponent = ($battle_my_turn->getUserTwo()->getId() == $user->getId()) ? "One" : "Two";
            $getOpponent = "getUser" . $userOpponent;
            $getApprovedOpponent = "getApprovedUser" . $userOpponent;
            $opponent = $battle_my_turn->$getOpponent();
            $opponent_image = (($opponent->getImage() == '' || $opponent->getImage() == null) && !$this->common_functions_model->url_exists($opponent->getImage())) ? $this->config->item('url_frontend') . 'Image/image.jpg' : $opponent->getImage();
            $marker = $this->common_functions_model->get_marker($battle_my_turn, $user->getId());
            $lastRoundId = ($this->round_model->get_last_round($battle_my_turn->getId()) == null) ? null : $this->round_model->get_last_round($battle_my_turn->getId())->getId();
            $data_battles_my_turn[] = array(
                'battleId' => $battle_my_turn->getId(),
                'name' => $opponent->getName(),
                'numberRound' => ( $battle_my_turn->getRounds()->count() == 0) ? 1 : $battle_my_turn->getRounds()->count(),
                'marker' => $marker,
                'image' => $opponent_image,
                'timeLeft' => $this->common_functions_model->get_time_difference($battle_my_turn->getLastGameDatetime()),
                'lastRoundId' => $lastRoundId
            );
        }

        foreach ($battles_for_approval as $battle_for_approval) {
            $userOpponent = ($battle_for_approval->getUserTwo()->getId() == $user->getId()) ? "One" : "Two";
            $getOpponent = "getUser" . $userOpponent;
            $getApprovedOpponent = "getApprovedUser" . $userOpponent;
            $opponent = $battle_for_approval->$getOpponent();
            $opponent_image = (($opponent->getImage() == '' || $opponent->getImage() == null) && !$this->common_functions_model->url_exists($opponent->getImage())) ? $this->config->item('url_frontend') . 'Image/image.jpg' : $opponent->getImage();
            $marker = $this->common_functions_model->get_marker($battle_for_approval, $user->getId());
            $lastRoundId = ($this->round_model->get_last_round($battle_for_approval->getId()) == null) ? null : $this->round_model->get_last_round($battle_for_approval->getId())->getId();
            $data_battles_for_approval[] = array(
                'battleId' => $battle_for_approval->getId(),
                'name' => $opponent->getName(),
                'image' => $opponent_image,
                'timeLeft' => $this->common_functions_model->get_time_difference($battle_for_approval->getLastGameDatetime()),
                'lastRoundId' => $lastRoundId
            );
        }

        foreach ($battles_your_turn as $battle_your_turn) {
            $userOpponent = ($battle_your_turn->getUserTwo()->getId() == $user->getId()) ? "One" : "Two";
            $getOpponent = "getUser" . $userOpponent;
            $getApprovedOpponent = "getApprovedUser" . $userOpponent;
            $opponent = $battle_your_turn->$getOpponent();
            $opponent_image = (($opponent->getImage() == '' || $opponent->getImage() == null) && !$this->common_functions_model->url_exists($opponent->getImage())) ? $this->config->item('url_frontend') . 'Image/image.jpg' : $opponent->getImage();
            $marker = $this->common_functions_model->get_marker($battle_your_turn, $user->getId());
            $lastRoundId = ($this->round_model->get_last_round($battle_your_turn->getId()) == null) ? null : $this->round_model->get_last_round($battle_your_turn->getId())->getId();
            $data_battles_your_turn[] = array(
                'battleId' => $battle_your_turn->getId(),
                'name' => $opponent->getName(),
                'numberRound' => ( $battle_your_turn->getRounds()->count() == 0) ? 1 : $battle_your_turn->getRounds()->count(),
                'marker' => $marker,
                'image' => $opponent_image,
                'isApproved' => $battle_your_turn->$getApprovedOpponent(),
                'timeLeft' => $this->common_functions_model->get_time_difference($battle_your_turn->getLastGameDatetime()),
                'lastRoundId' => $lastRoundId
            );
        }

        foreach ($battles_finished as $battle_finished) {
            $userOpponent = ($battle_finished->getUserTwo()->getId() == $user->getId()) ? "One" : "Two";
            $getOpponent = "getUser" . $userOpponent;
            $opponent = $battle_finished->$getOpponent();
            $opponent_image = (($opponent->getImage() == '' || $opponent->getImage() == null) && !$this->common_functions_model->url_exists($opponent->getImage())) ? $this->config->item('url_frontend') . 'Image/image.jpg' : $opponent->getImage();
            $marker = $this->common_functions_model->get_marker($battle_finished, $user->getId());
            $data_battles_finished[] = array(
                'battleId' => $battle_finished->getId(),
                'name' => $opponent->getName(),
                'numberRound' => ($battle_finished->getRounds()->count() == 0) ? 1 : $battle_finished->getRounds()->count(),
                'marker' => $marker,
                'image' => $opponent_image
            );
        }

        $data = array('status' => 'OK');
        $data['data'] = array('battlesMyTurn' => $data_battles_my_turn, 'battlesForApproval' => $data_battles_for_approval, 'battlesYourTurn' => $data_battles_your_turn, 'battlesFinished' => $data_battles_finished);

        $this->response($data, REST_Controller::HTTP_OK); // OK (200)
    }

    function newGame_get() {
        $data = array('status' => null);
        $user = $this->_existsControlUser();

        $lifeGenered = $this->common_functions_model->get_lives_genereted($user->getLastGameDatetime());
        if ($user->getLives() + $lifeGenered <= 0) {
            $data['status'] = 'without_lives';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        }

        $opponent = $this->logged_model->get_opponent($user->getId());
        if ($opponent == null)
            die("NO TENGO OPONENTES");

        $battle = new Battle();
        $battle->setUserOne($user);
        $battle->setUserTwo($opponent);
        $battle->setUserTurn($user);
        $battle->setApprovedUserOne(true);
        $battle->setApprovedUserTwo(false);
        $battle->setWinner(null);
        $battle->setLastGameDatetime(date_create());
        $battle->setCreationDate(date_create());
        try {
            $battle = $this->battle_model->save($battle);
        } catch (Exception $exc) {
            $data['status'] = 'unexpected_error';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302) 
        }
        $data['data'] = array('battleId' => $battle->getId());
        $data['status'] = 'OK';
        $this->response($data, REST_Controller::HTTP_OK); // OK (200) 
    }

    public function playWithFriend_get($friendIdFb) {
        $data = array('status' => null);

        $user = $this->_existsControlUser();

        $lifeGenered = $this->common_functions_model->get_lives_genereted($user->getLastGameDatetime());
        if ($user->getLives() + $lifeGenered <= 0) {
            $data['status'] = 'without_lives';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        }

        $opponent = $this->user_model->get_user_fb_id($friendIdFb);

        if ($opponent == null) {
            $data['status'] = 'not_opponent';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        } elseif ($user->getId() == $opponent->getId()) {
            $data['status'] = 'not_play';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        }

        $battle = $this->battle_model->get_battle_with_opponent_active($user->getId(), $opponent->getId());

        if ($battle != null) {
            $data['status'] = 'game_active';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        }

        $battle = new Battle();
        $battle->setUserOne($user);
        $battle->setUserTwo($opponent);
        $battle->setUserTurn($user);
        $battle->setApprovedUserOne(true);
        $battle->setApprovedUserTwo(false);
        $battle->setWinner(null);
        $battle->setCreationDate(date_create());

        try {
            $battle = $this->battle_model->save($battle);
        } catch (Exception $exc) {
            $data['status'] = 'unexpected_error';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        }

        $opponentImage = (($opponent->getImage() == '' || $opponent->getImage() == null) && !$this->common_functions_model->url_exists($opponent->getImage())) ? $this->config->item('url_frontend') . 'Image/image.jpg' : $opponent->getImage();
        $userImage = (($user->getImage() == '' || $user->getImage() == null) && !$this->common_functions_model->url_exists($user->getImage())) ? $this->config->item('url_frontend') . 'Image/image.jpg' : $user->getImage();

        $dataBattle = array('battleId' => $battle->getId());
        $dataBattle['user'] = array('name' => $user->getName(), 'image' => $userImage);
        $dataBattle['opponent'] = array('name' => $opponent->getName(), 'image' => $opponentImage);

        $data['data'] = $dataBattle;

        $data['status'] = 'OK';
        $this->response($data, REST_Controller::HTTP_OK); // OK (200) 
    }

    function letterSelection_get($battleId) {
        $data = array('status' => null);

        $user = $this->_existsControlUser();
        $battle = $this->_existsControlBattle($battleId, $user->getId());

        $userActual = ($battle->getUserOne()->getId() == $user->getId()) ? "One" : "Two";
        $userOpponent = ($battle->getUserTwo()->getId() == $user->getId()) ? "One" : "Two";

        $getUser = "getUser" . $userActual;
        $getApprovedUser = "getApprovedUser" . $userActual;
        $setApprovedUser = "setApprovedUser" . $userActual;
        $getStartedDatetimeUser = "getStartedDatetimeUser" . $userActual;
        $getStartedDatetimeOpponent = "getStartedDatetimeUser" . $userOpponent;

        $lifeGenered = $this->common_functions_model->get_lives_genereted($user->getLastGameDatetime());
        if ($user->getLives() + $lifeGenered <= 0 && !$battle->$getApprovedUser()) {
            $data['status'] = 'without_lives';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        }

        if ($battle->getWinner() != null) {
            $data['status'] = 'finished_game';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        }

        if ($battle->getUserTurn()->getId() != $user->getId()) {
            $data['status'] = 'not_your_turn';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        }

        if (!$battle->$getApprovedUser())
            $battle->$setApprovedUser(true);

        $nextRound = null;
        $myOpponentHasToPlay = false;
        $secundsTotalsUser = null;

        //obtengo el round actual si es que lo hay
        foreach ($battle->getRounds() as $round) {
            $started_datetime = $round->$getStartedDatetimeUser();
            if ($started_datetime != null) {
                $secunds_total = $this->common_functions_model->get_time_difference($started_datetime);
            }

            if ($round->$getStartedDatetimeUser() == null || $round->$getStartedDatetimeOpponent() == null) { // verifico si el round esta activo
                if ($round->$getStartedDatetimeUser() == null)  //verifico si el usuario correspondiente inicio alguna vez este round
                    $nextRound = $round; //si nunca inicio guardo el round para luego jugarlo
                elseif ($secunds_total < 30) {
                    $nextRound = $round; //si el tiempo es menor a 30 segundos guardo el round para redirecionar a la vista de play con el tiempo restantes
                    $secundsTotalsUser = $secunds_total;
                } elseif ($round->$getStartedDatetimeOpponent() == null) {
                    $myOpponentHasToPlay = true;
                    $secundsTotalsUser = $secunds_total;
                }
            } elseif (isset($secunds_total) && $secunds_total < 30) {
                $nextRound = $round; //si el tiempo es menor a 30 segundos guardo el round para redirecionar a la vista de play con el tiempo restantes
            }
        }

        if ($nextRound == null && $myOpponentHasToPlay === false) { //SI ES IGUAL A NULL O NO HAY ROUND O HAY PERO YA ESTAN JUGADOS            
            $number_letter = $this->common_functions_model->get_rand_letter();
            $dataLetterSelection = array('numberLetter' => $number_letter);
            $questions = $this->question_model->get_five_question_random($this->common_functions_model->get_letter_with_number($number_letter));

            $round = new Round();
            $round->setBattle($battle);
            $round->setLetter($this->common_functions_model->get_letter_with_number($number_letter));
            $round->setQuestionOne($questions[0]);
            $round->setQuestionTwo($questions[1]);
            $round->setQuestionThree($questions[2]);
            $round->setQuestionFour($questions[3]);
            $round->setQuestionFive($questions[4]);
            $battle->setUserTurn($battle->$getUser());

            try {
                $this->round_model->save($round);
                $this->battle_model->save($battle);
                $dataLetterSelection['roundId'] = $round->getId();
                $data['data'] = $dataLetterSelection;
                $data['status'] = 'OK';

                $this->response($data, REST_Controller::HTTP_CREATED); // OK (201)
            } catch (Exception $exc) {
                $data['status'] = 'unexpected_error';
                $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
            }
        } elseif ($myOpponentHasToPlay && ($secunds_total == null || $secundsTotalsUser > 30)) { // NO ES EL TURNO DEL USUARIO ES TURNO DEL OPONENTE
            $data['status'] = 'waiting_opponent';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        } elseif ($nextRound != null && $nextRound->$getStartedDatetimeUser() == null) { // SI LA FECHA DE INICIO ES NULL EL USUARIO NUNCA JUGO ESTE ROUND
            try {
                $this->battle_model->save($battle);
            } catch (Exception $exc) {
                $data['status'] = 'unexpected_error';
                $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
            }

            $data = array('status' => 'OK', 'data' => array('roundId' => $round->getId(), 'numberLetter' => $this->common_functions_model->get_number_with_letter($round->getLetter())));
            $this->response($data, REST_Controller::HTTP_OK); // OK (200)
        } else {
            if ($secunds_total < 30) { // si no es null pero el tiempo es menor a 30 segundos permitirle seguir jugando
                //redireccionar con el tiempo restante en el contador
                $data = array('status' => 'playing', 'data' => array('roundId' => $nextRound->getId(), 'numberLetter' => $this->common_functions_model->get_number_with_letter($nextRound->getLetter())));

                $this->response($data, REST_Controller::HTTP_OK); // OK (200)
            } else {//si esta aca es un error redireccionar y tirar error con un codigo
                $data['status'] = 'unexpected_error';
                $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
            }
        }
    }

    public function play_get($roundId) {
        $data = array('status' => null);
        $dataWS = array();

        $user = $this->_existsControlUser();
        $round = $this->_existsControlRound($roundId);
        $battle = $this->_existsControlBattle($round->getBattle()->getId(), $user->getId());
        $userActual = ($battle->getUserOne()->getId() == $user->getId()) ? "One" : "Two";
        $userOpponent = ($round->getBattle()->getUserTwo()->getId() == $user->getId()) ? "One" : "Two";

        $getUserOpponent = "getUser" . $userOpponent;
        $getStartedDatetimeUser = "getStartedDatetimeUser" . $userActual;
        $setStartedDatetimeUser = "setStartedDatetimeUser" . $userActual;

        if (($battle->getRounds()->count() == 0 || $battle->getRounds()->count() == 1) && $round->$getStartedDatetimeUser() == null) {
            // si el usuario logeado tiene su fecha de inicio de round nula descuento la vida. si no, esta jugando o ya lo jugo(time_out)
            $lifeGenered = $this->common_functions_model->get_lives_genereted($user->getLastGameDatetime());
            if ($user->getLives() + $lifeGenered <= 0) {
                $data['status'] = 'without_lives';
                $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
            } elseif ($user->getLives() != 1000) {
                $lifeGenered = ($lifeGenered > 3) ? 3 : $lifeGenered;
                $newLives = ($user->getLives() > 3) ? $user->getLives() : ((($user->getLives() + $lifeGenered) > 3) ? 3 : ($user->getLives() + $lifeGenered));
                if ($newLives >= 3)
                    $user->setLastGameDatetime(date_create());
                elseif ($lifeGenered <= 2 && $lifeGenered > 0) {
                    $dateLGD = $user->getLastGameDatetime();
                    date_add($dateLGD, date_interval_create_from_date_string('+ ' . $lifeGenered . ' hours'));
                    $user->setLastGameDatetime(date_create(date_format($dateLGD, 'Y-m-d G:i:s')));
                }
                $user->setLives($newLives - 1);
            }
        }

        if ($round->$getStartedDatetimeUser() != null) {
            $secunds_total = $this->common_functions_model->get_time_difference($round->$getStartedDatetimeUser());
            if ($secunds_total > 30) {
                $data['status'] = 'time_out';
                $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
            } else {
                $data['status'] = 'OK';
                $dataWS['secundsActual'] = 30 - $secunds_total;

                $questions = array();
                $questionsNames = array(1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five');

                for ($index = 1; $index <= 5; $index++) {
                    $questionsName = 'getQuestion' . $questionsNames[$index];
                    $questionsName = 'getQuestion' . $questionsNames[$index];

                    $getAnswer = "getAnswer" . $questionsNames[$index] . "User" . $userActual;
                    $getAnswerText = "getAnswerText" . $questionsNames[$index] . "User" . $userActual;
                    $answer = '';
                    $answerText = '';
                    $score = 0;

                    if ($round->$getAnswerText() != null && $round->$getAnswer() != null) {
                        $answer = 'OK';
                        $answerText = utf8_encode(ucfirst(strtolower($round->$getAnswer()->getText())));
                        $score = $round->$getAnswer()->getPoint();
                    } elseif ($round->$getAnswerText() != null) {
                        $answerText = $round->$getAnswerText();
                        $answer = 'INCORRECT';
                    }

                    $questions[] = array('questionId' => $round->$questionsName()->getId(), 'text' => $round->$questionsName()->getText(), 'answer' => $answer, 'answerText' => $answerText, 'score' => $score);
                }
                $dataWS['roundId'] = $round->getId();
                $dataWS['questions'] = $questions;
                $dataWS['letter'] = $round->getLetter();
                $data['data'] = $dataWS;

                $this->response($data, REST_Controller::HTTP_OK); // FOUND (200)
            }
        } else {
            $round->$setStartedDatetimeUser(date_create());
            $battle->setUserTurn($battle->$getUserOpponent());
            $battle->setLastGameDatetime(date_create());

            try {
                $this->user_model->save($user);
                $this->round_model->save($round);
                $this->battle_model->save($battle);
            } catch (Exception $exc) {
                $data['status'] = 'unexpected_error';
                $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302) 
            }

            $data['status'] = 'OK';
            $dataWS['secundsActual'] = 30;

            $questions = array();
            $questionsNames = array(1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five');

            for ($index = 1; $index <= 5; $index++) {
                $questionsName = 'getQuestion' . $questionsNames[$index];
                $questions[] = array('questionId' => $round->$questionsName()->getId(), 'text' => $round->$questionsName()->getText(), 'answer' => '');
            }
            $dataWS['roundId'] = $round->getId();
            $dataWS['questions'] = $questions;
            $dataWS['letter'] = $round->getLetter();
            $data['data'] = $dataWS;
            $this->response($data, REST_Controller::HTTP_OK); // FOUND (200)
        }
    }

    function endTurn_get($roundId) {
        $data = array('status' => null);

        $user = $this->_existsControlUser();
        $round = $this->_existsControlRound($roundId);
        $battle = $this->_existsControlBattle($round->getBattle()->getId(), $user->getId());

        $userActual = ($round->getBattle()->getUserOne()->getId() == $user->getId()) ? "One" : "Two";
        $userOpponent = ($round->getBattle()->getUserTwo()->getId() == $user->getId()) ? "One" : "Two";

        $getStartedDatetimeUser = "getStartedDatetimeUser" . $userActual;
        $setStartedDatetimeUser = "setStartedDatetimeUser" . $userActual;
        $getOpponent = "getUser" . $userOpponent;

        $startedDatetime = $round->$getStartedDatetimeUser();

        if ($startedDatetime == null) {
            $data['status'] = 'unexpected_error';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302) 
        }

        $startedDatetime = date_add($startedDatetime, date_interval_create_from_date_string('-30 second'));
        $newStartedDatetimefecha = DateTime::createFromFormat('Y-m-d H:i:s', $startedDatetime->format('Y-m-d H:i:s'));
        $round->$setStartedDatetimeUser($newStartedDatetimefecha);

        try {
            $this->round_model->save($round);
            $this->turn_model->update_turn_battle($round->getBattle()->getId(), date_create()->format('Y-m-d H:i:s'));

            $userPoints = ($battle->getUserOne()->getId() == $user->getId()) ? "points_user_one" : "points_user_two";
            $data['data'] = array('pointsTotal' => $this->logged_model->get_total_points($round->getId(), $userPoints));

            $data['status'] = 'OK';
            $this->response($data, REST_Controller::HTTP_OK); // FOUND (200)
        } catch (Exception $ex) {
            $data['status'] = 'unexpected_error';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302) 
        }
    }

    function checkWord_post() {
        $data = array('status' => null);
        $dataWS = array();

        $data_post = new stdClass();
        $data_post->roundId = $this->post('roundId');
        $data_post->questionId = $this->post('questionId');
        $data_post->answerText = $this->post('answerText');

        $user = $this->_existsControlUser();
        $round = $this->_existsControlRound($data_post->roundId);
        $battle = $this->_existsControlBattle($round->getBattle()->getId(), $user->getId());

        $userActual = ($round->getBattle()->getUserOne()->getId() == $user->getId()) ? "One" : "Two";
        $getStartedDatetimeUser = "getStartedDatetimeUser" . $userActual;
        if ($round->$getStartedDatetimeUser() != null) {
            $secunds_total = $this->common_functions_model->get_time_difference($round->$getStartedDatetimeUser());
//            if ($secunds_total < 45) {
            try {
                $answer = $this->answer_model->get_answer($round->getId(), $data_post->questionId, $data_post->answerText, $user->getId());
            } catch (Exception $exc) {
                $data['status'] = 'unexpected_error';
                $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
            }

            if ($answer != null) {
                $data['status'] = 'OK';
                $dataWS['questionId'] = $data_post->questionId;
                $dataWS['answerText'] = utf8_encode(ucfirst(strtolower($answer->getText())));
                $dataWS['score'] = $answer->getPoint();
                $data['data'] = $dataWS;
                $this->response($data, REST_Controller::HTTP_OK); // FOUND (200)
            } else {
                $data['status'] = 'incorrect_answer';
                $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
            }
//            } else {
//                $data['status'] = 'time_out';
//                $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
//            }
        } else {
            $data['status'] = 'unexpected_error';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        }
    }

    public function buyAnswer_post() {
        $data = array('status' => null);
        $dataWS = array();

        $data_post = new stdClass();
        $data_post->roundId = $this->post('roundId');
        $data_post->questionId = $this->post('questionId');

        $user = $this->_existsControlUser();
        $round = $this->_existsControlRound($data_post->roundId);
        $battle = $this->_existsControlBattle($round->getBattle()->getId(), $user->getId());
        $question = $this->_existsControlQuestion($data_post->questionId);

        $userActual = ($round->getBattle()->getUserOne()->getId() == $user->getId()) ? "One" : "Two";
        $getStartedDatetimeUser = "getStartedDatetimeUser" . $userActual;

        if ($round->$getStartedDatetimeUser() != null) {
            $secunds_total = $this->common_functions_model->get_time_difference($round->$getStartedDatetimeUser());
//            if ($secunds_total < 35) {
            if ($user->getCoins() >= 3) {
                $answerRamdom = $this->answer_model->get_answer_ramdon($data_post->questionId, $round->getLetter(), $question->getConditionQuery());

                $textNumberAnswer = $this->answer_model->get_text_number_answer($data_post->questionId, $round);

                if ($answerRamdom != null || $textNumberAnswer == "") {
                    $getAnswer = "getAnswer" . $textNumberAnswer . "User" . $userActual;

                    if ($round->$getAnswer() != null) {
                        $answer = $round->$getAnswer();

                        $data['status'] = 'OK';
                        $dataWS['questionId'] = $data_post->questionId;
                        $dataWS['answerText'] = utf8_encode(ucfirst(strtolower($answer->getText())));
                        $dataWS['score'] = $answer->getPoint();
                        $data['data'] = $dataWS;
                        $this->response($data, REST_Controller::HTTP_OK); // FOUND (200)
                    } else {
                        $getAnswer = "getAnswer" . $textNumberAnswer . "User" . $userActual;
                        $setAnswer = "setAnswer" . $textNumberAnswer . "User" . $userActual;
                        $setAnswerText = "setAnswerText" . $textNumberAnswer . "User" . $userActual;
                        $setPointsAnswer = "setPointsAnswer" . $textNumberAnswer . "User" . $userActual;

                        $round->$setAnswerText($answerRamdom->getText());
                        $round->$setAnswer($answerRamdom);
                        $round->$setPointsAnswer($answerRamdom->getPoint());

                        try {
                            $round = $this->round_model->save($round);
                            $user->setCoins($user->getCoins() - 3);
                            $this->user_model->save($user);
                        } catch (Exception $exc) {
                            $data['status'] = 'unexpected_error';
                            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
                        }
                        $data['status'] = 'OK';
                        $dataWS['questionId'] = $data_post->questionId;
                        $dataWS['answerText'] = utf8_encode(ucfirst(strtolower($answerRamdom->getText())));
                        $dataWS['score'] = $answerRamdom->getPoint();
                        $data['data'] = $dataWS;
                        $this->response($data, REST_Controller::HTTP_OK); // FOUND (200)
                    }
                } else {
                    $data['status'] = 'unexpected_error';
                    $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
                }
            } else {
                $data['status'] = 'insufficient_coins';
                $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
            }
            //   } else {
            //     $data['status'] = 'time_out';
            //     $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
            // }
        } else {
            $data['status'] = 'unexpected_error';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        }
    }

    function getDataBattle_get($battleId) {
        $data = array('status' => null);
        $dataWS = array();

        $user = $this->_existsControlUser();
        $battle = $this->_existsControlBattle($battleId, $user->getId());

        if ($battle->getWinner() == null) {
            $data['status'] = 'not_finished';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        }

        $userActual = ($battle->getUserOne()->getId() == $user->getId()) ? "One" : "Two";
        $userOpponent = ($battle->getUserTwo()->getId() == $user->getId()) ? "One" : "Two";

        $getOpponent = "getUser" . $userOpponent;

        $opponent = $battle->$getOpponent();
        $userImage = (($user->getImage() == '' || $user->getImage() == null) && !$this->common_functions_model->url_exists($user->getImage())) ? $this->config->item('url_frontend') . 'Image/image.jpg' : $user->getImage();
        $opponentImage = (($opponent->getImage() == '' || $opponent->getImage() == null) && !$this->common_functions_model->url_exists($opponent->getImage())) ? $this->config->item('url_frontend') . 'Image/image.jpg' : $opponent->getImage();

        $dataWS['marker'] = $this->common_functions_model->get_marker($battle, $user->getId());
        $dataWS['user'] = array('userImage' => $userImage, 'name' => $user->getName());
        $dataWS['opponent'] = array('userImage' => $opponentImage, 'name' => $opponent->getName());

        if ($battle->getWinner()->getId() == $user->getId()) {
            $dataWS['resultGame'] = 'winner';

            $getPointsUser = "getPointsUser" . $userActual;
            $getPointsOpponent = "getPointsUser" . $userOpponent;
            $roundsWinUser = 0;
            $roundsWinOpponet = 0;
            foreach ($battle->getRounds() as $round) {
                if ($round->$getPointsUser() > $round->$getPointsOpponent()) {
                    $roundsWinUser++;
                } elseif ($round->$getPointsUser() < $round->$getPointsOpponent()) {
                    $roundsWinOpponet++;
                } elseif ($round->$getPointsUser() == $round->$getPointsOpponent()) {
                    $roundsWinOpponet++;
                    $roundsWinUser++;
                }
            }

            switch ($roundsWinUser - $roundsWinOpponet) {
                case 1: $experience = 2;
                    break;
                case 2: $experience = 4;
                    break;
                case 3: $experience = 5;
                    break;
                case 4: $experience = 6;
                    break;
                default: $experience = 10;
                    break;
            }

            $dataWS['experience'] = $experience;
            $dataWS['coins'] = $roundsWinUser;
        } else {
            $dataWS['experience'] = 1;
            $dataWS['coins'] = 0;
            $dataWS['resultGame'] = 'loser';
        }

        $dataWS['lastRoundId'] = $this->round_model->get_last_round($battle->getId())->getId();
        $data['data'] = $dataWS;
        $data['status'] = 'OK';
        $this->response($data, REST_Controller::HTTP_OK); // FOUND (200)
    }

    function getDataRound_get($roundId) {
        $data = array('status' => null);
        $dataWS = array();

        $user = $this->_existsControlUser();
        $round = $this->_existsControlRound($roundId);
        $battle = $this->_existsControlBattle($round->getBattle()->getId(), $user->getId());

        $userActual = ($battle->getUserOne()->getId() == $user->getId()) ? "One" : "Two";
        $userOpponent = ($battle->getUserTwo()->getId() == $user->getId()) ? "One" : "Two";

        $getStartedDatetimeUser = "getStartedDatetimeUser" . $userActual;
        $getStartedDatetimeOpponent = "getStartedDatetimeUser" . $userOpponent;
        $getOpponent = "getUser" . $userOpponent;
        $getPointsUser = "getPointsUser" . $userActual;
        $getPointsOpponent = "getPointsUser" . $userOpponent;

        $dataWS['nextRoundId'] = $this->round_model->get_next_round_id($battle, $round->getId());
        $dataWS['backRoundId'] = $this->round_model->get_back_round_id($battle, $round->getId());

        $opponent = $battle->$getOpponent();
        $dataWS['opponent'] = array('name' => $opponent->getName(), 'image' => (($opponent->getImage() == '' || $opponent->getImage() == null) && !$this->common_functions_model->url_exists($opponent->getImage())) ? $this->config->item('url_frontend') . 'Image/image.jpg' : $opponent->getImage());
        $dataWS['user'] = array('totalPoints' => $round->$getPointsUser(), 'name' => $user->getName(), 'image' => (($user->getImage() == '' || $user->getImage() == null) && !$this->common_functions_model->url_exists($user->getImage())) ? $this->config->item('url_frontend') . 'Image/image.jpg' : $user->getImage());
        $dataWS['numberRound'] = $this->common_functions_model->get_number_round($battle->getRounds(), $round->getId());
        $dataWS['marker'] = $this->common_functions_model->get_marker($battle, $user->getId());

        if ($round->$getStartedDatetimeOpponent() != null)
            $dataWS['opponent']['totalPoints'] = $round->$getPointsOpponent();

        $text_questions = array(1 => "One", 2 => "Two", 3 => "Three", 4 => "Four", 5 => "Five");
        $dataWS['questions'] = array();

        foreach ($text_questions as $number => $number_text) {
            $getQuestion = "getQuestion" . $number_text;
            $dataUser = $this->common_functions_model->get_data_show($round, $number_text, $userActual);
            $dataRow = array('dataUser' => $dataUser);
            if ($round->$getStartedDatetimeOpponent() != null) {
                $dataOpponent = $this->common_functions_model->get_data_show($round, $number_text, $userOpponent);
                $dataRow['dataOpponent'] = $dataOpponent;
            }
            $dataRow['textQuestion'] = $round->$getQuestion()->getText();
            $dataRow['questionId'] = $round->$getQuestion()->getId();

            $suggest = $this->answer_model->get_answer_suggest($round->$getQuestion()->getId(), $round->getId(), $user->getId());
            $dataRow['isSent'] = ($suggest != null);

            if ($suggest != null)
                $dataRow['isApproved'] = $suggest->getApproved();

            $dataRow['isLastOne'] = ($number == 5);
            $dataWS['questions'][] = $dataRow;
        }
        $dataWS['battleId'] = $battle->getId();
        $dataWS['finished'] = ($battle->getWinner() != null);

        $data['status'] = 'OK';
        $data['data'] = $dataWS;

        $this->response($data, REST_Controller::HTTP_OK); // FOUND (200)
    }

    function getStatistics_get() {
        $data = array('status' => null);

        $user = $this->_existsControlUser();
        //TODO ANALIZAR ESTA INFO COMO CALCULARLA AHORA POR EL CAMBIO. ADEMAS VER SI INVICCTOS ESTA ANDANDO CREO QNO

        $battles = $this->battle_model->get_battles_closed($user->getId());
        $dataWS['picture'] = (($user->getImage() == '' || $user->getImage() == null) && !$this->common_functions_model->url_exists($user->getImage())) ? $this->config->item('url_frontend') . 'Image/image.jpg' : $user->getImage();
        $dataWS['experience'] = $user->getExperience();
        $dataWS['victories'] = $user->getVictories();
        $dataWS['bestScore'] = $user->getBestScore();
        $dataWS['defeats'] = ($battles == null) ? 0 : (count($battles) - $user->getVictories());
        $dataWS['unbeaten'] = $user->getUnbeaten();

        $data['data'] = $dataWS;
        $data['status'] = 'OK';

        $this->response($data, REST_Controller::HTTP_OK); // FOUND (200)
    }

    function getGlobal_get($offset) {
        $data = array('status' => null);
        $user = $this->_existsControlUser();

        $users = $this->logged_model->get_ranking_global($offset);
        $usersData = array();
        foreach ($users as $us) {
            $userData = array('userId' => $us->getId(), 'userFBId' => $us->getUserIdFb(), 'name' => $us->getName(), 'experience' => $us->getExperience(), 'image' => (($us->getImage() == '' || $us->getImage() == null) && !$this->common_functions_model->url_exists($us->getImage())) ? $this->config->item('url_frontend') . 'Image/image.jpg' : $us->getImage());
            $usersData[] = $userData;
        }

        $data['data'] = array('users' => $usersData);
        $data['status'] = 'OK';

        $this->response($data, REST_Controller::HTTP_OK); // FOUND (200)
    }

    function getFriends_post() {
        $data = array('status' => null);

        $user = $this->_existsControlUser();
        $friendsData = $this->post('data');
        $withMe = ($this->post('withMe') === 'ME');

        $friends = array();
        foreach ($friendsData as $friendData) {
            $opponent = $this->user_model->get_user_fb_id($friendData['id']);

            if ($opponent != null) {
                $friend = array('userId' => $opponent->getId(), 'userFBId' => $friendData['id'], 'name' => $friendData['name'], 'experience' => $opponent->getExperience(), 'image' => $opponent->getImage(), 'play' => false);
                $battle = $this->battle_model->get_battle_with_opponent_active($user->getId(), $opponent->getId());
                if ($battle == null)
                    $friend['play'] = true;
                $friends[] = $friend;
            }
        }

        if (!$withMe)
            $friends[] = array('userId' => $user->getId(), 'userFBId' => $user->getUserIdFb(), 'name' => $user->getName(), 'experience' => $user->getExperience(), 'image' => $user->getImage(), 'play' => false);

        $data['data'] = array('users' => $friends);
        $data['status'] = 'OK';
        $this->response($data, REST_Controller::HTTP_OK); // OK (200)
    }

    public function sendAnswer_post() {
        $data = array('status' => null);

        $data_post = new stdClass();
        $data_post->answer = $this->post('answer');
        $data_post->roundId = $this->post('roundId');
        $data_post->questionId = $this->post('questionId');

        $user = $this->_existsControlUser();
        $round = $this->_existsControlRound($data_post->roundId);
        $question = $this->_existsControlQuestion($data_post->questionId);

        $categorySuggest = new AnswerSuggest();
        $categorySuggest->setText($data_post->answer);
        $categorySuggest->setApproved(false);
        $categorySuggest->setUser($user);
        $categorySuggest->setRound($round);
        $categorySuggest->setQuestion($question);
        try {
            $this->category_model->save($categorySuggest);
        } catch (Exception $exc) {
            $data['status'] = 'unexpected_error';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        }
        $data['status'] = 'OK';
        $this->response($data, REST_Controller::HTTP_OK); // FOUND (200)
    }

    public function sendCategory_post() {
        $data = array('status' => null);
        $user = $this->_existsControlUser();

        $category = $this->post('category');

        if ($category == null || $category == '') {
            $data['status'] = 'category_required';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        } else {
            $categorySuggest = new CategorySuggest();
            $categorySuggest->setName($category);
            $categorySuggest->setUser($user);

            try {
                $this->category_model->save($categorySuggest);
            } catch (Exception $exc) {
                $data['status'] = 'unexpected_error';
                $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
            }

            $data['status'] = 'OK';
            $this->response($data, REST_Controller::HTTP_OK); // FOUND (200)
        }
    }

    public function updatePassword_post() {
        $data = array('status' => null);
        $user = $this->_existsControlUser();

        $password = $this->post('password');
        $passwordNew = $this->post('passwordNew');

        if (($password == null || $password == '') && $user->getPassword() != null) {
            $data['status'] = 'password_required';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        } elseif ($user->getPassword() == null || md5($password) == $user->getPassword()) {
            $user->setPassword(md5($passwordNew));
            try {
                $this->user_model->save($user);
            } catch (Exception $exc) {
                $data['status'] = 'unexpected_error';
                $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
            }

            $data['status'] = 'OK';
            $this->response($data, REST_Controller::HTTP_OK); // FOUND (200)
        } else {
            $data['status'] = 'password_incorrect';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        }
    }

    function _existsControlQuestion($questionId) {
        $data = array('status' => null);
        $question = $this->question_model->load($questionId);
        if (empty($question)) {
            $data['status'] = 'question_not_exist';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        } else
            return $question;
    }

    function _existsControlUser() {
        $data = array('status' => null);
        $userId = $this->session->userdata('userId');
        $user = ($userId != null) ? $this->user_model->load($userId) : null;
        if ($userId == null || $user == null) {
            $data['status'] = 'expired_session';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        }
        return $user;
    }

    function _existsControlBattle($battleId, $userId) {
        $data = array('status' => null);
        $battle = $this->battle_model->load($battleId);
        if (empty($battle) || (!empty($battle) && ($battle->getUserOne()->getId() != $userId && $battle->getUserTwo()->getId() != $userId))) {
            $data['status'] = 'unexpected_error';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        } else
            return $battle;
    }

    function _existsControlRound($roundId) {
        $data = array('status' => null);
        $round = $this->round_model->load($roundId);
        if (empty($round)) {
            $data['status'] = 'unexpected_error';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        } else
            return $round;
    }

}
