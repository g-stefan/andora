<?php
// Andora
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Apache-2.0

namespace Andora\Admin\User {

    defined("XYO_WEB") or die("Forbidden");

    require_once("./_site/xyo/web/web.php");
    require_once("./_site/xyo/lucide-icons/lucide-icons.php");
    require_once("./_site/andora/component/form.php");
    require_once("./_site/andora/component/input-name.php");
    require_once("./_site/andora/component/input-email.php");
    require_once("./_site/andora/component/input-password.php");
    require_once("./_site/andora/component/input-password-text.php");

    use \XYO\LucideIcons;
    use \Andora\Component\InputName;
    use \Andora\Component\InputEmail;
    use \Andora\Component\InputPassword;
    use \Andora\Component\InputPasswordText;

    class FormEdit extends \Andora\Component\Form
    {

        public function formInit($options = null)
        {
            $this->value->name = "";
            $this->value->email = "";
            $this->value->password = "";
            $this->value->passwordConfirmation = "";
        }

        public function formInitComponents($options = null)
        {

            LucideIcons::register($this, "icons");

            InputName::register($this, "name", array(
                "name" => "name",
                "required" => true,
                "autofocus" => true,
                "initialValue" => $this->value->name,
                "autocomplete" => "off"
            ));

            InputEMail::register($this, "email", array(
                "name" => "email",
                "placeholder" => "mail@example.com",
                "required" => true,
                "initialValue" => $this->value->email,
                "autocomplete" => "off"
            ));

            InputPasswordText::register($this, "password", array(
                "name" => "password",
                "hasLabel" => true,
                "required" => false,
                "autocomplete" => "new-password"
            ));

            InputPasswordText::register($this, "passwordConfirmation", array(
                "name" => "passwordConfirmation",
                "required" => false,
                "autocomplete" => "off"
            ));

        }

        public function formProcess($options = null)
        {
            if ((strlen($this->value->password) > 0) || (strlen($this->value->passwordConfirmation) > 0)) {
                if (!(strcmp($this->value->password, $this->value->passwordConfirmation) == 0)) {
                    $this->setElementError("password", true);
                    $this->setElementError("passwordConfirmation", true);
                    $this->setElementAlert("password", true);
                    return;
                }
            }
        }

        public function formRenderAJAX($options = null)
        {
            ?>

            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold"><?php $this->language->render("page.title"); ?></h1>
                <p class="mt-3 text-lg text-base-content/70">
                    <?php $this->language->render("edit-user-account"); ?>
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
                    <?php $this->language->render("submit"); ?>
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
