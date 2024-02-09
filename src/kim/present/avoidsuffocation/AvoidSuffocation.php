<?php

/**
 *  ____                           _   _  ___
 * |  _ \ _ __ ___  ___  ___ _ __ | |_| |/ (_)_ __ ___
 * | |_) | '__/ _ \/ __|/ _ \ '_ \| __| ' /| | '_ ` _ \
 * |  __/| | |  __/\__ \  __/ | | | |_| . \| | | | | | |
 * |_|   |_|  \___||___/\___|_| |_|\__|_|\_\_|_| |_| |_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author       PresentKim (debe3721@gmail.com)
 * @link         https://github.com/PresentKim
 * @license      https://www.gnu.org/licenses/lgpl-3.0 LGPL-3.0 License
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 *
 * @noinspection PhpUnused
 */

declare(strict_types=1);

namespace kim\present\avoidsuffocation;

use pocketmine\block\BlockTypeIds;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\math\Facing;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

use function count;
use function is_dir;
use function rmdir;
use function scandir;

class AvoidSuffocation extends PluginBase implements Listener{
	public function onEnable() : void{
		/**
		 * This is a plugin that does not use data folders.
		 * Delete the unnecessary data folder of this plugin for users.
		 */
		$dataFolder = $this->getDataFolder();
		if(is_dir($dataFolder) && count(scandir($dataFolder)) <= 2){
			rmdir($dataFolder);
		}

		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	/** @priority HIGHEST */
	public function onEntityDamageEvent(EntityDamageEvent $event) : void{
		if($event->getCause() !== EntityDamageEvent::CAUSE_SUFFOCATION){
			return;
		}

		$entity = $event->getEntity();
		if(!$entity instanceof Player){
			return;
		}

		$world = $entity->getWorld();
		$pos = $entity->getPosition();
		foreach(Facing::HORIZONTAL as $face){
			$blockVec = $pos->getSide($face);
			if(
				$world->getBlock($blockVec->up())->getTypeId() === BlockTypeIds::AIR
				&& $world->getBlock($blockVec)->getTypeId() === BlockTypeIds::AIR
			){
				$entity->setMotion($blockVec->subtractVector($pos)->multiply(0.1));
				$event->cancel();
				return;
			}
		}
	}
}
