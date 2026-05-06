<?php

declare(strict_types = 1);

namespace guilib\inventory;

use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use function count;

trait InventoryEntityTrait{

    abstract public function getEntityInventory() : EntityInventory;

    public function onUpdate(int $currentTick) : bool{
        $changedProperties = $this->getDirtyNetworkData();
        if(count($changedProperties) > 0){
            $this->sendData(null, $changedProperties);
            $this->getNetworkProperties()->clearDirtyProperties();
        }

        $properties = $this->getNetworkProperties();
        $properties->setByte(EntityMetadataProperties::CONTAINER_TYPE, ($inventory = $this->getEntityInventory())->getWindowType());
        $properties->setInt(EntityMetadataProperties::CONTAINER_BASE_SIZE, $inventory->getSize());
        $this->networkPropertiesDirty = true;
        return true;
    }
}