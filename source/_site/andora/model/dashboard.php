<?php
// Andora
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Apache-2.0


namespace Andora\Model {

    defined("XYO_WEB") or die("Forbidden");

    class Dashboard
    {
        private static $instance = null;

        public $applicationTitle = null;

        protected function __construct()
        {
            $this->applicationTitle = "";
        }

        public static function instance()
        {
            return self::$instance;
        }

        public static function init()
        {
            self::$instance = new Dashboard();
        }

        public static function checkInit()
        {
            if(is_null(self::$instance)) {
                self::init();
            }
        }

        public static function setApplicationTitle($title)
        {
            self::checkInit();
            self::$instance->applicationTitle = $title;
        }

    }
}
