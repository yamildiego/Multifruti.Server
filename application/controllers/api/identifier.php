<?php

require APPPATH . 'libraries/REST_Controller.php';

class identifier extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('battle_model');
        $this->load->model('common_functions_model');
    }

    public function getLoginStatus_get() {
        $data = array('status' => null);

        if ($this->session->userdata('logout') === 'no-logged') {
            $data['status'] = 'no-logged';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        } else
            $user = $this->_existsControlUser();

        $data['userFB'] = array(
            'userId' => $user->getId(),
            'id' => $user->getUserIdFb(),
            'first_name' => utf8_encode($user->getName()),
            'last_name' => utf8_encode($user->getLastName()),
            'is_premium' => $user->getIsPremium(),
            'picture' => array('data' => array('url' => utf8_encode($user->getImage()))),
            'cover' => array('source' => utf8_encode($user->getCover())));

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

        $data['dataUser'] = array('lives' => $lives, 'timer' => $timer, 'coins' => $user->getCoins());
        $data['status'] = 'OK';

        $this->response($data, REST_Controller::HTTP_OK); // OK (200) 
    }

    function truncate($val, $f = "0") {
        if (($p = strpos($val, '.')) !== false)
            $val = floatval(substr($val, 0, $p + 1 + $f));
        return $val;
    }

    public function updateInfo_post() {
        $data_post = new stdClass();
        $data_post->id = $this->post('id');
        $data_post->first_name = $this->post('first_name');
        $data_post->last_name = $this->post('last_name');
        $data_post->picture = $this->post('picture');
        $data_post->cover = $this->post('cover');
        $data_post->email = $this->post('email');

        $this->session->unset_userdata('logout');
        $user = $this->user_model->get_user_fb_id($data_post->id);
        if ($user == null) { // si no hay usuario con ese id de fb jamas se logeo con facebook
            $user = $this->user_model->get_by_email($data_post->email);
            if ($user == null) { // si el email no existe en la db el usuario es completamente nuevo
                $user = new User();
                $user->setPassword(null);
                $user->setLives(3);
                $user->setLastGameDatetime(date_create());
                $user->setCodePassword(null);
                $user->setRequestPassword(null);
                $user->setCoins(40);
                $user->setExperience(0);
                $user->setVictories(0);
            } else
                $user->setCoins($user->getCoins() + 40);
        }

        $user->setName($data_post->first_name);
        $user->setLastName($data_post->last_name);
        if ($data_post->picture != null && is_array($data_post->picture) && isset($data_post->picture['data']) && isset($data_post->picture['data']['url']))
            $user->setImage($data_post->picture['data']['url']);
        if ($data_post->cover != null && is_array($data_post->cover) && isset($data_post->cover['source']))
            $user->setCover($data_post->cover['source']);
        $user->setEmail($data_post->email);
        $user->setUserIdFb($data_post->id);

        try {
            $userUpdated = $this->user_model->save($user);
        } catch (Exception $exc) {
            $errors[] = array('text' => "[I105] Ocurrio un error. Contacte al administrador.", 'type' => 'danger');
            $this->response($errors, REST_Controller::HTTP_FOUND); // FOUND (302)
        }
        $userId = $this->session->userdata('userId');
        if ($userId == null)
            $userId = $this->session->set_userdata('userId', $userUpdated->getId());
        elseif ($user->getId() != $this->session->userdata('userId'))
            $this->session->set_userdata('userId', $user->getId());

        $this->getLoginStatus_get();
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

}
