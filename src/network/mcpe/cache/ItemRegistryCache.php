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

namespace pocketmine\network\mcpe\cache;

use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\ItemRegistryPacket;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\utils\ProtocolSingletonTrait;

final class ItemRegistryCache{
	use ProtocolSingletonTrait;

	private ?ItemRegistryPacket $packet = null;

	/** @phpstan-var \WeakMap<ItemRegistryPacket, string>|null */
	private ?\WeakMap $encodedBuffers = null;

	public function getPacket(TypeConverter $typeConverter) : ItemRegistryPacket{
		return $this->packet ??= ItemRegistryPacket::create($typeConverter->getItemTypeDictionary()->getEntries());
	}

	/**
	 * Item registries are large and identical for every player on a protocol, so each packet instance is only
	 * encoded once. Packets are immutable after creation, which keeps the cached buffer valid.
	 */
	public function encode(PacketSerializer $serializer, ItemRegistryPacket $packet) : string{
		$this->encodedBuffers ??= new \WeakMap();
		if(!isset($this->encodedBuffers[$packet])){
			$packet->encode($serializer);
			$this->encodedBuffers[$packet] = $serializer->getBuffer();
		}

		return $this->encodedBuffers[$packet];
	}
}
