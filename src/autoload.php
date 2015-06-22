<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */

/**
 * 立ち上げようのスクリプト
 */
define('NORA_SOURCE_PATH', realpath(__dir__));

// クラスローダーの読み込み
require_once NORA_SOURCE_PATH.'/class/AutoLoader.php';

// ノラクラスの読み込み
require_once NORA_SOURCE_PATH.'/class/Nora.php';

Nora\AutoLoader::singleton([
    'Nora' => NORA_SOURCE_PATH.'/class'
]);
