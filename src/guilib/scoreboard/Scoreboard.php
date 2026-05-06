<?php

declare(strict_types = 1);

namespace guilib\scoreboard;

use pocketmine\player\Player;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;

abstract class Scoreboard{

    /** @var array<string, string> */
    private static array $players = [];

    public static function send(Player $player, self $scoreboard) : void{
        if(self::get($player) !== null){
            self::remove($player);
        }

        self::$players[$player->getName()] = $scoreboard->getName();
        $packet = new SetDisplayObjectivePacket();
        $packet->displaySlot = "sidebar";
        $packet->objectiveName = $scoreboard->getName();
        $packet->displayName = $scoreboard->getTitle($player);
        $packet->criteriaName = "dummy";
        $packet->sortOrder = 0;
        $player->getNetworkSession()->sendDataPacket($packet);

        $score = 0;
        foreach($scoreboard->getLines($player) as $line){
            if($line === ""){
                continue;
            }

            $score++;
            $entry = new ScorePacketEntry();
            $entry->objectiveName = $scoreboard->getName();
            $entry->type = $entry::TYPE_FAKE_PLAYER;
            $entry->customName = $line;
            $entry->score = $score;
            $entry->scoreboardId = $score;
            $packet = new SetScorePacket();
            $packet->type = $packet::TYPE_CHANGE;
            $packet->entries[] = $entry;
            $player->getNetworkSession()->sendDataPacket($packet);
        }
    }

    public static function remove(Player $player) : void{
        $packet = new RemoveObjectivePacket();
        $packet->objectiveName = self::$players[$player->getName()];
        $player->getNetworkSession()->sendDataPacket($packet);

        unset(self::$players[$player->getName()]);
    }

    public static function get(Player $player) : ?string{
        return self::$players[$player->getName()] ?? null;
    }

    abstract public function getName() : string;

    abstract public function getTitle(Player $player) : string;

    abstract public function getLines(Player $player) : array;
}