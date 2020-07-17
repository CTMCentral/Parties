<?php


namespace diduhless\parties\party;


use diduhless\parties\session\SessionFactory;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class PartyItem extends Item {

    public function __construct() {
        $this->setCustomName(TextFormat::GREEN . "Party");
        parent::__construct(new ItemIdentifier(ItemIds::HEART_OF_THE_SEA, 0), "Party");
    }

    public function onClickAir(Player $player, Vector3 $directionVector): ItemUseResult {
        if(SessionFactory::hasSession($player)) {
            SessionFactory::getSession($player)->openPartyForm();
        }
        return ItemUseResult::NONE();
    }

}