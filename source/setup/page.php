<?php
// Andora
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Apache-2.0

namespace Andora\Setup {

    defined("XYO_WEB") or die("Forbidden");

    require_once("./_site/xyo/web/web.php");
    require_once("./_site/xyo/lucide-icons/lucide-icons.php");
    require_once("./_site/andora/component/theme-change.php");
    require_once("./_site/andora/component/logo-andora.php");
    require_once("./_site/andora/component/message-error.php");
    require_once("./_site/andora/component/message-ok.php");
    require_once("./_site/andora/component/container.php");
    require_once("./_site/andora/component/placeholder.php");
    require_once("./_site/andora/model/setup.php");
    require_once("./_site/andora/model/user.php");

    require_once("./setup/form-select-language.php");
    require_once("./setup/form-select-database.php");
    require_once("./setup/form-user.php");

    use \XYO\LucideIcons;
    use \Andora\Component\ThemeChange;
    use \Andora\Component\LogoAndora;
    use \Andora\Component\MessageError;
    use \Andora\Component\MessageOk;
    use \Andora\Component\Container;
    use \Andora\Component\Placeholder;
    use \Andora\Model\Setup;
    use \Andora\Model\User;

    class Page extends \XYO\Web\Page
    {
        protected $comContainer = null;

        public function init($options = null)
        {
            // ---

            $language = $this->getState("lang", "en-US");
            $this->setLanguage($language);
            $this->loadLanguage(true);

            //---

            $this->setTitle("Andora - Setup");
            LucideIcons::register($this, "icons");
            ThemeChange::register($this, "theme-change");
            LogoAndora::register($this, "logo-andora");

            $component = "select-language";
            $config = \XYO\Web\Config::instance();
            if ($config->get("configured", false)) {
                $isMessage = false;
                if ($this->isAjax()) {                    
                    if ($this->request->get("component", "") == "message-ok") {
                        $isMessage = true;
                    }
                }
                if (!$isMessage) {
                    $component = "message-ok";
                    $this->request->set("message", "already-configured");
                }
            }

            $this->comContainer = Container::register($this, "container", array(
                "ajax-component" => true,
                "component" => $component,
                "class" => "w-full flex items-start justify-center"
            ));

            $this->comContainer->setAJAXComponent("select-language", function ($parent, $component) {
                $form = FormSelectLanguage::registerAndInit($parent, $component);
                $form->setOnInit([$this, "onSelectLanguageInit"]);
                $form->setOnSelect([$this, "onSelectLanguage"]);
                $form->setOnSuccess([$this, "onSetLanguage"]);
            });

            $this->comContainer->setAJAXComponent("select-database", function ($parent, $component) {
                $form = FormSelectDatabase::registerAndInit($parent, $component);
                $form->setOnSuccess([$this, "onSelectDatabase"]);
            });

            $this->comContainer->setAJAXComponent("register-user", function ($parent, $component) {
                $form = FormUser::registerAndInit($parent, $component);
                $form->setOnSuccess([$this, "onRegisterUser"]);
            });

            $this->comContainer->setAJAXComponent("message-ok", function ($parent, $component) {
                $message = $this->request->get("message", "");
                MessageOk::register($parent, $component, array(
                    "title" => $this->language->get("page.title"),
                    "message" => $this->language->get($message)
                ));
            });

            $this->comContainer->setAJAXComponent("message-error", function ($parent, $component) {
                $reason = $this->request->get("reason", "");
                MessageError::register($parent, $component, array(
                    "title" => "Setup",
                    "message" => $this->language->get("an-error-occurred"),
                    "reason" => $reason
                ));
            });
        }

        public function messageError($form, $reason)
        {
            $form->setIsDone(false);
            $form->disableRenderAJAX(true);
            $this->view->renderJS(function () use ($reason) {
                $this->comContainer->renderJSRequestGet(array("component" => "message-error", "reason" => $reason));
            });
        }

        public function messageOk($form, $message)
        {
            $form->setIsDone(false);
            $form->disableRenderAJAX(true);
            $this->view->renderJS(function () use ($message) {
                $this->comContainer->renderJSRequestGet(array("component" => "message-ok", "message" => $message));
            });
        }

        public function render($options = null)
        {
            $this->renderComponent("container");
        }

        public function onSelectLanguageInit(&$form)
        {
            $form->value->language = $this->getState("lang", "en-US");
        }

        public function onSelectLanguage(&$form)
        {
            $form->setIsDone(false);
            $form->disableRenderAJAX(true);
            // ---
            $this->setState("lang", $form->value->language);
            $this->view->renderJS(function () {
                $this->comContainer->renderJSRequestGet(array("component" => "select-language"));
            });
        }

        public function onSetLanguage(&$form)
        {
            $form->disableRenderAJAX(true);
            // ---
            $reason = null;
            $isOk = Setup::writeLanguageConfigFile(
                $this->getState("lang", "en-US"),
                $reason
            );

            if (!$isOk) {
                $this->messageError($form, $reason);
                return;
            }

            $this->view->renderJS(function () {
                $this->comContainer->renderJSRequestGet(array("component" => "select-database"));
            });
        }

        public function onSelectDatabase(&$form)
        {
            $form->disableRenderAJAX(true);
            // ---
            $reason = null;
            $isOk = Setup::writeDatabaseConfigFile(
                $form->value->databaseType,
                $form->value->username,
                $form->value->password,
                $form->value->databaseServer,
                $form->value->databasePort,
                $form->value->databaseName,
                $form->value->tablePrefix,
                $reason
            );

            if (!$isOk) {
                $this->messageError($form, $reason);
                return;
            }

            if (!Setup::checkDatabaseConnection($reason)) {
                $this->messageError($form, $reason);
                return;
            }

            if (!Setup::dsCreateStorage($reason)) {
                $this->messageError($form, $reason);
                return;
            }

            if (!Setup::writeUserConfigFile($reason)) {
                $this->messageError($form, $reason);
                return;
            }

            if (!Setup::writeConfiguredConfigFile($reason)) {
                $this->messageError($form, $reason);
                return;
            }

            $this->view->renderJS(function () {
                $this->comContainer->renderJSRequestGet(array("component" => "register-user"));
            });
        }

        public function onRegisterUser(&$form)
        {
            $form->disableRenderAJAX(true);
            // ---
            $reason = null;
            $isOk = User::addUser(
                $form->value->name,
                $form->value->email,
                $form->value->password,
                $reason
            );

            if (!$isOk) {
                $this->messageError($form, $reason);
                return;
            }

            $this->messageOk($form, "thank-you");
        }

    }

    return Page::class;
}
