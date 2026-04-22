<?php
// Andora
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Apache-2.0

namespace Andora {

    defined("XYO_WEB") or die("Forbidden");

    require_once("./_site/xyo/web/web.php");

    class Client extends \XYO\Web\Component
    {

        protected static $name = "andora.client";

        public function init($options = null)
        {
            $this->view->cssLinks->removeGroup(self::$name);
            $this->view->cssLinks->set(self::$name, $this->site . "_site/andora/client/andora.css");
            $this->view->jsLinks->set(self::$name, $this->site . "_site/andora/client/andora.js", "defer");
        }

    }

}
