<?php


namespace diduhless\parties\form\presets;


use diduhless\parties\form\PartyModalForm;
use diduhless\parties\party\Invitation;
use diduhless\parties\session\Session;
use pocketmine\player\Player;

class ConfirmInvitationForm extends PartyModalForm {

    /** @var Invitation */
    private $invitation;

    public function __construct(Invitation $invitation, Session $session) {
        $this->invitation = $invitation;
        parent::__construct($session, "Join a party", "Do you want to join this party?");
        $this->setFirstButton("Yes");
        $this->setSecondButton("No");
    }

    public function onAccept(Player $player): void {
        $this->invitation->attemptToAccept();
    }

    public function onClose(Player $player): void {
        $this->invitation->getTarget()->removeInvitation($this->invitation);
    }
}