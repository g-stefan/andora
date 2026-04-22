<?php
// Andora
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Apache-2.0

namespace Andora\Admin\Settings {

    defined("XYO_WEB") or die("Forbidden");

    require_once("./_site/xyo/web/web.php");
    require_once("./_site/xyo/lucide-icons/lucide-icons.php");
    require_once("./_site/andora/components/form.php");
    require_once("./_site/andora/components/input-text.php");
    require_once("./_site/andora/components/input-password-text.php");

    use \XYO\LucideIcons;
    use \Andora\Components\InputText;
    use \Andora\Components\InputPasswordText;

    class SMTPForm extends \Andora\Components\Form
    {

        public function init($options = null)
        {
            parent::init($options);

            $this->value->name = null;
            $this->value->server = null;
            $this->value->username = null;
            $this->value->password = null;
            $this->value->port = null;

            $name = "";
            $server = "";
            $username = "";
            $port = "465";
            $config = \XYO\Web\Config::instance();
            if (property_exists($config, "smtp")) {
                $name = $config->smtp->name;
                $server = $config->smtp->server;
                $username = $config->smtp->username;
                $port = $config->smtp->port;                
            }

            LucideIcons::register($this, "icons");

            InputText::register($this, "name", array(
                "form" => &$this,
                "name" => "name",
                "required" => true,
                "initialValue" => $name
            ));

            InputText::register($this, "username", array(
                "form" => &$this,
                "name" => "username",
                "required" => true,
                "autocomplete" => "off",
                "initialValue" => $username
            ));

            InputPasswordText::registerAndInit($this, "password", array(
                "form" => &$this,
                "name" => "password",
                "required" => true,
                "autocomplete" => "new-password"
            ));

            InputText::register($this, "server", array(
                "form" => &$this,
                "name" => "server",
                "required" => true,
                "initialValue" => $server
            ));

            InputText::register($this, "port", array(
                "form" => &$this,
                "name" => "port",
                "required" => true,
                "initialValue" => $port
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

            $this->setIsDone(true);
        }

        public function renderAJAX($options = null)
        {
            ?>

            <div class="text-center mb-3">
                <p class="mt-3 text-base text-base-content/70">
                    <?php $this->language->render("smtp.form.info"); ?>
                </p>
            </div>

            <?php

            $this->renderFormAJAX(function () {
                ?>

                <?php if ($this->hasAlert()) { ?>
                    <div role="alert" class="alert alert-error">
                        <?php $this->renderComponent("icons", array("icon" => "circle-x", "class" => "text-xl")); ?>
                        <span><?php $this->language->render("from-smtp.error"); ?></span>
                    </div>
                <?php } ?>

                <?php $this->renderComponent("name"); ?>
                <?php $this->renderComponent("username"); ?>
                <?php $this->renderComponent("password"); ?>
                <?php $this->renderComponent("server"); ?>
                <?php $this->renderComponent("port"); ?>

                <button type="submit" class="btn btn-neutral">
                    <?php $this->language->render("smtp-form.submit"); ?>
                </button>

                <?php
            }, "flex flex-col gap-2");

        }

        public function renderContainer($options = null)
        { ?>
            <div class="relative w-full flex flex-col min-h-100" id="<?php echo $this->id; ?>">
                <?php $this->renderAJAX(); ?>
            </div>
            <?php
        }
    }
}
