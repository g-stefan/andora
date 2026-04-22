<?php
// Andora
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Apache-2.0

namespace Andora\Setup {

    defined("XYO_WEB") or die("Forbidden");

    require_once("./_site/xyo/web/web.php");
    require_once("./_site/andora/components/form.php");
    require_once("./_site/andora/components/input-name.php");
    require_once("./_site/andora/components/input-email.php");
    require_once("./_site/andora/components/input-password.php");

    use \Andora\Components\InputName;
    use \Andora\Components\InputEmail;
    use \Andora\Components\InputPassword;

    class FormUser extends \Andora\Components\Form
    {

        public function init($options = null)
        {
            parent::init($options);

            $this->value->name = null;
            $this->value->email = null;
            $this->value->password = null;
            $this->value->passwordConfirmation = null;

            InputName::register($this, "name", array(
                "form" => &$this,
                "name" => "name",                
                "required" => true
            ));

            InputEMail::register($this, "email", array(
                "form" => &$this,
                "name" => "email",                
                "placeholder" => "mail@example.com",
                "required" => true
            ));

            InputPassword::register($this, "password", array(
                "form" => &$this,
                "name" => "password",                
                "hasLabel" => true,
                "required" => true
            ));

            InputPassword::register($this, "passwordConfirmation", array(
                "form" => &$this,
                "name" => "passwordConfirmation",                
                "required" => true
            ));

        }

        public function process($options = null)
        {
            if ($this->isInit()) {
                return;
            }

            if (!$this->isPOST()) {
                return;
            }

            if ($this->hasError()) {
                return;
            }

            if (!(strcmp($this->value->password, $this->value->passwordConfirmation) == 0)) {
                $this->setElementError("password", true);
                $this->setElementError("passwordConfirmation", true);
                $this->setElementAlert("password", true);
                return;
            }

            $this->sessionSet("setup_name", $this->value->name);
            $this->sessionSet("setup_email", $this->value->email);
            $this->sessionSet("setup_password", $this->value->password);

            $this->setIsDone(true);
        }

        public function renderAJAX($options = null)
        {
            ?>

            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold">Setup</h1>
                <p class="mt-3 text-lg text-base-content/70">
                    <?php $this->language->render("let-s-setup-the-administrator-account"); ?>
                </p>
            </div>

            <?php

            $this->renderFormAJAX(function () {
                ?>

                <?php if ($this->hasAlert()) { ?>
                    <div role="alert" class="alert alert-error">
                        <?php $this->renderComponent("icons", array("icon" => "circle-x", "class" => "text-xl")); ?>
                        <span><?php $this->language->render("error-the-passwords-must-match"); ?></span>
                    </div>
                <?php } ?>

                <?php $this->renderComponent("name"); ?>
                <?php $this->renderComponent("email"); ?>
                <?php $this->renderComponent("password"); ?>
                <?php $this->renderComponent("passwordConfirmation"); ?>

                <button type="submit" class="btn btn-neutral">
                    <?php $this->language->render("create-administrator-account"); ?>
                </button>

                <?php
            }, "flex flex-col gap-2");

        }

        public function renderContainer($options = null)
        {
            echo "<div class=\"w-full max-w-[400px] flex flex-col\" id=\"" . $this->id . "\">";
            $this->renderAJAX();
            echo "</div>";
        }
    }
}

