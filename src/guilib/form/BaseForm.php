<?php

declare(strict_types = 1);

namespace guilib\form;

use pocketmine\form\Form;
use pocketmine\player\Player;

abstract class BaseForm implements Form{

    protected Player $player;

    protected array $data = [];

    public function __construct(Player $player){
        $this->player = $player;
        $this->data['type'] = $this->getType();
        $this->data['title'] = $this->getTitle($player);
    }

    abstract protected function getType() : string;

    abstract protected function getTitle(Player $player) : string;

    final public function jsonSerialize() : array{
        return $this->data;
    }
}