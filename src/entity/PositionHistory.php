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

namespace pocketmine\entity;

use pocketmine\math\Vector3;
use function abs;
use const PHP_INT_MAX;

final class PositionHistory{

	private const int MAX_TICKS = 40;

	/** @var array<int, Vector3> tick => position */
	private array $history = [];

	public function record(int $tick, Vector3 $pos) : void{
		$this->history[$tick] = $pos;

		// Prune entries older than MAX_TICKS
		$cutoff = $tick - self::MAX_TICKS;
		foreach($this->history as $t => $_){
			if($t < $cutoff){
				unset($this->history[$t]);
			}else{
				break;
			}
		}
	}

	public function getPositionAtTick(int $tick) : ?Vector3{
		if(isset($this->history[$tick])){
			return $this->history[$tick];
		}

		$bestTick = null;
		$bestDist = PHP_INT_MAX;
		foreach($this->history as $t => $pos){
			$dist = abs($t - $tick);
			if($dist < $bestDist){
				$bestDist = $dist;
				$bestTick = $t;
			}
		}

		return $bestTick !== null ? $this->history[$bestTick] : null;
	}

	public function isNearRecentHitbox(Vector3 $pos, EntitySizeInfo $size, int $currentTick, int $maxAgeTicks, float $margin) : bool{
		$cutoff = $currentTick - $maxAgeTicks;
		$halfWidth = ($size->getWidth() / 2) + $margin;
		$height = $size->getHeight() + $margin;

		foreach($this->history as $tick => $historyPos){
			if($tick < $cutoff){
				continue;
			}
			if(abs($pos->x - $historyPos->x) > $halfWidth || abs($pos->z - $historyPos->z) > $halfWidth){
				continue;
			}
			if($pos->y >= $historyPos->y - $margin && $pos->y <= $historyPos->y + $height){
				return true;
			}
		}

		return false;
	}

	public function clear() : void{
		$this->history = [];
	}
}
