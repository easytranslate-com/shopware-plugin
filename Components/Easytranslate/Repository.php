<?php

namespace Easytranslate\Components\Easytranslate;

/**
 * Interface Repository
 * @package Easytranslate\Components\Easytranslate
 */
interface Repository
{
    function save($entity);
    function load($id);
    function update($entity);
}
