<?php

namespace FloatingStatus;

use pocketmine\scheduler\PluginTask;
use pocketmine\level\Level;

class PluginTask extends PluginTask
{
	public $owner, $particle, $level;
	
	public function __construct(MainClass $owner, $particle, Level $level){
		$this->owner = $owner;
		$this->particle = $particle;
		$this->level = $level;
	}
	
	public function onRun($currentTick){
		$this->owner->respawnText($this->particle, $this->level);
	}
}
