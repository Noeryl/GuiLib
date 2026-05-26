<?php

declare(strict_types = 1);

namespace guilib\form;

use pocketmine\player\Player;
use function is_bool;

abstract class ModalForm extends BaseForm{

    public function __construct(Player $player){
        parent::__construct($player);
        $this->data['content'] = $this->getContent($player);
        $this->data['button1'] = $this->getButtons($player)[0] ?? 'null';
        $this->data['button2'] = $this->getButtons($player)[1] ?? 'null';
    }

    final protected function getType() : string{
        return 'modal';
    }

    final public function handleResponse(Player $player, mixed $data) : void{
        if(is_bool($data)){
            $this->onClick($player, $data);
        }

        if($data === null){
            $this->onClose($player);
        }
    }

    abstract protected function getContent(Player $player) : string;

    abstract protected function getButtons(Player $player) : array;

    protected function onClick(Player $player, bool $answer) : void{
        //NOOP
    }

    protected function onClose(Player $player) : void{
        //NOOP
    }
}