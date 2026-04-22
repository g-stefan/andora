<?php
// Andora
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Apache-2.0

namespace Andora\Setup {

    defined("XYO_WEB") or die("Forbidden");

    require_once("./_site/xyo/web/web.php");
    require_once("./_site/andora/components/form.php");
    require_once("./_site/andora/components/input-select.php");
    require_once("./_site/andora/components/input-text.php");
    require_once("./_site/andora/components/input-password-text.php");

    use \Andora\Components\InputSelect;
    use \Andora\Components\InputText;
    use \Andora\Components\InputPasswordText;

    class FormSelectDatabase extends \Andora\Components\Form
    {

        public function init($options = null)
        {
            parent::init($options);

            $this->value->databaseType = null;
            $this->value->databaseName = null;
            $this->value->username = null;
            $this->value->password = null;
            $this->value->databaseServer = null;
            $this->value->databasePort = null;
            $this->value->tablePrefix = null;

            InputSelect::register($this, "databaseType", array(
                "form" => &$this,
                "name" => "databaseType",
                "required" => true,
                "list" => array(
                    "mysql" => "MySQL",
                    "postgresql" => "PostgreSQL",
                    "sqlite" => "SQLite",
                ),
                "initialValue" => $this->sessionGet("setup_databaseType", "mysql")
            ));

        }

        public function process($options = null)
        {
            if (!$this->isInit()) {
                $action = $this->getElementValueString("action", "select");
                if ($action == "select") {
                    $this->setIsInit(true);
                }
            }

            if ($this->value->databaseType == "mysql" || $this->value->databaseType == "postgresql") {
                $defaultPort = 3306;
                if ($this->value->databaseType == "postgresql") {
                    $defaultPort = 5432;
                }

                InputText::registerAndInit($this, "databaseName", array(
                    "form" => &$this,
                    "name" => "databaseName",
                    "placeholder" => "andora",
                    "required" => true,
                    "initialValue" => $this->sessionGet("setup_databaseName", "")
                ));

                InputText::registerAndInit($this, "username", array(
                    "form" => &$this,
                    "name" => "username",
                    "required" => true,
                    "initialValue" => $this->sessionGet("setup_username", ""),
                    "autocomplete" => "off"
                ));

                InputPasswordText::registerAndInit($this, "password", array(
                    "form" => &$this,
                    "name" => "password",
                    "initialValue" => $this->sessionGet("setup_password", ""),
                    "autocomplete" => "new-password"
                ));

                InputText::registerAndInit($this, "databaseServer", array(
                    "form" => &$this,
                    "name" => "databaseServer",
                    "required" => true,
                    "initialValue" => $this->sessionGet("setup_databaseServer", "localhost")
                ));

                InputText::registerAndInit($this, "databasePort", array(
                    "form" => &$this,
                    "name" => "databasePort",
                    "required" => true,
                    "initialValue" => $this->sessionGet("setup_databasePort", $defaultPort)
                ));

                InputText::registerAndInit($this, "tablePrefix", array(
                    "form" => &$this,
                    "name" => "tablePrefix",
                    "placeholder" => "",
                    "initialValue" => $this->sessionGet("setup_tablePrefix", "")
                ));

            }

            if ($this->isInit()) {
                return;
            }

            if (!$this->isPOST()) {
                return;
            }

            if ($this->hasError()) {
                return;
            }

            $this->sessionSet("setup_databaseType", $this->value->databaseType);
            if ($this->value->databaseType == "mysql" || $this->value->databaseType == "postgresql") {
                $this->sessionSet("setup_databaseName", $this->value->databaseName);
                $this->sessionSet("setup_username", $this->value->username);
                $this->sessionSet("setup_password", $this->value->password);
                $this->sessionSet("setup_databaseServer", $this->value->databaseServer);
                $this->sessionSet("setup_databasePort", $this->value->databasePort);
                $this->sessionSet("setup_tablePrefix", $this->value->tablePrefix);
            }

            $this->setIsDone($this->getElementValueString("action") == "submit");
        }

        public function renderAJAX($options = null)
        {
            ?>

            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold">Setup</h1>
                <p class="mt-3 text-lg text-base-content/70">
                    <?php $this->language->render("let-s-configure-the-database"); ?>
                </p>
            </div>

            <?php

            $this->renderFormAJAX(function () {
                ?>
                <input type="hidden" name="action" value="select" id="<?php echo $this->getElementId("action"); ?>" />

                <?php

                $this->renderComponent("databaseType");
                if (($this->value->databaseType == "mysql") || ($this->value->databaseType == "postgresql")) {
                    $this->renderComponent("databaseName");
                    $this->renderComponent("username");
                    $this->renderComponent("password");
                    $this->renderComponent("databaseServer");
                    $this->renderComponent("databasePort");
                    $this->renderComponent("tablePrefix");
                }

                if ($this->value->databaseType == "sqlite") {
                    $this->language->render("sqlite-database-stored-in-repository");
                }

                ?>

                <button type="submit" class="btn btn-neutral" id="<?php echo $this->getElementId("submit"); ?>">
                    <?php echo $this->language->get("configure"); ?>
                </button>

                <?php

                $this->view->renderJS(function () {
                    echo "document.getElementById(\"" . $this->getComponentId("databaseType") . "\").addEventListener(\"change\",function(e){";
                    echo "e.preventDefault();";
                    $this->renderAJAXRequestPostForm();
                    echo "});";
                    echo "document.getElementById(\"" . $this->getElementId("submit") . "\").addEventListener(\"click\",function(e){";
                    echo "e.preventDefault();";
                    echo "document.getElementById(\"" . $this->getElementId("action") . "\").value=\"submit\";";
                    $this->renderAJAXRequestPostForm();
                    echo "});";
                });

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

