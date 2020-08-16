<?php


namespace diduhless\parties\form;


use diduhless\parties\event\PartyInviteEvent;
use diduhless\parties\party\Invitation;
use diduhless\parties\session\Session;
use diduhless\parties\session\SessionFactory;
use diduhless\parties\utils\StoresSession;
use EasyUI\element\Dropdown;
use EasyUI\element\Input;
use EasyUI\element\Option;
use EasyUI\utils\FormResponse;
use EasyUI\variant\CustomForm;
use pocketmine\Player;

class PartyInviteForm extends CustomForm {
    use StoresSession;

    public function __construct(Session $session) {
        $this->session = $session;
        parent::__construct("Invite a player");
    }

    protected function onCreation(): void {
        $dropdown = new Dropdown("Select an online player:");
        foreach(SessionFactory::getSessions() as $session) {
            if(!$session->hasParty()) {
                $dropdown->addOption(new Option($session->getUsername(), $session->getUsername()));
            }
        }
        $this->addElement("input_player", new Input("Write the name of the player:"));
        $this->addElement("dropdown_player", $dropdown);
    }

    protected function onSubmit(Player $player, FormResponse $response): void {
        $input_username = $response->getInputSubmittedText("input_player");
        $dropdown_username = $response->getDropdownSubmittedOptionId("dropdown_player");

        $username = $input_username ?? $dropdown_username ?? null;
        if($username === null) {
            return;
        }

        $target = SessionFactory::getSessionByName($username);
        if($target === null) {
            $this->session->message("{RED}The player {WHITE}$username {RED}is not online!");
        } elseif($target->hasParty()) {
            $this->session->message($target->getUsername() . " {RED}is already on a party!");
        } elseif($target->hasSessionInvitation($this->session)) {
            $this->session->message("{RED}You have already invited {WHITE}" . $target->getUsername() . " {RED}to your party!");
        } elseif(!$this->isCancelled($target)) {
            $target->addInvitation(new Invitation($this->session, $target, $this->session->getParty()->getId()));
        }
    }

    private function isCancelled(Session $target): bool {
        $session = $this->getSession();
        $event = new PartyInviteEvent($session->getParty(), $session, $target);
        $event->call();
        return $event->isCancelled();
    }


}