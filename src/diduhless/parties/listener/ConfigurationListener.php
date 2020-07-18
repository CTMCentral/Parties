<?php


namespace diduhless\parties\listener;


use diduhless\parties\session\SessionFactory;
use diduhless\parties\utils\ConfigGetter;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerTransferEvent;
use pocketmine\player\Player;
use pocketmine\Server;

class ConfigurationListener implements Listener {

    public function onFight(EntityDamageByEntityEvent $event): void {
        $entity = $event->getEntity();
        $damager = $event->getDamager();

        if(ConfigGetter::isPvpDisabled() and $entity instanceof Player and $damager instanceof Player and SessionFactory::hasSession($damager)) {
            $session = SessionFactory::getSession($damager);

            if($session->hasParty() and $session->getParty()->hasMemberByName($entity->getName())) {
                $event->setCancelled();
            }
        }
    }

    public function onLevelChange(EntityTeleportEvent $event): void {
    	if($event->getTo()->getWorld()->getId() === $event->getFrom()->getWorld()->getId())
    		return;

        $player = $event->getEntity();
        if(ConfigGetter::isWorldTeleportEnabled() and $player instanceof Player and SessionFactory::hasSession($player)) {
            $session = SessionFactory::getSession($player);

            if($session->isPartyLeader()) {
                foreach($session->getParty()->getMembers() as $member) {
                    $member->getPlayer()->teleport($event->getTo()->getWorld()->getSafeSpawn());
                }
            }
        }
    }

    public function onTransfer(PlayerTransferEvent $event): void {
        $player = $event->getPlayer();
        if(ConfigGetter::isTransferTeleportEnabled() and SessionFactory::hasSession($player)) {
            $session = SessionFactory::getSession($player);

            if($session->isPartyLeader()) {
                foreach($session->getParty()->getMembers() as $member) {
                    $member->getPlayer()->transfer($event->getAddress(), $event->getPort(), $event->getMessage());
                }
            }
        }
    }

    public function onCommandPreprocess(PlayerCommandPreprocessEvent $event): void {
        $player = $event->getPlayer();
        $commandLine = str_replace("/", "", $event->getMessage());

        if(ConfigGetter::areLeaderCommandsEnabled() and in_array($commandLine, ConfigGetter::getSelectedCommands()) and SessionFactory::hasSession($player)) {
            $session = SessionFactory::getSession($player);

            if($session->isPartyLeader()) {
                foreach($session->getParty()->getMembers() as $member) {
                    Server::getInstance()->dispatchCommand($member->getPlayer(), $commandLine);
                }
            }
        }
    }

}