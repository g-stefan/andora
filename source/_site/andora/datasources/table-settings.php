<?php
// GuestBox.Web
// Copyright (c) 2024 Grigore Stefan <g_stefan@yahoo.com.hpp>
// Apache License 2.0 <https://opensource.org/license/apache-2-0/>
// SPDX-FileCopyrightText: 2024 Grigore Stefan <g_stefan@yahoo.com.hpp>
// SPDX-License-Identifier: Apache-2.0

namespace Andora\DataSources {

    defined("XYO_WEB") or die("Forbidden");
    require_once("./_site/xyo/web/web.php");

    class TableSettings extends \XYO\Web\DataSource\Table
    {
        public $id;
        public $component_id;
        public $name;
        public $value;

        public function __construct($connection = null)
        {
            parent::__construct($connection);
        }

        public static function descriptor(&$info)
        {
            $info->name = "settings";
            $info->primaryKey = "id";
            $info->fields = array(
                "id" => array("bigint", "DEFAULT", "unsigned", "autoIncrement"),
                "component_id" => array("bigint", 0, "unsigned"),
                "name" => array("varchar", null, 128),
                "value" => array("varchar", null, 255)
            );
            $info->indexes = array(
                "component_id",
                "name"
            );
        }

    }

}

