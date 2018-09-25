<?php

require APPPATH . 'libraries/REST_Controller.php';

class createAccount extends REST_Controller {

    function __construct() {
        parent::__construct();
    }

    public function createAccount_post() {
        $userNew = new User();
        $userNew->setName($this->post('name'));
        $userNew->setLastName($this->post('lastName'));
        $userNew->setEmail($this->post('email'));
        $password = ($this->post('password') == '' || $this->post('password') == null) ? null : $this->post('password');
        $userNew->setPassword($password);

        if ($userNew->getName() == '' || $userNew->getName() == null) {
            $data = array('status' => 'name_required');
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        }

        if ($userNew->getEmail() == '' || $userNew->getEmail() == null) {
            $data = array('status' => 'lastName_required');
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        } elseif (!filter_var($userNew->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $data = array('status' => 'invalid_email');
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        } else {
            $user = $this->user_model->get_by_email($userNew->getEmail());
            if ($user != null) {
                if ($user->getUserIdFb() == null || $user->getUserIdFb() == '') {
                    $data = array('status' => 'exist_email');
                    $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
                } else {
                    $data = array('status' => 'exist_Fb_email');
                    $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
                }
            }
        }

        if ($userNew->getPassword() == '' || $userNew->getPassword() == null) {
            $data = array('status' => 'password_required');
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        } elseif (strlen($userNew->getPassword()) < 5) {
            $data = array('status' => 'min_password');
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        } else
            $userNew->setPassword(md5($userNew->getPassword()));

        $userNew->setLives(3);
        $userNew->setLastGameDatetime(date_create());
        $userNew->setCodePassword(null);
        $userNew->setRequestPassword(null);
        $userNew->setCoins(20);
        $userNew->setExperience(0);
        $userNew->setVictories(0);

        try {
            $user = $this->user_model->save($userNew);
        } catch (Exception $ex) {
            $data = array('status' => 'unexpected_error');
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302) 
        }

        $data = array('status' => 'OK');
        $this->response($data, REST_Controller::HTTP_CREATED); // OK (201) 
    }

    function _send_email($p_email_from, $p_email_to, $p_message, $p_subject) {
        $this->load->library('email');
        $this->email->from($p_email_from, $this->config->item('name_company'));
        $this->email->to($p_email_to);
        $this->email->subject($p_subject);
        $this->email->message($p_message);
        return $this->email->send();
    }

}
