<?php


namespace diduhless\parties\form\presets;


use cosmicpe\form\entries\simple\Button;
use diduhless\parties\form\PartySimpleForm;
use diduhless\parties\session\Session;
use pocketmine\player\Player;

class PartyMembersForm extends PartySimpleForm {

    /** @var Session[] */
    private $members;

    public function __construct(Session $session) {
        parent::__construct($session, "FriendsList Members", "Current members in your party:");

        $session = $this->getSession();
        $members = $session->getParty()->getMembers();

        unset($members[array_search($session, $members, true)]);
        array_unshift($members, $session);

        $this->members = $members;
        foreach($this->members as $member) {
            $this->addButton(new Button($member->getUsername()), function(Player $player, int $data) {
                $session = $this->getSession();
                if(!$session->hasParty())
                    return;

                $member = $this->members[$data];

                if($session->isPartyLeader() and !$member->isPartyLeader()) {
                    $player->sendForm(new PartyMemberForm($member, $session));
                } else {
                    $player->sendForm(new PartyMembersForm($session));
                }
            });
        }
    }
}