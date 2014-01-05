<?php

if (!function_exists('gzdecode')) {
    function gzdecode($data) {
        return gzinflate(substr($data, 10, -8));
    }
}