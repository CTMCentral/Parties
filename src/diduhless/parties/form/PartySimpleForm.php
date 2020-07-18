<?php


namespace diduhless\parties\form;


use cosmicpe\form\SimpleForm;
use diduhless\parties\session\Session;

abstract class PartySimpleForm extends SimpleForm {

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