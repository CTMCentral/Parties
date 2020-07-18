<?php


namespace diduhless\parties\form\presets;


use cosmicpe\form\entries\custom\CustomFormEntry;
use cosmicpe\form\entries\custom\LabelEntry;
use cosmicpe\form\entries\custom\SliderEntry;
use cosmicpe\form\entries\custom\ToggleEntry;
use diduhless\parties\event\PartySetPrivateEvent;
use diduhless\parties\event\PartySetPublicEvent;
use diduhless\parties\event\PartyUpdateSlotsEvent;
use diduhless\parties\form\PartyCustomForm;
use diduhless\parties\session\Session;
use diduhless\parties\utils\ConfigGetter;
use pocketmine\player\Player;

class PartyOptionsForm extends PartyCustomForm {

    public function __construct(Session $session) {
        $party = $this->getSession()->getParty();
        parent::__construct($session, "Party Options");
        $this->addEntry(new LabelEntry("Change the party options in this window."));
        $this->addEntry(new ToggleEntry("Do you want to set your party public?", $party->isPublic()), function(Player $player, CustomFormEntry $entry, $data) {
            $session = $this->getSession();
            if(!$session->hasParty())
                return;
            $party = $session->getParty();

            if($data) {
                $event = new PartySetPublicEvent($party, $session);
                $event->call();
                if(!$event->isCancelled())
                    $party->setPublic(true);
            } else {
                $event = new PartySetPrivateEvent($party, $session);
                $event->call();
                if(!$event->isCancelled())
                    $party->setPublic(false);
            }
        });
        $this->addEntry(new SliderEntry("Set your maximum party slots", 1, ConfigGetter::getMaximumSlots(), 1, $party->getSlots()), function(Player $player, CustomFormEntry $entry, $data) {
            $session = $this->getSession();
            if(!$session->hasParty())
                return;
            $party = $session->getParty();

            $event = new PartyUpdateSlotsEvent($party, $session, $data);
            $event->call();
            if(!$event->isCancelled())
                $party->setSlots($data);
        });
    }

}