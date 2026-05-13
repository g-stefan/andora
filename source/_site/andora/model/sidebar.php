<?php
// Andora
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Apache-2.0

namespace Andora\Model {

    defined("XYO_WEB") or die("Forbidden");
    require_once("./_site/xyo/web/web.php");

    class Sidebar
    {

        public static function getMenu()
        {
            $menu = array(
                "sidebar-content" =>
                    array(
                        "Home" => array(
                            array(false, "circle-check-big", "Dashboard", "admin/dashboard"),
                            array(false, "list-tree", "Lifecycle", "#"),
                            array(false, "chart-no-axes-column", "Analytics", "#"),
                            array(false, "folder", "Projects", "#"),
                            array(false, "user", "User", "admin/user"),
                        ),
                        "Documents" => array(
                            array(false, "database", "Data Library", "#"),
                            array(false, "file-text", "Reports", "#"),
                            array(false, "file-code-2", "Word Assistant", "#"),
                            array(false, "ellipsis", "More", "#"),
                        )
                    ),
                "sidebar-bottom" => array(
                    array(false, "settings", "Settings", "admin/settings"),
                    array(false, "circle-question-mark", "Get Help", "#"),
                    array(false, "search", "Search", "#")
                )
            );

            $info = \XYO\Web\Info::instance();
            foreach ($menu["sidebar-content"] as &$submenu) {
                foreach ($submenu as &$row) {
                    $row[0] = false;
                    if ($row[3] == $info->path) {
                        $row[0] = true;
                    }
                    if (!strncmp($row[3], "#", 1) == 0) {
                        $row[3] = $info->sitePath . $row[3];
                    }
                }
            }
            foreach ($menu["sidebar-bottom"] as &$row) {
                $row[0] = false;
                if ($row[3] == $info->path) {
                    $row[0] = true;
                }
                if (!strncmp($row[3], "#", 1) == 0) {
                    $row[3] = $info->sitePath . $row[3];
                }
            }

            return $menu;
        }

    }

}
