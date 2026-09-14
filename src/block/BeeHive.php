<?php

/*
 *
 *  _____                    _   _       _
 * | ____|___ ___  ___ _ __ | |_(_) __ _| |
 * |  _| / __/ __|/ _ \ '_ \| __| |/ _` | |
 * | |___\__ \__ \  __/ | | | |_| | (_| | |
 * |_____|___/___/\___|_| |_|\__|_|\__,_|_|
 *
 * Essential — PocketMine-MP Fork
 * Supported MCPE/Bedrock versions: 1.12, 1.16 - 1.26.x
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Essential Team
 * @link https://github.com/BakuTeam/Essential
 *
 *
 */

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\block\utils\FacesOppositePlacingPlayerTrait;
use pocketmine\data\runtime\RuntimeDataDescriber;

class BeeHive extends Opaque{
	use FacesOppositePlacingPlayerTrait;

	public const MAX_HONEY_LEVEL = 5;

	protected int $honeyLevel = 0;

	protected function describeBlockOnlyState(RuntimeDataDescriber $w) : void{
		$w->horizontalFacing($this->facing);
		$w->boundedIntAuto(0, self::MAX_HONEY_LEVEL, $this->honeyLevel);
	}

	public function getHoneyLevel() : int{ return $this->honeyLevel; }

	/** @return $this */
	public function setHoneyLevel(int $honeyLevel) : self{
		if($honeyLevel < 0 || $honeyLevel > self::MAX_HONEY_LEVEL){
			throw new \InvalidArgumentException("Honey level must be in range 0 ... " . self::MAX_HONEY_LEVEL);
		}
		$this->honeyLevel = $honeyLevel;
		return $this;
	}
}
