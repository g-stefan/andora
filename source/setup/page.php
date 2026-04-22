<?php
// Andora
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Apache-2.0

namespace Andora\Setup {

    defined("XYO_WEB") or die("Forbidden");

    require_once("./_site/xyo/web/web.php");
    require_once("./_site/xyo/lucide-icons/lucide-icons.php");
    require_once("./_site/andora/components/theme-change.php");
    require_once("./_site/andora/components/logo-andora.php");
    require_once("./_site/andora/components/message-error.php");
    require_once("./_site/andora/components/message-ok.php");
    require_once("./_site/andora/models/setup.php");
    require_once("./_site/andora/models/user.php");

    require_once("./setup/form-select-language.php");
    require_once("./setup/form-select-database.php");
    require_once("./setup/form-user.php");

    use \XYO\LucideIcons;
    use \Andora\Components\ThemeChange;
    use \Andora\Components\LogoAndora;
    use \Andora\Components\MessageError;
    use \Andora\Components\MessageOk;
    use \Andora\Models\Setup;
    use \Andora\Models\User;

    class Page extends \XYO\Web\Page
    {
        protected $form = null;
        protected $step = null;
        protected $init = null;
        protected $reason = null;

        public function init($options = null)
        {
            $this->step = $this->sessionGet("setup_step", "select-language");
            $this->init = false;

            // ---

            $language = $this->sessionGet("setup_language", "en-US");
            $this->setLanguage($language);
            $this->loadLanguage(true);

            //---

            $this->setTitle("Andora - Setup");
            LucideIcons::register($this, "icons");
            ThemeChange::register($this, "theme-change");
            LogoAndora::register($this, "logo-andora");

        }

        public function setStep($step)
        {
            $this->step = $step;
            $this->sessionSet("setup_step", $step);
            $this->init = true;
            $this->setFormComponent();
        }

        public function setFormComponent()
        {
            if ($this->step == "select-language") {
                $this->form = FormSelectLanguage::registerAndInit($this, "form", array("init" => $this->init));
            }
            if ($this->step == "select-database") {
                $this->form = FormSelectDatabase::registerAndInit($this, "form", array("init" => $this->init));
            }
            if ($this->step == "user") {
                $this->form = FormUser::registerAndInit($this, "form", array("init" => $this->init));
            }
            if ($this->step == "ok") {
                $this->form = MessageOk::registerAndInit($this, "form", array(
                    "title" => "Setup",
                    "message" => $this->language->get("thank-you")
                ));
            }
            if ($this->step == "error") {
                $this->form = MessageError::registerAndInit($this, "form", array(
                    "title" => "Setup",
                    "message" => $this->language->get("an-error-occurred"),
                    "reason" => $this->reason
                ));
            }
        }

        public function process($options = null)
        {

            if (!$this->isPOST()) {
                $this->setStep("select-language");
                return;
            }

            $this->setFormComponent();

            if (!$this->form->isDone()) {
                return;
            }

            if ($this->step == "select-language") {
                if (
                    !Setup::writeLanguageConfigFile(
                        $this->sessionGet("setup_language"),
                        $this->reason
                    )
                ) {
                    $this->setStep("error");
                    return;
                }

                $this->setStep("select-database");
                return;
            }

            if ($this->step == "select-database") {

                if (
                    !Setup::writeDatabaseConfigFile(
                        $this->sessionGet("setup_databaseType"),
                        $this->sessionGet("setup_username"),
                        $this->sessionGet("setup_password"),
                        $this->sessionGet("setup_databaseServer"),
                        $this->sessionGet("setup_databasePort"),
                        $this->sessionGet("setup_databaseName"),
                        $this->sessionGet("setup_tablePrefix"),
                        $this->reason
                    )
                ) {
                    $this->setStep("error");
                    return;
                }

                if (!Setup::checkDatabaseConnection($this->reason)) {
                    $this->setStep("error");
                    return;
                }

                if (!Setup::dsCreateStorage($this->reason)) {
                    $this->setStep("error");
                    return;
                }

                if (!Setup::writeUserConfigFile($this->reason)) {
                    $this->setStep("error");
                    return;
                }

                if (!Setup::writeConfiguredConfigFile($this->reason)) {
                    $this->setStep("error");
                    return;
                }

                $this->setStep("user");
                return;
            }

            if ($this->step == "user") {
                if (
                    !User::addUser(
                        $this->sessionGet("setup_name"),
                        $this->sessionGet("setup_email"),
                        $this->sessionGet("setup_password"),
                        $this->reason
                    )
                ) {
                    $this->setStep("error");
                    return;
                }

                $this->setStep("ok");
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
