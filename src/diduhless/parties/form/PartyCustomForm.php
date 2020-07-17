<?php


namespace diduhless\parties\form;


use diduhless\parties\session\Session;
use jojoe77777\FormAPI\CustomForm;
use pocketmine\player\Player;

abstract class PartyCustomForm extends CustomForm {

    /** @var Session */
    private $session;

    public function __construct(Session $session) {
        $this->session = $session;
        parent::__construct(function(Player $player, ?array $options) {
            $this->setCallback($options);
        });
        $this->onCreation();
    }

    abstract public function onCreation(): void;

    abstract public function setCallback(?array $options): void;

    public function getSession(): Session {
        return $this->session;
    }

}