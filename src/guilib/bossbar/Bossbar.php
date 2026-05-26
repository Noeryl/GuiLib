<?php

declare(strict_types = 1);

namespace guilib\bossbar;

use pocketmine\player\Player;
use pocketmine\network\mcpe\protocol\BossEventPacket;
use pocketmine\network\mcpe\protocol\types\BossBarColor;

abstract class Bossbar{

    /** @var array<string, string> */
    private static array $players = [];

    public static function send(Player $player, self $bossbar) : void{
        if(self::get($player) !== null){
            self::remove($player);
        }
        self::$players[$player->getName()] = $bossbar->getName();

        $packet = new BossEventPacket();
        $packet->bossActorUniqueId = $player->getId();
        $packet->eventType = BossEventPacket::TYPE_SHOW;
        $packet->title = $title = $bossbar->getTitle($player);
        $packet->filteredTitle = $title;
        $packet->healthPercent = $bossbar->getHealth($player) / 100;
        $packet->color = $bossbar->getColor($player);
        $packet->darkenScreen = false;
        $packet->overlay = 0;
        $player->getNetworkSession()->sendDataPacket($packet);
    }

    public static function remove(Player $player) : void{
        $packet = new BossEventPacket();
        $packet->bossActorUniqueId = $player->getId();
        $packet->eventType = BossEventPacket::TYPE_HIDE;
        $player->getNetworkSession()->sendDataPacket($packet);

        unset(self::$players[$player->getName()]);
    }

    public static function get(Player $player) : ?string{
        return self::$players[$player->getName()] ?? null;
    }

    abstract public function getName() : string;

    abstract public function getTitle(Player $player) : string;

    public function getHealth(Player $player) : float{
        return 100;
    }

    public function getColor(Player $player) : int{
        return BossBarColor::PINK;
    }
}