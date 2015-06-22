<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */

define('NORA_CSS_SPRITE_PATH', realpath(__dir__));

Nora\AutoLoader::singleton([
    'Nora\Module\CssSprite' => NORA_CSS_SPRITE_PATH.'/class'
]);
