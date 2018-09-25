<?php

require_once(APPPATH . "models/Entidades/models.php");

class User_model extends CI_Model {

    var $em;

    function __construct() {
        parent::__construct();
        $this->em = $this->doctrine->em;
    }

    function save($user) {
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    function remove($user) {
        $this->em->remove($user);
        $this->em->flush();
    }

    function load($user_id) {
        return $this->em->find('User', $user_id);
    }

    function get_by_username_password($data) {
        $query = $this->em->getRepository('User')
                ->createQueryBuilder('u');
        $query->where('u.password = :password')
                ->setParameter('password', $data->password)
                ->andWhere('u.email= :email')
                ->setParameter('email', $data->email);

        return $query->getQuery()->getOneOrNullResult();
    }

    function get_by_email($email) {
        $query = $this->em->getRepository('User')
                ->createQueryBuilder('u')
                ->where('u.email = :email')
                ->setParameter('email', $email);

        return $query->getQuery()->getOneOrNullResult();
    }

    function verify_code_password($userId, $codePassword) {
        $query = $this->em->getRepository('User')
                ->createQueryBuilder('u');

        $query->where('u.id = :id')
                ->setParameter('id', $userId)
                ->andWhere('u.codePassword = :codePassword')
                ->setParameter('codePassword', $codePassword);

        return $query->getQuery()->getOneOrNullResult();
    }

    function get_user_fb_id($userFbId) {
        $query = $this->em->getRepository('User')
                ->createQueryBuilder('u');

        $query->where('u.userIdFb = :userFbId')
                ->setParameter('userFbId', $userFbId);

        return $query->getQuery()->getOneOrNullResult();
    }

}
