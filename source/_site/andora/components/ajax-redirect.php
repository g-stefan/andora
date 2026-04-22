<?php
// Andora
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Apache-2.0

namespace Andora\Components {

    defined("XYO_WEB") or die("Forbidden");

    require_once("./_site/xyo/web/web.php");

    class AJAXRedirect extends \XYO\Web\Component
    {

        public $redirect = null;
        public function init($options = null)
        {
            $this->redirect = null;
            if (array_key_exists("redirect", $options)) {
                $this->redirect = $options["redirect"];
            }
        }

        public function renderAJAX($options = null)
        {
            if (!is_null($this->redirect)) {
                $this->view->renderJS(function () {
                    $this->redirect->renderAJAXRequestGet();
                });
            }
        }

        public function renderContainer($options = null)
        {
        }

    }

}
