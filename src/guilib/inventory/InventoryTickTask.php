<?php

declare(strict_types = 1);

namespace guilib\inventory;

use pocketmine\scheduler\Task;
use pocketmine\Server;

final class InventoryTickTask extends Task{

    private int $tick = 0;

    public function onRun() : void{
        foreach(Server::getInstance()->getOnlinePlayers() as $player){
            $inventory = $player->getCurrentWindow();
            if($inventory instanceof CustomInventory){
                $inventory->onTick($this->tick);
            }
        }

        $this->tick++;
    }
}