<?php
// Andora
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Apache-2.0

namespace Andora\Components {

    defined("XYO_WEB") or die("Forbidden");

    require_once("./_site/xyo/web/web.php");
    class Form extends \XYO\Web\ComponentForm
    {
        public $value = null;
        protected $isDone_ = null;
        protected $isInit_ = null;

        public function init($options = null)
        {
            parent::init($options);

            if (!is_null($options)) {
                $this->isInit_ = false;
                if (array_key_exists("init", $options)) {
                    $this->isInit_ = $options["init"];
                }
            }

            $this->value = new \stdClass();
            $this->isDone_ = false;
        }

        protected function setIsDone($value)
        {
            $this->isDone_ = $value;
        }

        public function isDone()
        {
            return $this->isDone_;
        }

        protected function setIsInit($value)
        {
            $this->isInit_ = $value;
        }

        public function isInit()
        {
            return $this->isInit_;
        }

    }

}
