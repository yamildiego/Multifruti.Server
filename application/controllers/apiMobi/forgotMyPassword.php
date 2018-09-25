<?php

require APPPATH . 'libraries/REST_Controller.php';

class forgotMyPassword extends REST_Controller {

    function __construct() {
        parent::__construct();
    }

    public function forgotMyPassword_post() {
        $data = array('status' => false);

        $data_post = new stdClass();
        $data_post->email = $this->post('email');

        if ($data_post->email == '' || $data_post->email == null) {
            $data = array('status' => 'email_required');
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302) 
        } elseif (!filter_var($data_post->email, FILTER_VALIDATE_EMAIL)) {
            $data = array('status' => 'email_invalid');
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302) 
        } else {
            $user = $this->user_model->get_by_email($data_post->email);
            if ($user == null) {
                $data = array('status' => 'email_not_registered');
                $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302) 
            }
        }

        $user = $this->user_model->get_by_email($data_post->email);
        $code = md5($user->getEmail() . date('siGdmY') . $user->getName());
        $user->setCodePassword($code);
        $user->setRequestPassword(date_create());
        $statusEmail = false;

        try {
            $user = $this->user_model->save($user);
            $statusEmail = $this->_send_email($this->config->item('email_noreply'), $data_post->email, $this->load->view('basic/email/emailRecoveryPassword_view', array('user' => $user, 'url' => $this->config->item('url_frontend') . '#!/resetPassword/' . $user->getId() . '/' . $code), true), 'Solicitud para restablecer la contraseña.');
        } catch (Exception $exc) {
            $data = array('status' => 'unexpected_error');
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302) 
        }

        if ($statusEmail) {
            $data = array('status' => 'OK');
            $this->response($data, REST_Controller::HTTP_CREATED); // OK (201) 
        } else {
            $data = array('status' => 'unexpected_error');
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302) 
        }
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
