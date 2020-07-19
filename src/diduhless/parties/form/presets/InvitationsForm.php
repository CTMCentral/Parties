<?php


namespace diduhless\parties\form\presets;


use cosmicpe\form\entries\simple\Button;
use diduhless\parties\form\PartySimpleForm;
use diduhless\parties\party\Invitation;
use diduhless\parties\session\Session;
use pocketmine\player\Player;

class InvitationsForm extends PartySimpleForm {

    /** @var Invitation[] */
    private $invitations;

    public function __construct(Session $session) {
        $this->invitations = $session->getInvitations();
        $content = empty($this->invitations) ? "You do not have any invitations! :(" : "These are your party invitations:";
        parent::__construct($session, "Party Invitations", $content);
        foreach($this->invitations as $invitation) {
            $this->addButton(new Button($invitation->getSender()->getUsername() . "'s Party"), function(Player $player, int $data) {
                $session = $this->getSession();
                $player->sendForm(new ConfirmInvitationForm(array_values($this->invitations)[$data], $session));
            });
        }
        $this->addButton(new Button("Go Back"), function(Player $player, int $data) {
            $session = $this->getSession();
            $session->openPartyForm();
        });
    }
}