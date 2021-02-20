<?php


namespace diduhless\parties\form\presets;


use cosmicpe\form\entries\simple\Button;
use diduhless\parties\event\PartyLeaderPromoteEvent;
use diduhless\parties\event\PartyMemberKickEvent;
use diduhless\parties\form\PartySimpleForm;
use diduhless\parties\session\Session;
use diduhless\parties\session\SessionFactory;
use pocketmine\player\Player;

class PartyMemberForm extends PartySimpleForm {

    /** @var Session */
    private $member;

    public function __construct(Session $member, Session $session) {
        $this->member = $member;
        parent::__construct($session, "FriendsList Member", "What do you want to do with this member?");
        $this->addButton(new Button("Kick him from the party"), function(Player $player, int $data) {
            $session = $this->getSession();
            if(!$session->isPartyLeader() or SessionFactory::getSession($this->member->getPlayer()) === null)
                return;
            $this->onKick();
        });
        $this->addButton(new Button("Promote to party leader"), function(Player $player, int $data) {
            $session = $this->getSession();
            if(!$session->isPartyLeader() or SessionFactory::getSession($this->member->getPlayer()) === null)
                return;
            $this->onPromote();
        });
    }

    private function onKick(): void {
        $session = $this->getSession();
        $party = $session->getParty();

        $event = new PartyMemberKickEvent($party, $session, $this->member);
        $event->call();

        if(!$event->isCancelled()) {
            $party->remove($this->member);
        }
    }

    private function onPromote(): void {
        $session = $this->getSession();
        $party = $session->getParty();

        $event = new PartyLeaderPromoteEvent($party, $session, $this->member);
        $event->call();

        if(!$event->isCancelled()) {
            $party->setLeader($this->member);
        }
    }
}