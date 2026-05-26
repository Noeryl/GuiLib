<?php

declare(strict_types = 1);

namespace guilib\inventory;

use pocketmine\block\VanillaBlocks;
use pocketmine\block\tile\Nameable;
use pocketmine\block\inventory\BlockInventory as PMBlockInventory;
use pocketmine\block\inventory\BlockInventoryTrait;
use pocketmine\world\Position;
use pocketmine\player\Player;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use function strtolower;

abstract class BlockInventory extends CustomInventory implements PMBlockInventory{

    use BlockInventoryTrait;

    /** @var Vector3[] */
    private array $chestPos = [];

    public function __construct(int $size, Player $player){
        parent::__construct($size);
        $chestPos = $player->getPosition()->floor()->subtract(0, 3, 0);
        $this->holder = Position::fromObject($chestPos, $player->getWorld());

        $session = $player->getNetworkSession();
        $chestId = $session->getTypeConverter()->getBlockTranslator()->internalIdToNetworkId(VanillaBlocks::CHEST()->getStateId());
        $packet = new UpdateBlockPacket();
        $packet->blockPosition = $blockPosition = BlockPosition::fromVector3($chestPos);
        $packet->blockRuntimeId = $chestId;
        $session->sendDataPacket($packet);

        $packet = new BlockActorDataPacket();
        $packet->blockPosition = $blockPosition;
        $packet->nbt = new CacheableNbt(CompoundTag::create()->setString(Nameable::TAG_CUSTOM_NAME, $this->getTitle()));
        $session->sendDataPacket($packet);

        $this->chestPos[strtolower($player->getName())] = $chestPos;
    }

    final public function onClose(Player $who) : void{
        parent::onClose($who);
        $this->close($who);
        $chestPos = $this->chestPos[$name = strtolower($who->getName())];

        $session = $who->getNetworkSession();
        $blockId = $who->getWorld()->getBlockAt($chestPos->x, $chestPos->y, $chestPos->z)->getStateId();
        $runtimeId = $session->getTypeConverter()->getBlockTranslator()->internalIdToNetworkId($blockId);
        $packet = new UpdateBlockPacket();
        $packet->blockPosition = BlockPosition::fromVector3($chestPos);
        $packet->blockRuntimeId = $runtimeId;
        $session->sendDataPacket($packet);

        unset($this->chestPos[$name]);
    }
}