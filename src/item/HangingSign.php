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

namespace pocketmine\item;

use pocketmine\block\Block;
use pocketmine\block\CeilingCenterHangingSign;
use pocketmine\block\CeilingEdgesHangingSign;
use pocketmine\block\utils\SupportType;
use pocketmine\block\WallHangingSign;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

final class HangingSign extends Item{

	public function __construct(
		ItemIdentifier $identifier,
		string $name,
		private Block $centerPointCeilingVariant,
		private Block $edgePointCeilingVariant,
		private Block $wallVariant
	){
		parent::__construct($identifier, $name);
	}

	public function getPlacementBlock(?Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector) : Block{
		if($face === Facing::DOWN){
			if($player !== null && $player->isSneaking()){
				return clone $this->centerPointCeilingVariant;
			}

			$support = $blockReplace->getSide(Facing::UP);
			$result =
				(($support instanceof CeilingEdgesHangingSign || $support instanceof WallHangingSign) && ($player === null || Facing::axis($player->getHorizontalFacing()) !== Facing::axis($support->getFacing()))) ||
				$support instanceof CeilingCenterHangingSign ||
				$support->getSupportType(Facing::DOWN) === SupportType::CENTER ?
					$this->centerPointCeilingVariant :
					$this->edgePointCeilingVariant;
		}else{
			$result = $this->wallVariant;
		}
		return clone $result;
	}

	public function getBlock(?int $clickedFace = null) : Block{
		return $clickedFace === Facing::DOWN ? clone $this->centerPointCeilingVariant : clone $this->wallVariant;
	}

	public function getMaxStackSize() : int{
		return 16;
	}

	public function getFuelTime() : int{
		return 200;
	}
}
