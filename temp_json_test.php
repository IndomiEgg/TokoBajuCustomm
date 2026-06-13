<?php
$value = '["front_center"]';
echo "VALUE=" . $value . "\n";
echo "LENGTH=" . strlen($value) . "\n";
var_export(json_decode($value, false, 512, JSON_THROW_ON_ERROR));
