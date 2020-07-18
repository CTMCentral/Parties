<?php


namespace diduhless\parties\form;


use cosmicpe\form\ModalForm;
use diduhless\parties\session\Session;

abstract class PartyModalForm extends ModalForm {

    /** @var Session */
    private $session;

    public function __construct(Session $session, string $title, ?string $content) {
        $this->session = $session;
        parent::__construct($title, $content);
    }

    public function getSession(): Session {
        return $this->session;
    }

}