<?php
// GuestBox.Web
// Copyright (c) 2024 Grigore Stefan <g_stefan@yahoo.com.hpp>
// Apache License 2.0 <https://opensource.org/license/apache-2-0/>
// SPDX-FileCopyrightText: 2024 Grigore Stefan <g_stefan@yahoo.com.hpp>
// SPDX-License-Identifier: Apache-2.0

namespace Andora\DataSource {

    defined("XYO_WEB") or die("Forbidden");
    require_once("./_site/xyo/web/web.php");

    class TableUserSetting extends \XYO\Web\DataSource\Table
    {
        public $id;
        public $user__id;
        public $component__id;
        public $name;
        public $value;

        public function __construct($connection = null)
        {
            parent::__construct($connection);
        }

        public static function descriptor(&$info)
        {
            $info->name = "user-setting";
            $info->primaryKey = "id";
            $info->fields = array(
                "id" => array("bigint", "DEFAULT", "unsigned", "autoIncrement"),
                "user__id" => array("bigint", 0, "unsigned"),
                "component__id" => array("bigint", 0, "unsigned"),
                "name" => array("varchar", null, 128),
                "value" => array("varchar", null, 255)
            );
            $info->indexes = array(
                "user__id",
                "component__id",
                "name"
            );
        }

    }

}
