<?php
// Andora
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Apache-2.0

namespace Andora\User\SignUp {

    defined("XYO_WEB") or die("Forbidden");

    require_once("./_site/xyo/web/web.php");
    require_once("./_site/xyo/lucide-icons/lucide-icons.php");
    require_once("./_site/andora/components/form.php");
    require_once("./_site/andora/components/input-name.php");
    require_once("./_site/andora/components/input-email.php");
    require_once("./_site/andora/components/input-password.php");

    use \XYO\LucideIcons;
    use \Andora\Components\InputName;
    use \Andora\Components\InputEmail;
    use \Andora\Components\InputPassword;

    class Form extends \Andora\Components\Form
    {

        public function init($options = null)
        {
            parent::init($options);

            $this->value->firstName = null;
            $this->value->lastName = null;
            $this->value->email = null;
            $this->value->password = null;
            $this->value->passwordConfirmation = null;

            LucideIcons::register($this, "icons");
            
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

        }

        public function renderAJAX($options = null)
        {
            ?>

            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold"><?php $this->language->render("page.title"); ?></h1>
                <p class="mt-3 text-lg text-base-content/70">
                    <?php $this->language->render("enter-your-email-below-to-register-your-account"); ?>
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

                <button class="btn btn-neutral">
                    <?php $this->language->render("sign-up"); ?>
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
