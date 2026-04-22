<?php
// GuestBox.Web
// Copyright (c) 2024 Grigore Stefan <g_stefan@yahoo.com.hpp>
// Apache License 2.0 <https://opensource.org/license/apache-2-0/>
// SPDX-FileCopyrightText: 2024 Grigore Stefan <g_stefan@yahoo.com.hpp>
// SPDX-License-Identifier: Apache-2.0

namespace Andora\DataSources {

    defined("XYO_WEB") or die("Forbidden");
    require_once("./_site/xyo/web/web.php");

    class TableUserGroupXUserGroups extends \XYO\Web\DataSource\Table
    {
        public $id;
        public $user_group_id_super;
        public $user_group_id;
        public $enabled;

        public function __construct($connection = null)
        {
            parent::__construct($connection);
        }

        public static function descriptor(&$info)
        {
            $info->name = "user-group-x-user-groups";
            $info->primaryKey = "id";
            $info->fields = array(
                "id" => array("bigint", "DEFAULT", "unsigned", "autoIncrement"),
                "user_group_id_super" => array("bigint", 0, "unsigned"),
                "user_group_id" => array("bigint", 0, "unsigned"),
                "enabled" => array("int", 0, "unsigned")
            );
            $info->indexes = array(
                "user_group_id_super",
                "user_group_id"
            );
        }

    }

}

