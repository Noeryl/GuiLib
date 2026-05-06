<?php

declare(strict_types = 1);

namespace guilib\inventory;

use pocketmine\inventory\SimpleInventory;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;

abstract class CustomInventory extends SimpleInventory{

    private int $tick = -1;

    public function __construct(int $size){
        parent::__construct($size);
        $this->setContents($this->getContent());
    }

    abstract public function getTitle() : string;

    /**
     * @return Item[]
     */
    abstract public function getContent() : array;

    final public function onOpen(Player $who) : void{
        parent::onOpen($who);
        $this->open($who);
    }

    final public function onTick(int $tick) : void{
        if($this->tick === $tick){
            return;
        }

        $this->tick = $tick;
        $this->tick($tick);
    }

    public function open(Player $player) : void{
        //NOOP
    }

    public function close(Player $player) : void{
        //NOOP
    }

    public function tick(int $tick) : void{
        //NOOP
    }

    public function click(Player $player, int $slot, Item $sourceItem, Item $targetItem) : bool{
        return false;
    }

    public function getWindowType() : int{
        return WindowTypes::CONTAINER;
    }
}