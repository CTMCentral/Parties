<?php


namespace diduhless\parties\form\presets;


use cosmicpe\form\entries\simple\Button;
use diduhless\parties\form\PartySimpleForm;
use diduhless\parties\party\Invitation;
use diduhless\parties\party\Party;
use diduhless\parties\party\PartyFactory;
use diduhless\parties\session\Session;
use pocketmine\player\Player;

class PublicPartiesForm extends PartySimpleForm {

    /** @var Party[] */
    private $parties;

    public function __construct(Session $session) {
        $parties = PartyFactory::getParties();
        foreach($parties as $party) {
            if($party->isPublic() and !$party->isFull()) {
                $this->parties[] = $party;
                $this->addButton(new Button($party->getLeaderName() . "'s Party"), function(Player $player, int $data) {
                    $session = $this->getSession();
                    $party = array_values($this->parties)[$data];
                    $player->sendForm(new ConfirmInvitationForm(new Invitation($party->getLeader(), $session, $party->getId()), $session));
                });
            }
        }
        $content = empty($this->parties) ? "There are no public parties to join! :(" : "Press on the party you want to join!";
        parent::__construct($session, "Join a public party", $content);
        $this->addButton(new Button("Go Back"), function(Player $player, int $data) {
            $session = $this->getSession();
            $session->openPartyForm();
        });
    }
}