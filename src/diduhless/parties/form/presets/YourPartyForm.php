<?php


namespace diduhless\parties\form\presets;


use cosmicpe\form\entries\simple\Button;
use diduhless\parties\event\PartyDisbandEvent;
use diduhless\parties\event\PartyLeaveEvent;
use diduhless\parties\form\PartySimpleForm;
use diduhless\parties\party\PartyFactory;
use diduhless\parties\session\Session;
use pocketmine\player\Player;

class YourPartyForm extends PartySimpleForm {

    public function __construct(Session $session) {
        parent::__construct($session, "Your FriendsList", "What do you want to check?");
        $this->addButton(new Button("Members"), function(Player $player, int $data) {
            $session = $this->getSession();
            if(!$session->hasParty())
                return;
            $player->sendForm(new PartyMembersForm($session));
        });
        if($this->getSession()->isPartyLeader()) {
            $this->addButton(new Button("Invite a player"), function(Player $player, int $data) {
                $session = $this->getSession();
                if(!$session->hasParty())
                    return;
                $player->sendForm(new PartyInviteForm($session));
            });
            $this->addButton(new Button("FriendsList Options"), function(Player $player, int $data) {
                $session = $this->getSession();
                if(!$session->hasParty())
                    return;
                $player->sendForm(new PartyOptionsForm($session));
            });
            $this->addButton(new Button("Disband the party"), function(Player $player, int $data) {
	            $this->disbandParty();
            });
        } else {
            $this->addButton(new Button("Leave the party"), function(Player $player, int $data) {
                $session = $this->getSession();
                if(!$session->hasParty())
                    return;
                $this->leaveParty();
            });
        }
    }

    private function disbandParty(): void {
        $session = $this->getSession();
        $party = $session->getParty();

        $event = new PartyDisbandEvent($party, $session);
        $event->call();
        if($event->isCancelled()) return;

        foreach($party->getMembers() as $member) {
            $party->remove($member);
            PartyFactory::removeParty($party);
        }
    }

    private function leaveParty(): void {
        $session = $this->getSession();
        $party = $session->getParty();

        $event = new PartyLeaveEvent($party, $session);
        $event->call();
        if(!$event->isCancelled()) {
            $party->remove($session);
        }
    }

}