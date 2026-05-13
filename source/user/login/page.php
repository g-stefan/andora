<?php
// Andora
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Apache-2.0

namespace Andora\User\Login {

    defined("XYO_WEB") or die("Forbidden");

    require_once("./_site/xyo/web/web.php");
    require_once("./_site/andora/component/message-error.php");
    require_once("./_site/andora/component/message-ok.php");
    require_once("./_site/andora/model/user.php");

    require_once("./user/login/form.php");

    use \Andora\Component\MessageError;
    use \Andora\Component\MessageOk;
    use \Andora\Model\User;

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

            $reason = "";
            if (!User::login($this->form->value->email, $this->form->value->password, $reason)) {
                $this->form->setElementError("email", true);
                $this->form->setElementError("password", true);
                $this->form->setElementAlert("email", true);
                return;
            }

            // ---
            MessageOk::registerAndInit($this, "form", array(
                "title" => $this->language->get("page.title"),
                "message" => $this->language->get("login-successfully")
            ));
            
            if ($this->sessionGet("login_as_admin", false)) {
                $this->sessionSet("login_as_admin", false);
                if ($this->isAJAX()) {
                    $this->view->renderJS(function () {
                        echo "setTimeout(function(){";
                        echo "document.location.href=\"" . $this->site . "admin/dashboard\";";
                        echo "},1500);";
                    });
                }
            }

        }

        public function render($options = null)
        {
            $this->renderComponent("form");
        }

    }

    return Page::class;
}
