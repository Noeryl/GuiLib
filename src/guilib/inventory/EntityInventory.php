<?php

declare(strict_types = 1);

namespace guilib\inventory;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\block\tile\ContainerTrait;
use pocketmine\entity\Entity;
use pocketmine\inventory\Inventory;

abstract class EntityInventory extends CustomInventory{

    use ContainerTrait;

    private ?Entity $entity = null;

    final public function getTitle() : string{
        return ($entity = $this->entity) !== null ? $entity->getNameTag() : "";
    }

    final public function onClose(Player $who) : void{
        parent::onClose($who);
        $this->close($who);
    }

    final public function setEntity(?Entity $entity) : void{
        $this->entity = $entity;
    }

    final public function getEntity() : ?Entity{
        return $this->entity;
    }

    final public function getRealInventory() : Inventory{
        return $this;
    }

    /**
     * @internal
     * @return Position
     */
    final protected function getPosition() : Position{
        return ($entity = $this->entity) !== null ? $entity->getPosition() : Server::getInstance()->getWorldManager()->getDefaultWorld()->getSpawnLocation();
    }
}