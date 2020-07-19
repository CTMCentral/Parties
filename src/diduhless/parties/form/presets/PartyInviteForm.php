<?php


namespace diduhless\parties\form\presets;


use cosmicpe\form\entries\custom\CustomFormEntry;
use cosmicpe\form\entries\custom\DropdownEntry;
use cosmicpe\form\entries\custom\InputEntry;
use diduhless\parties\event\PartyInviteEvent;
use diduhless\parties\form\PartyCustomForm;
use diduhless\parties\party\Invitation;
use diduhless\parties\session\Session;
use diduhless\parties\session\SessionFactory;
use pocketmine\player\Player;

class PartyInviteForm extends PartyCustomForm {

    /** @var Session[] */
    private $sessions = [];

    public function __construct(Session $session) {
        parent::__construct($session, "Invite a player");
        $this->addEntry(new InputEntry("Write the name of the player:"), function(Player $player, CustomFormEntry $entry, $data) {
            $this->attemptToInvite($data);
        });

        $usernames = [];
        foreach(SessionFactory::getSessions() as $session) {
            if(!$session->hasParty()) {
                $usernames[] = $session->getUsername();
                $this->sessions[] = $session;
            }
        }
        if(!empty($this->sessions)) {
            $this->addEntry(new DropdownEntry("Select an online player:", ...$usernames), function(Player $player, CustomFormEntry $entry, $data) use($usernames) {
                $this->attemptToInvite($usernames[$data]);
            });
        }
    }

    private function attemptToInvite(string $username): void {
        $session = $this->getSession();
        $target = SessionFactory::getSessionByName($username);

        if($target === null) {
            $session->message("{RED}The player {WHITE}$username {RED}is not online!");
        } elseif($target->hasParty()) {
            $session->message($target->getUsername() . " {RED}is already on a party!");
        } elseif($target->hasSessionInvitation($session)) {
            $session->message("{RED}You have already invited {WHITE}" . $target->getUsername() . " {RED}to your party!");
        } elseif(!$this->isCancelled($target)) {
            $this->sendInvitation($target);
        }
    }

    private function isCancelled(Session $target): bool {
        $session = $this->getSession();
        $event = new PartyInviteEvent($session->getParty(), $session, $target);
        $event->call();
        return $event->isCancelled();
    }

    private function sendInvitation(Session $target): void {
        $session = $this->getSession();
        $target->addInvitation(new Invitation($session, $target, $session->getParty()->getId()));
    }

}