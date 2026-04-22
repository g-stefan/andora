<?php
// Andora
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Apache-2.0

namespace Andora\User\SignUp {

    defined("XYO_WEB") or die("Forbidden");

    require_once("./_site/xyo/web/web.php");
    require_once("./_site/andora/models/user.php");
    require_once("./_site/andora/components/message-error.php");
    require_once("./_site/andora/components/message-ok.php");

    require_once("./user/sign-up/form.php");

    use \Andora\Components\MessageError;
    use \Andora\Components\MessageOk;
    use \Andora\Models\User;

    class Page extends \XYO\Web\Page
    {
        protected $form = null;        

        public function init($options = null)
        {
            $this->setTitle("Andora - " . $this->language->get("page.title"));
            $this->form = Form::register($this, "form");
        }

        public function process($options = null)
        {
            if (!$this->isPOST()) {
                return;
            }
            if ($this->form->hasError()) {
                return;
            }

            // ---
            MessageOk::registerAndInit($this, "form", array(
                "title" => $this->language->get("page.title"),
                "message" => $this->language->get("thank-you-for-your-registration...")
            ));
        }

        public function render($options = null)
        {
            $this->renderComponent("form");
        }

    }

    return Page::class;
}
