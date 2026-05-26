<?php

declare(strict_types = 1);

namespace guilib\form;

use pocketmine\player\Player;
use function is_int;
use function preg_match;

abstract class SimpleForm extends BaseForm{

    /** @var array<int, mixed> */
    private array $values = [];

    public function __construct(Player $player){
        parent::__construct($player);

        $this->data['content'] = $this->getContent($player);
        foreach($this->getButtons($player) as $button){
            $text = $button[0] ?? 'null';
            $image = $button[1] ?? null;
            $value = $button[2] ?? null;

            $b = ['text' => $text];
            if(!empty($image)){
                $b['image'] = [
                    'type' => preg_match('/^https?:\/\//i', $image) ? 'url' : 'path',
                    'data' => $image
                ];
            }

            $this->values[] = $value;
            $this->data['buttons'][] = $b;
        }
    }

    final protected function getType() : string{
        return 'form';
    }

    final public function handleResponse(Player $player, mixed $data) : void{
        if(is_int($data)){
            $this->onClick($player, $data, ($this->values[$data] ?? null));
        }

        if($data === null){
            $this->onClose($player);
        }
    }

    abstract protected function getButtons(Player $player) : array;

    protected function getContent(Player $player) : string{
        return '';
    }

    protected function onClick(Player $player, int $button, mixed $value) : void{
        //NOOP
    }

    protected function onClose(Player $player) : void{
        //NOOP
    }
}