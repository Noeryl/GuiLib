<?php

declare(strict_types = 1);

namespace guilib;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use guilib\scoreboard\Scoreboard;
use guilib\bossbar\Bossbar;
use guilib\inventory\CustomInventory;
use guilib\inventory\BlockInventory;
use guilib\inventory\EntityInventory;
use function rmdir;

final class GuiLib extends PluginBase implements Listener{

    protected function onLoad() : void{
        rmdir($this->getDataFolder());
    }

    protected function onEnable() : void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    /**
     * @priority LOWEST
     */
    public function onPlayerJoin(PlayerJoinEvent $event) : void{
        $player = $event->getPlayer();

        $player->getNetworkSession()->getInvManager()?->getContainerOpenCallbacks()->add(function(int $id, Inventory $inventory) : ?array{
            if($inventory instanceof CustomInventory){
                if($inventory instanceof BlockInventory){
                    return [ContainerOpenPacket::blockInv($id, $inventory->getWindowType(), BlockPosition::fromVector3($inventory->getHolder()))];
                }
                if($inventory instanceof EntityInventory){
                    return [ContainerOpenPacket::entityInv($id, $inventory->getWindowType(), $inventory->getEntity()->getId())];
                }
            }

            return null;
        });
    }

    /**
     * @priority LOWEST
     */
    public function onPlayerQuit(PlayerQuitEvent $event) : void{
        $player = $event->getPlayer();

        if(Scoreboard::get($player) !== null){
            Scoreboard::remove($player);
        }
        if(Bossbar::get($player) !== null){
            Bossbar::remove($player);
        }
    }

    /**
     * @priority LOWEST
     */
    public function onInventoryTransaction(InventoryTransactionEvent $event) : void{
        $transaction = $event->getTransaction();
        $player = $transaction->getSource();

        foreach($transaction->getActions() as $action){
            if(!$action instanceof SlotChangeAction){
                continue;
            }

            $inventory = $action->getInventory();
            if(!$inventory instanceof CustomInventory){
                continue;
            }
            if(!$inventory->click($player, $action->getSlot(), $action->getSourceItem(), $action->getTargetItem())){
                continue;
            }

            $event->cancel();
            return;
        }
    }
}