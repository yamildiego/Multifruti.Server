<?php

require APPPATH . 'libraries/REST_Controller.php';

class FB extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('payment_model');
    }

    public function getRequestId_get($packId = null) {
        $data = array('status' => null);
        $user = $this->_existsControlUser();

        if ($packId == null || $packId < 0 || $packId > 11) {
            $data['status'] = 'unexpected_error';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        }

        $payment = new Payment();
        $payment->setUser($user);
        $payment->setPackId($packId);
        $payment->setStatus('new');

        try {
            $payment = $this->payment_model->save($payment);
            $data['requestId'] = $payment->getId();
            $data['status'] = 'OK';
            $this->response($data, REST_Controller::HTTP_OK); // FOUND (200)
        } catch (Exception $exc) {
            $data['status'] = 'unexpected_error';
            $this->response($data, REST_Controller::HTTP_FOUND); // FOUND (302)
        }
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
