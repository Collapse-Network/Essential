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

use pocketmine\block\utils\HorizontalFacingTrait;
use pocketmine\block\utils\SupportType;
use pocketmine\item\Item;
use pocketmine\math\Axis;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\BlockTransaction;

final class WallHangingSign extends BaseSign{
	use HorizontalFacingTrait;

	protected function getSupportingFace() : int{
		return Facing::rotateY($this->facing, clockwise: true);
	}

	public function onNearbyBlockChange() : void{
		//NOOP
	}

	protected function recalculateCollisionBoxes() : array{
		return [AxisAlignedBB::one()->trim(Facing::DOWN, 7 / 8)->squash(Facing::axis($this->facing), 3 / 4)];
	}

	public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null) : bool{
		if($player === null){
			return false;
		}
		$attachFace = Facing::axis($face) === Axis::Y ? Facing::rotateY($player->getHorizontalFacing(), clockwise: true) : $face;

		if($this->canBeSupportedAt($blockReplace->getSide($attachFace), $attachFace)){
			$direction = $attachFace;
		}elseif($this->canBeSupportedAt($blockReplace->getSide($opposite = Facing::opposite($attachFace)), $opposite)){
			$direction = $opposite;
		}else{
			return false;
		}

		$this->facing = Facing::rotateY(Facing::opposite($direction), clockwise: true);
		if($this->facing === $player->getHorizontalFacing()){
			$this->facing = Facing::opposite($this->facing);
		}

		return parent::place($tx, $item, $blockReplace, $blockClicked, $face, $clickVector, $player);
	}

	private function canBeSupportedAt(Block $block, int $face) : bool{
		return
			($block instanceof WallHangingSign && Facing::axis(Facing::rotateY($block->getFacing(), clockwise: true)) === Facing::axis($face)) ||
			$block->getSupportType(Facing::opposite($face)) === SupportType::FULL;
	}
}
