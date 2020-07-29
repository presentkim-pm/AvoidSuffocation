<?php

/*
 *
 *  ____  _             _         _____
 * | __ )| |_   _  __ _(_)_ __   |_   _|__  __ _ _ __ ___
 * |  _ \| | | | |/ _` | | '_ \    | |/ _ \/ _` | '_ ` _ \
 * | |_) | | |_| | (_| | | | | |   | |  __/ (_| | | | | | |
 * |____/|_|\__,_|\__, |_|_| |_|   |_|\___|\__,_|_| |_| |_|
 *                |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author  Blugin team
 * @link    https://github.com/Blugin
 * @license https://www.gnu.org/licenses/lgpl-3.0 LGPL-3.0 License
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 */

declare(strict_types=1);

namespace blugin\avoidsuffocation;

use pocketmine\block\BlockIds;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class AvoidSuffocation extends PluginBase implements Listener{
    public function onEnable() : void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    /**
     * @priority HIGHEST
     *
     * @param EntityDamageEvent $event
     */
    public function onEntityDamageEvent(EntityDamageEvent $event) : void{
        if($event->getCause() !== EntityDamageEvent::CAUSE_SUFFOCATION)
            return;

        $entity = $event->getEntity();
        if(!$entity instanceof Player)
            return;

        $world = $entity->getLevel();
        $vec = $entity->getPosition()->floor();
        foreach([
            Vector3::SIDE_NORTH,
            Vector3::SIDE_SOUTH,
            Vector3::SIDE_WEST,
            Vector3::SIDE_EAST
        ] as $_ => $face){
            $blockVec = $vec->getSide($face);
            if($world->getBlock($blockVec->up())->getId() === BlockIds::AIR && $world->getBlock($blockVec)->getId() === BlockIds::AIR){
                $entity->setMotion($blockVec->subtract($vec)->multiply(0.1));
                $event->setCancelled();
                return;
            }
        }
    }
}