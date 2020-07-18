<?php


namespace diduhless\parties\form;


use cosmicpe\form\CustomForm;
use diduhless\parties\session\Session;

abstract class PartyCustomForm extends CustomForm {

    /** @var Session */
    private $session;

    public function __construct(Session $session, string $title) {
        $this->session = $session;
        parent::__construct($title);
    }

    public function getSession(): Session {
        return $this->session;
    }

}