<?php

declare(strict_types = 1);

namespace guilib\form\types;

use pocketmine\player\Player;
use guilib\form\BaseForm;
use function is_array;

abstract class CustomForm extends BaseForm{

    public function __construct(Player $player){
        parent::__construct($player);
        $this->data["content"] = [];
        $this->getContent($player);
    }

    final protected function getType() : string{
        return "custom_form";
    }

    private function addContent(array $content) : void{
        $this->data["content"][] = $content;
    }

    final public function addLabel(string $text) : void{
        $this->addContent(["type" => "label", "text" => $text]);
    }

    final public function addToggle(string $text, bool $default = false) : void{
        $this->addContent(["type" => "toggle", "text" => $text, "default" => $default]);
    }

    final public function addSlider(string $text, int $min, int $max, int $step = 1, int $default = 0) : void{
        $this->addContent(["type" => "slider", "text" => $text, "min" => $min, "max" => $max, "step" => $step, "default" => $default]);
    }

    final public function addStepSlider(string $text, array $steps = [], int $default = 0) : void{
        $this->addContent(["type" => "step_slider", "text" => $text, "steps" => $steps, "default" => $default]);
    }

    final public function addDropdown(string $text, array $options = [], int $default = 0) : void{
        $this->addContent(["type" => "dropdown", "text" => $text, "options" => $options, "default" => $default]);
    }

    final public function addInput(string $text, string $placeholder = "", ?string $default = null) : void{
        $this->addContent(["type" => "input", "text" => $text, "placeholder" => $placeholder, "default" => $default]);
    }

    final public function handleResponse(Player $player, mixed $data) : void{
        if(is_array($data)){
            $this->onSubmit($player, $data);
        }

        if($data === null){
            $this->onClose($player);
        }
    }

    abstract protected function getContent(Player $player) : void;

    protected function onSubmit(Player $player, array $data) : void{
        //NOOP
    }

    protected function onClose(Player $player) : void{
        //NOOP
    }
}