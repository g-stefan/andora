<?php
// XYO.Web
// Copyright (c) 2024 Grigore Stefan <g_stefan@yahoo.com>
// MIT License (MIT) <http://opensource.org/licenses/MIT>
// SPDX-FileCopyrightText: 2024 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: MIT

defined("XYO_WEB") or die("Forbidden");

$configInstance = \XYO\Web\Config::instance();
$configPattern = "config.*.php";
foreach (glob($configPattern) as $configFilename) {
	$configInstance->includeFile($configFilename);
}

return (new \stdClass());
