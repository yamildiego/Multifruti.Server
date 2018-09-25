<?php

require APPPATH . 'libraries/REST_Controller.php';

class login extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('battle_model');
        $this->load->model('common_functions_model');
    }

    public function login_post() {
        $data = array('status' => false);

        $this->session->unset_userdata('logout');
        $data_post = new stdClass();
        $data_post->email = $this->post('email');
        $data_post->password = ($this->post('password') == '' || $this->post('password') == null) ? null : md5($this->post('password'));

        if ($data_post->email == '' || $data_post->email == null) {
            $data = array('status' => 'email_required');
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        }

        if ($data_post->password == '' || $data_post->password == null) {
            $data = array('status' => 'password_required');
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        }

        $user = $this->user_model->get_by_username_password($data_post);

        if ($user == null) {
            $data = array('status' => 'data_incorrect');
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        } else {
            $this->session->set_userdata('userId', $user->getId());

            $lives = $user->getLives();
            $timer = 0;

            $constantMaxLife = 3;

            if ($user->getLastGameDatetime() != null && $user->getLives() < $constantMaxLife && $lives != 1000) {
                $secondTotal = $this->common_functions_model->get_time_difference_negative($user->getLastGameDatetime());
                $lifeGenered = $this->_truncate($secondTotal / 3600);
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
    }

    function _truncate($val, $f = "0") {
        if (($p = strpos($val, '.')) !== false)
            $val = floatval(substr($val, 0, $p + 1 + $f));
        return $val;
    }

}
