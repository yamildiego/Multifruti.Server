<?php

namespace Proxies\__CG__;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class Battle extends \Battle implements \Doctrine\ORM\Proxy\Proxy
{
    private $_entityPersister;
    private $_identifier;
    public $__isInitialized__ = false;
    public function __construct($entityPersister, $identifier)
    {
        $this->_entityPersister = $entityPersister;
        $this->_identifier = $identifier;
    }
    /** @private */
    public function __load()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;

            if (method_exists($this, "__wakeup")) {
                // call this after __isInitialized__to avoid infinite recursion
                // but before loading to emulate what ClassMetadata::newInstance()
                // provides.
                $this->__wakeup();
            }

            if ($this->_entityPersister->load($this->_identifier, $this) === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            unset($this->_entityPersister, $this->_identifier);
        }
    }

    /** @private */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    
    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int) $this->_identifier["id"];
        }
        $this->__load();
        return parent::getId();
    }

    public function setUserOne(\User $userOne = NULL)
    {
        $this->__load();
        return parent::setUserOne($userOne);
    }

    public function getUserOne()
    {
        $this->__load();
        return parent::getUserOne();
    }

    public function setUserTwo(\User $userTwo = NULL)
    {
        $this->__load();
        return parent::setUserTwo($userTwo);
    }

    public function getUserTwo()
    {
        $this->__load();
        return parent::getUserTwo();
    }

    public function setUserTurn(\User $userTurn = NULL)
    {
        $this->__load();
        return parent::setUserTurn($userTurn);
    }

    public function getUserTurn()
    {
        $this->__load();
        return parent::getUserTurn();
    }

    public function setWinner(\User $winner = NULL)
    {
        $this->__load();
        return parent::setWinner($winner);
    }

    public function getWinner()
    {
        $this->__load();
        return parent::getWinner();
    }

    public function setLastGameDatetime($lastGameDatetime)
    {
        $this->__load();
        return parent::setLastGameDatetime($lastGameDatetime);
    }

    public function getLastGameDatetime()
    {
        $this->__load();
        return parent::getLastGameDatetime();
    }

    public function setApprovedUserOne($approvedUserOne)
    {
        $this->__load();
        return parent::setApprovedUserOne($approvedUserOne);
    }

    public function getApprovedUserOne()
    {
        $this->__load();
        return parent::getApprovedUserOne();
    }

    public function setApprovedUserTwo($approvedUserTwo)
    {
        $this->__load();
        return parent::setApprovedUserTwo($approvedUserTwo);
    }

    public function getApprovedUserTwo()
    {
        $this->__load();
        return parent::getApprovedUserTwo();
    }

    public function setCreationDate($creationDate)
    {
        $this->__load();
        return parent::setCreationDate($creationDate);
    }

    public function getCreationDate()
    {
        $this->__load();
        return parent::getCreationDate();
    }

    public function setRounds($rounds)
    {
        $this->__load();
        return parent::setRounds($rounds);
    }

    public function getRounds()
    {
        $this->__load();
        return parent::getRounds();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'lastGameDatetime', 'approvedUserOne', 'approvedUserTwo', 'creationDate', 'userOne', 'userTwo', 'userTurn', 'winner', 'rounds');
    }

    public function __clone()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            $class = $this->_entityPersister->getClassMetadata();
            $original = $this->_entityPersister->load($this->_identifier);
            if ($original === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            foreach ($class->reflFields as $field => $reflProperty) {
                $reflProperty->setValue($this, $reflProperty->getValue($original));
            }
            unset($this->_entityPersister, $this->_identifier);
        }
        
    }
}