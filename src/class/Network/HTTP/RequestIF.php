<?php
namespace Nora\Network\HTTP;

/**
 * HTTP Client Request
 */
interface RequestIF
{
    public function getMethod();
    public function getURL();
    public function getHeaders();
    public function getParams();
}
