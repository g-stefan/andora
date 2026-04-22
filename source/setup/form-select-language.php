<?php
// Andora
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Apache-2.0

namespace Andora\Setup {

    defined("XYO_WEB") or die("Forbidden");

    require_once("./_site/xyo/web/web.php");
    require_once("./_site/andora/components/form.php");
    require_once("./_site/andora/components/input-select.php");

    use \Andora\Components\InputSelect;
    class FormSelectLanguage extends \Andora\Components\Form
    {

        public function init($options = null)
        {
            parent::init($options);

            $this->value->language = null;

            InputSelect::register($this, "language", array(
                "form" => &$this,
                "name" => "language",                
                "required" => true,
                "list" => array(
                    "en-US" => "English (United States)",
                    "ro-RO" => "Romana"
                ),
                "initialValue" => $this->sessionGet("setup_language", "en-US")
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

            $this->sessionSet("setup_language", $this->value->language);
            $this->setLanguage($this->value->language);
            $this->loadLanguage(true);
            
            $this->setIsDone($this->getElementValueString("action") == "submit");
        }

        public function renderAJAX($options = null)
        {
            ?>

            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold">Setup</h1>
                <p class="mt-3 text-lg text-base-content/70">
                    <?php $this->language->render("this-will-perform-the-initial-setup"); ?>
                </p>
            </div>

            <?php

            $this->renderFormAJAX(function () {
                ?>
                <input type="hidden" name="action" value="select" id="<?php echo $this->getElementId("action"); ?>" />

                <?php $this->renderComponent("language"); ?>

                <button type="submit" class="btn btn-neutral" id="<?php echo $this->getElementId("submit"); ?>">
                    <?php $this->language->render("begin"); ?>
                </button>

                <?php

                $this->view->renderJS(function () use (&$payload, &$payloadJs) {
                    echo "document.getElementById(\"" . $this->getComponentId("language") . "\").addEventListener(\"change\",function(e){";
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
