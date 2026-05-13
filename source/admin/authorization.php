<?php
// XYO.Web
// Copyright (c) 2024 Grigore Stefan <g_stefan@yahoo.com>
// MIT License (MIT) <http://opensource.org/licenses/MIT>
// SPDX-FileCopyrightText: 2024 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: MIT

namespace Andora\Admin {

    defined("XYO_WEB") or die("Forbidden");

    require_once("./_site/xyo/web/web.php");
    require_once("./_site/andora/model/user.php");

    use \Andora\Model\User;

    class Authorization extends \XYO\Web\Authorization
    {


        public function setHeaders()
        {
            $info = \XYO\Web\Info::instance();
            $config = \XYO\Web\Config::instance();

            $reason = "";
            $isAuthorized = false;            
            if ($config->get("configured")) {
                if (User::checkCurrentSession($reason)) {                    
                    $isAuthorized = true;
                }
            }            

            if (!$isAuthorized) {
                $this->sessionSet("login_as_admin", true);
                header("Location: " . $info->sitePath . "user/login", true, 302);
                exit(0);
            }

            if (strcmp($info->path, "admin") == 0) {
                header("Location: " . $info->sitePath . "admin/dashboard", true, 302);
            }

        }

    }

    return Authorization::class;
}