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

namespace pocketmine\network\mcpe\convert;

use function preg_match;
use function preg_replace;
use function str_starts_with;
use function strlen;
use function substr;

/**
 * Picks look-alike blocks for states which don't exist in older protocols.
 */
final class BlockStateSubstitutes{
	private const NAMESPACE = 'minecraft:';

	private const RULES = [
		'/^(crimson|warped)_hyphae$/' => '$1_wood',
		'/^stripped_(crimson|warped)_hyphae$/' => 'stripped_$1_wood',
		'/^(stripped_)?(crimson|warped)_stem$/' => '$1$2_log',
		'/^waxed_(.+)$/' => '$1',

		'/^(deepslate|infested_deepslate|reinforced_deepslate)$/' => 'stone',
		'/^cobbled_deepslate_stairs$/' => 'stone_stairs',
		'/^cobbled_deepslate/' => 'cobblestone',
		'/^polished_deepslate/' => 'polished_andesite',
		'/^(chiseled|cracked)_deepslate_(bricks|tiles)$/' => '$1_stone_bricks',
		'/^chiseled_deepslate$/' => 'chiseled_stone_bricks',
		'/^deepslate_(brick|tile)/' => 'stone_brick',
		'/^(lit_)?deepslate_(.+_ore)$/' => '$1$2',

		'/^(chiseled_)?tuff_bricks$/' => '$1stone_bricks',
		'/^chiseled_tuff$/' => 'chiseled_stone_bricks',
		'/^tuff_brick_/' => 'stone_brick_',
		'/^polished_tuff/' => 'polished_andesite',
		'/^tuff/' => 'andesite',
		'/^calcite$/' => 'diorite',
		'/^dripstone_block$/' => 'granite',
		'/^pointed_dripstone$/' => 'air',

		'/^(polished_)?basalt$/' => 'polished_andesite',
		'/^smooth_basalt$/' => 'smooth_stone',
		'/^polished_blackstone_button$/' => 'stone_button',
		'/^polished_blackstone_pressure_plate$/' => 'stone_pressure_plate',
		'/^(blackstone|gilded_blackstone|polished_blackstone|polished_blackstone_bricks|chiseled_polished_blackstone|cracked_polished_blackstone_bricks)$/' => 'nether_brick',
		'/^(polished_blackstone_brick|polished_blackstone|blackstone)_/' => 'nether_brick_',
		'/^(cracked|chiseled)_nether_bricks$/' => 'nether_brick',
		'/^(crimson|warped)_nylium$/' => 'netherrack',
		'/^(crimson_roots|warped_roots|crimson_fungus|warped_fungus|nether_sprouts|twisting_vines|weeping_vines)$/' => 'air',
		'/^warped_wart_block$/' => 'nether_wart_block',
		'/^shroomlight$/' => 'glowstone',
		'/^soul_soil$/' => 'brown_concrete',
		'/^soul_(torch|lantern|campfire|fire)$/' => '$1',
		'/^(ancient_debris|lodestone)$/' => 'netherrack',
		'/^(crying_obsidian|respawn_anchor)$/' => 'obsidian',
		'/^netherite_block$/' => 'coal_block',
		'/^(stripped_)?crimson_/' => '$1dark_oak_',
		'/^(stripped_)?warped_/' => '$1spruce_',

		'/^mangrove_roots$/' => 'oak_leaves',
		'/^muddy_mangrove_roots$/' => 'coarse_dirt',
		'/^mangrove_propagule$/' => 'air',
		'/^(stripped_)?mangrove_/' => '$1jungle_',
		'/^(stripped_)?(cherry|pale_oak)_/' => '$1birch_',
		'/^bamboo_mosaic$/' => 'birch_planks',
		'/^(stripped_)?bamboo_block$/' => '$1birch_log',
		'/^bamboo_(mosaic_)?(planks|stairs|slab|double_slab|fence|fence_gate|door|trapdoor|button|pressure_plate|standing_sign|wall_sign|hanging_sign)$/' => 'birch_$2',
		'/^pale_moss_block$/' => 'light_gray_concrete',
		'/^pale_moss_carpet$/' => 'light_gray_carpet',
		'/^creaking_heart$/' => 'birch_log',
		'/^resin_bricks$/' => 'brick_block',
		'/^resin_brick_/' => 'brick_',
		'/^resin_block$/' => 'orange_concrete',

		'/^(weathered|oxidized)_cut_copper/' => 'prismarine',
		'/^(exposed_)?cut_copper/' => 'granite',
		'/^(weathered_copper|oxidized_copper|weathered_chiseled_copper|oxidized_chiseled_copper)$/' => 'prismarine',
		'/^(copper_block|exposed_copper|chiseled_copper|exposed_chiseled_copper|raw_copper_block)$/' => 'granite',
		'/^(exposed_|weathered_|oxidized_)?copper_bulb$/' => 'redstone_lamp',
		'/^(exposed_|weathered_|oxidized_)?copper_(torch|lantern)$/' => '$2',
		'/^(exposed_|weathered_|oxidized_)?copper_chain$/' => 'iron_chain',
		'/^(exposed_|weathered_|oxidized_)?lightning_rod$/' => 'end_rod',
		'/^iron_chain$/' => 'iron_bars',
		'/^raw_(iron|gold)_block$/' => '$1_block',

		'/^(amethyst_block|budding_amethyst)$/' => 'purpur_block',
		'/^(amethyst_cluster|small_amethyst_bud|medium_amethyst_bud|large_amethyst_bud)$/' => 'air',
		'/^azalea_leaves_flowered$/' => 'azalea_leaves',
		'/^(azalea_leaves|azalea|flowering_azalea)$/' => 'oak_leaves',
		'/^big_dripleaf$/' => 'waterlily',
		'/^small_dripleaf_block$/' => 'fern',
		'/^cave_vines(_body_with_berries|_head_with_berries)?$/' => 'vine',
		'/^(glow_lichen|hanging_roots|spore_blossom|sculk_vein|pink_petals|wildflowers|leaf_litter|pale_hanging_moss|resin_clump|pitcher_crop|torchflower_crop|torchflower|pitcher_plant|firefly_bush|bush|short_dry_grass|tall_dry_grass|cactus_flower|open_eyeblossom|closed_eyeblossom)$/' => 'air',
		'/^moss_block$/' => 'green_concrete',
		'/^moss_carpet$/' => 'green_carpet',
		'/^dirt_with_roots$/' => 'coarse_dirt',
		'/^packed_mud$/' => 'hardened_clay',
		'/^mud_bricks$/' => 'brick_block',
		'/^mud_brick_/' => 'brick_',
		'/^mud$/' => 'gray_concrete',
		'/^sculk$/' => 'black_concrete',
		'/^(pearlescent|verdant)_froglight$/' => 'sea_lantern',
		'/^ochre_froglight$/' => 'glowstone',
		'/^chiseled_bookshelf$/' => 'bookshelf',
		'/^smithing_table$/' => 'crafting_table',
		'/^(beehive|bee_nest)$/' => 'oak_planks',
		'/^light_block_\d+$/' => 'air',
		'/^([a-z_]+_)?candle$/' => 'air',
		'/^([a-z_]+_)?candle_cake$/' => 'cake',
		'/^dead_([a-z]+_coral)$/' => '$1',

		'/^.+_double_slab$/' => 'oak_double_slab',
		'/^.+_slab$/' => 'oak_slab',
		'/^.+_stairs$/' => 'oak_stairs',
		'/^.+_wall$/' => 'cobblestone_wall',
		'/^.+_fence_gate$/' => 'fence_gate',
		'/^.+_fence$/' => 'oak_fence',
		'/^.+_trapdoor$/' => 'trapdoor',
		'/^.+_door$/' => 'wooden_door',
		'/^.+_button$/' => 'wooden_button',
		'/^.+_pressure_plate$/' => 'wooden_pressure_plate',
		'/^.+_planks$/' => 'oak_planks',
		'/^.+_log$/' => 'oak_log',
		'/^.+_wood$/' => 'oak_wood',
		'/^.+_leaves$/' => 'oak_leaves',
		'/^.+_sapling$/' => 'oak_sapling',
		'/^.+_standing_sign$/' => 'standing_sign',
		'/^.+_wall_sign$/' => 'wall_sign',
		'/^.+_hanging_sign$/' => 'air',
		'/^.+_carpet$/' => 'white_carpet',
	];

	private function __construct(){
		//NOOP
	}

	/**
	 * Returns the next look-alike block name to try, or null if there is nothing left to try.
	 */
	public static function next(string $name) : ?string{
		if(!str_starts_with($name, self::NAMESPACE)){
			return null;
		}

		$shortName = substr($name, strlen(self::NAMESPACE));
		foreach(self::RULES as $pattern => $replacement){
			if(preg_match($pattern, $shortName) === 1){
				$substitute = self::NAMESPACE . preg_replace($pattern, $replacement, $shortName, 1);

				return $substitute !== $name ? $substitute : null;
			}
		}

		return null;
	}
}
