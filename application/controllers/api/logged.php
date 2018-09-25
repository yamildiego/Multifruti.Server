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

        $this->response(array('battlesMyTurn' => $data_battles_my_turn, 'battlesForApproval' => $data_battles_for_approval, 'battlesYourTurn' => $data_battles_your_turn, 'battlesFinished' => $data_battles_finished, 'status' => 'OK'), REST_Controller::HTTP_OK); // OK (200)
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

        $opponentImage = (($opponent->getImage() == '' || $opponent->getImage() == null) && !$this->common_functions_model->url_exists($opponent->getImage())) ? $this->config->item('url_frontend') . 'Image/image.jpg' : $opponent->getImage();
        $userImage = (($user->getImage() == '' || $user->getImage() == null) && !$this->common_functions_model->url_exists($user->getImage())) ? $this->config->item('url_frontend') . 'Image/image.jpg' : $user->getImage();
        $dataBattle = array('id' => $battle->getId(), 'userName' => $user->getName(), 'userImage' => $userImage, 'opponentName' => $opponent->getName(), 'opponentImage' => $opponentImage);
        $data['dataBattle'] = $dataBattle;
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

        $dataBattle = array('id' => $battle->getId(), 'userName' => $user->getName(), 'userImage' => $userImage, 'opponentName' => $opponent->getName(), 'opponentImage' => $opponentImage);
        $data['dataBattle'] = $dataBattle;

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
            $data['status'] = 'no_es_tu_turno';
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
            $data['numberLetter'] = $number_letter;
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
                $data['roundId'] = $round->getId();
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

            $data = array('status' => 'OK', 'roundId' => $round->getId(), 'numberLetter' => $this->common_functions_model->get_number_with_letter($round->getLetter()));
            $this->response($data, REST_Controller::HTTP_OK); // OK (200)
        } else {
            if ($secunds_total < 30) { // si no es null pero el tiempo es menor a 30 segundos permitirle seguir jugando
                //redireccionar con el tiempo restante en el contador
                $data['roundId'] = $nextRound->getId();
                $data['status'] = 'playing';
                $this->response($data, REST_Controller::HTTP_OK); // OK (200)
            } else {//si esta aca es un error redireccionar y tirar error con un codigo
                $data['status'] = 'unexpected_error';
                $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
            }
        }
    }

    public function play_get($roundId) {
        $data = array('status' => null);

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
                $data['secundsActual'] = 30 - $secunds_total;

                $questions = array();
                $questionsNames = array(1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five');

                for ($index = 1; $index <= 5; $index++) {
                    $questionsName = 'getQuestion' . $questionsNames[$index];
                    $questionsName = 'getQuestion' . $questionsNames[$index];

                    $getAnswer = "getAnswer" . $questionsNames[$index] . "User" . $userActual;
                    $getAnswerText = "getAnswerText" . $questionsNames[$index] . "User" . $userActual;
                    $answer = '';
                    $answerText = '';
                    $score = '';

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
                $data['roundId'] = $round->getId();
                $data['questions'] = $questions;
                $data['letter'] = $round->getLetter();

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
            $data['secundsActual'] = 30;

            $questions = array();
            $questionsNames = array(1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five');

            for ($index = 1; $index <= 5; $index++) {
                $questionsName = 'getQuestion' . $questionsNames[$index];
                $questions[] = array('questionId' => $round->$questionsName()->getId(), 'text' => $round->$questionsName()->getText(), 'answer' => '');
            }
            $data['roundId'] = $round->getId();
            $data['questions'] = $questions;
            $data['letter'] = $round->getLetter();

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
            $data['status'] = 'OK';
            $this->response($data, REST_Controller::HTTP_OK); // FOUND (200)
        } catch (Exception $ex) {
            $data['status'] = 'unexpected_error';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302) 
        }
    }

    function checkWord_post() {
        $data = array('status' => null);

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
            if ($secunds_total < 45) {
                try {
                    $answer = $this->answer_model->get_answer($round->getId(), $data_post->questionId, $data_post->answerText, $user->getId());
                } catch (Exception $exc) {
                    $data['status'] = 'unexpected_error';
                    $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
                }

                if ($answer != null) {
                    $data['status'] = 'OK';
                    $data['questionId'] = $data_post->questionId;
                    $data['answerText'] = utf8_encode(ucfirst(strtolower($answer->getText())));
                    $data['score'] = $answer->getPoint();
                    $this->response($data, REST_Controller::HTTP_OK); // FOUND (200)
                } else {
                    $data['status'] = 'incorrect_answer';
                    $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
                }
            } else {
                $data['status'] = 'time_out';
                $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
            }
        } else {
            $data['status'] = 'unexpected_error';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        }
    }

    public function buyAnswer_post() {
        $data = array('status' => null);

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
            if ($secunds_total < 45) {
                if ($user->getCoins() >= 3) {
                    $answerRamdom = $this->answer_model->get_answer_ramdon($data_post->questionId, $round->getLetter(), $question->getConditionQuery());

                    $textNumberAnswer = $this->answer_model->get_text_number_answer($data_post->questionId, $round);

                    if ($answerRamdom != null || $textNumberAnswer == "") {
                        $getAnswer = "getAnswer" . $textNumberAnswer . "User" . $userActual;

                        if ($round->$getAnswer() != null) {
                            $answer = $round->$getAnswer();

                            $data['status'] = 'OK';
                            $data['questionId'] = $data_post->questionId;
                            $data['answerText'] = utf8_encode(ucfirst(strtolower($answer->getText())));
                            $data['score'] = $answer->getPoint();
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
                            $data['questionId'] = $data_post->questionId;
                            $data['answerText'] = utf8_encode(ucfirst(strtolower($answerRamdom->getText())));
                            $data['score'] = $answerRamdom->getPoint();
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
            } else {
                $data['status'] = 'time_out';
                $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
            }
        } else {
            $data['status'] = 'unexpected_error';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        }
    }

    function getDataBattle_get($battleId) {
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

        $data['marker'] = $this->common_functions_model->get_marker($battle, $user->getId());
        $data['user'] = array('userImage' => $userImage, 'name' => $user->getName());
        $data['opponent'] = array('userImage' => $opponentImage, 'name' => $opponent->getName());

        if ($battle->getWinner()->getId() == $user->getId()) {
            $data['resultGame'] = 'winner';

            $getPointsUser = "getPointsUser" . $userActual;
            $getPointsOpponent = "getPointsUser" . $userOpponent;
            $points = 0;
            foreach ($battle->getRounds() as $round)
                $points = $points + $round->$getPointsUser();
            $data['experience'] = $points;
        } else {
            $data['experience'] = 1;
            $data['resultGame'] = 'loser';
        }

        $data['lastRoundId'] = $this->round_model->get_last_round($battle->getId())->getId();
        $data['status'] = 'OK';
        $this->response($data, REST_Controller::HTTP_OK); // FOUND (200)
    }

    function getDataRound_get($roundId) {
        $data = array('status' => null);

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

        $data['nextRoundId'] = $this->round_model->get_next_round_id($battle, $round->getId());
        $data['backRoundId'] = $this->round_model->get_back_round_id($battle, $round->getId());

        $opponent = $battle->$getOpponent();
        $data['opponent'] = array('name' => $opponent->getName(), 'image' => (($opponent->getImage() == '' || $opponent->getImage() == null) && !$this->common_functions_model->url_exists($opponent->getImage())) ? $this->config->item('url_frontend') . 'Image/image.jpg' : $opponent->getImage());
        $data['user'] = array('totalPoints' => $round->$getPointsUser(), 'name' => $user->getName(), 'image' => (($user->getImage() == '' || $user->getImage() == null) && !$this->common_functions_model->url_exists($user->getImage())) ? $this->config->item('url_frontend') . 'Image/image.jpg' : $user->getImage());
        $data['numberRound'] = $this->common_functions_model->get_number_round($battle->getRounds(), $round->getId());
        $data['marker'] = $this->common_functions_model->get_marker($battle, $user->getId());

        if ($round->$getStartedDatetimeOpponent() != null)
            $data['opponent']['totalPoints'] = $round->$getPointsOpponent();

        $text_questions = array(1 => "One", 2 => "Two", 3 => "Three", 4 => "Four", 5 => "Five");
        $data['dataQuestions'] = array();

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
            $data['dataQuestions'][] = $dataRow;
        }
        $data['battleId'] = $battle->getId();
        $data['finished'] = ($battle->getWinner() != null);

        $data['status'] = 'OK';

        $this->response($data, REST_Controller::HTTP_OK); // FOUND (200)
    }

    function getStatistics_get() {
        $data = array('status' => null);

        $user = $this->_existsControlUser();
//TODO ANALIZAR ESTA INFO COMO CALCULARLA AHORA POR EL CAMBIO. ADEMAS VER SI INVICCTOS ESTA ANDANDO CREO QNO

        $battles = $this->battle_model->get_battles_closed($user->getId());
        $statistics['picture'] = (($user->getImage() == '' || $user->getImage() == null) && !$this->common_functions_model->url_exists($user->getImage())) ? $this->config->item('url_frontend') . 'Image/image.jpg' : $user->getImage();
        $statistics['experience'] = $user->getExperience();
        $statistics['victories'] = $user->getVictories();
        $statistics['bestScore'] = $user->getBestScore();
        $statistics['defeats'] = ($battles == null) ? 0 : (count($battles) - $user->getVictories());
        $statistics['unbeaten'] = $user->getUnbeaten();

        $data['statistics'] = $statistics;
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

        $data['users'] = $usersData;
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

        $data['friends'] = $friends;
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

    public function buyExtra_get($packId) {
        $data = array('status' => null);
        $user = $this->_existsControlUser();
//        $packId = $this->post('packId');

        switch ($packId) {
            case 1: $user->setCoins($user->getCoins() + 50);
                break;
            case 2: $user->setCoins($user->getCoins() + 150);
                break;
            case 3: $user->setCoins($user->getCoins() + 300);
                break;
            case 4: $user->setCoins($user->getCoins() + 1200);
                break;
            case 5: $user->setCoins($user->getCoins() + 5000);
                break;
            case 6: $user->setLives(1000);
                break;
            case 7: $user->setLives($user->getLives() + 50);
                break;
            case 8: $user->setLives($user->getLives() + 30);
                break;
            case 9: $user->setLives($user->getLives() + 10);
                break;
            case 10: $user->setLives($user->getLives() + 5);
                break;
            case 11: $user->setIsPremium(true);
                break;
//            case 12: $user->setCoins($user->getCoins() + 150);
//                break;
        }

//        try {
//            $this->user_model->save($user);
//            $data['status'] = 'OK';
//            $this->response($data, REST_Controller::HTTP_OK); // FOUND (200)
//        } catch (Exception $exc) {
//            $data['status'] = 'unexpected_error';
//            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
//        }
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

    function _existsControlQuestion($questionId) {
        $data = array('status' => null);
        $question = $this->question_model->load($questionId);
        if (empty($question)) {
            $data['status'] = 'question_not_exist';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        } else
            return $question;
    }

    function _existsControlRound($roundId) {
        $data = array('status' => null);
        $round = $this->round_model->load($roundId);
        if (empty($round)) {
            $data['status'] = 'round_not_exist';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        } else
            return $round;
    }

    function _existsControlBattle($battleId, $userId) {
        $data = array('status' => null);
        $battle = $this->battle_model->load($battleId);
        if (empty($battle) || (!empty($battle) && ($battle->getUserOne()->getId() != $userId && $battle->getUserTwo()->getId() != $userId))) {
            $data['status'] = 'game_not_exist';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        } else
            return $battle;
    }

}
