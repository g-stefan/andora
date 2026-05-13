<?php
// Andora
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Apache-2.0

namespace Andora\Admin\Logout {

    defined("XYO_WEB") or die("Forbidden");

    require_once("./_site/xyo/web/web.php");
    require_once("./_site/andora/component/message-error.php");
    require_once("./_site/andora/component/message-ok.php");
    require_once("./_site/andora/model/user.php");

    use \Andora\Component\MessageError;
    use \Andora\Component\MessageOk;
    use \Andora\Model\User;

    class Page extends \XYO\Web\Page
    {
        protected $form = null;        

        public function init($options = null)
        {
            $this->setTitle("Andora - " . $this->language->get("page.title"));
            MessageOk::register($this, "form",array(
                "title" => $this->language->get("page.title"),
                "message" => $this->language->get("logout-successfully")
            ));
        }

        public function process($options = null)
        {

            $reason = "";
            if (!User::logoutCurrentSession($reason)) {
                MessageError::registerAndInit($this, "form", array(
                    "title" => $this->language->get("page.title"),
                    "message" => $this->language->get("an-error-occurred"),
                    "reason" => $reason
                    ));
                return;
            }

        }

        public function render($options = null)
        {
            $this->renderComponent("form");
        }


    }

    return Page::class;
}
