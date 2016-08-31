<?php

namespace FloatingStatus;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

use pocketmine\scheduler\CallbackTask;
use pocketmine\utils\TextFormat as F;
use pocketmine\utils\Config;
use pocketmine\network\protocol\RemoveEntityPacket;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

class MainClass extends PluginBase implements Listener {

	private $config;

	public function onLoad() {
		$this->getLogger()->info(F::WHITE . "I've been loaded!");
	}

	public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info(F::DARK_GREEN . "I've been enabled!");

		if(!file_exists($this->getDataFolder())){
			$this->getLogger()->info(F::YELLOW . "Create a directory...");
			mkdir($this->getDataFolder());
		}
		if(!file_exists($this->getDataFolder() . "config.yaml")){
			(new Config($this->getDataFolder() . "config.yaml", Config::YAML, yaml_parse(stream_get_contents($this->getResource("config.yaml")))))->save();
			$this->getLogger()->info(F::YELLOW . "Loading config.yaml!");
		}	
		$this->config = new Config($this->getDataFolder() . "config.yaml", Config::YAML);

		$this->getServer()->getScheduler()->scheduleRepeatingTask(new CallbackTask(array($this, "spawnText")), 20 * 5);
    }
	
	public function spawnText() {
 		$tps = $this->getServer()->getTicksPerSecond();
        $p = count($this->getServer()->getOnlinePlayers());
        $full = $this->getServer()->getMaxPlayers();
        $load = $this->getServer()->getTickUsage();

		$x = $this->config->get("isX");
        $y = $this->config->get("isY");
        $z = $this->config->get("isZ");

		$group[0] =
		"§5- §2Server Status! §5-\n" .
		"§eTPS: §f" . $tps . "\n" .
		"§bONLINE: §f" . $p . "§b/§f" .$full . "\n" .
		"§6LOAD: §f" . $load . "§6%";
		$group[1] =
		"§e- §6Server Status! §e-\n" .
		"§eTPS: §f" . $tps . "\n" .
		"§bONLINE: §f" . $p . "§b/§f" .$full . "\n" .
		"§6LOAD: §f" . $load . "§6%" . "\n";

		$texts = new FloatingTextParticle(new Vector3($x, $y, $z), $group[mt_rand(0,1)], null);
		$this->getServer()->getDefaultLevel()->addParticle($texts);
		$this->getServer()->getScheduler()->scheduleDelayedTask(new PluginTask($this, $texts, $this->getServer()->getDefaultLevel()), 20 * 5);
	}

	public function respawnText(FloatingTextParticle $particle, Level $level) {
		$particle->setInvisible();
		$level->addParticle($particle);
	}

	public function onDisable() {
		$this->getLogger()->info(F::DARK_RED . "I've been disabled!");
	}

}
