<?php


namespace diduhless\parties\form\presets;


use cosmicpe\form\entries\simple\Button;
use diduhless\parties\event\PartyCreateEvent;
use diduhless\parties\form\PartySimpleForm;
use diduhless\parties\party\Party;
use diduhless\parties\party\PartyFactory;
use diduhless\parties\session\Session;
use pocketmine\player\Player;

class PartyMenuForm extends PartySimpleForm {

    public function __construct(Session $session) {
        parent::__construct($session, "Party Menu", "You do not have a party! Create a party or accept an invitation to join a party.");
        $this->addButton(new Button("Create a party"), function(Player $player, int $data) {
            $this->onPartyCreate();
        });
        $this->addButton(new Button("Join a public party"), function(Player $player, int $data) {
            $this->onOpenPublicParties();
        });
        $this->addButton(new Button("Invitations [" . count($this->getSession()->getInvitations()) . "]"), function(Player $player, int $data) {
            $this->onOpenInvitations();
        });
    }

    private function onPartyCreate(): void {
        $session = $this->getSession();
        $party = new Party(uniqid(), $session);
        $event = new PartyCreateEvent($party, $session);

        $event->call();
        if(!$event->isCancelled()) {
            $party->add($session);
            PartyFactory::addParty($party);
            $session->openPartyForm();
        }
    }

    private function onOpenPublicParties(): void {
        $session = $this->getSession();
        $session->getPlayer()->sendForm(new PublicPartiesForm($session));
    }

    private function onOpenInvitations(): void {
        $session = $this->getSession();
        $session->getPlayer()->sendForm(new InvitationsForm($session));
    }
}