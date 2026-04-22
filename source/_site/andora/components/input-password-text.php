<?php
// Andora
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Apache-2.0

namespace Andora\Components {

    defined("XYO_WEB") or die("Forbidden");

    require_once("./_site/xyo/web/web.php");
    class InputPasswordText extends \XYO\Web\Component
    {
        public $form = null;
        public $name = null;
        public $legend = null;
        public $placeholder = null;
        public $required = null;
        public $autocomplete = null;
        public $value = null;

        public function init($options = null)
        {
            $this->form = $options["form"];
            $this->name = $options["name"];
            $this->legend = null;
            if (array_key_exists("legend", $options)) {
                $this->legend = $options["legend"];
            }
            $this->placeholder = null;
            if (array_key_exists("placeholder", $options)) {
                $this->placeholder = $options["placeholder"];
            }
            $this->required = false;
            if (array_key_exists("required", $options)) {
                if ($options["required"]) {
                    $this->required = true;
                }
            }
            $this->autocomplete = null;
            if (array_key_exists("autocomplete", $options)) {
                if ($options["autocomplete"]) {
                    $this->autocomplete = $options["autocomplete"];
                }
            }

            // ---
            $this->value = "";
            if (array_key_exists("initialValue", $options)) {
                $this->value = $options["initialValue"];
                $this->form->value->{$this->name} = $this->value;
            }

        }

        public function process($options = null)
        {
            if ($this->form->isInit()) {
                return;
            }
            if ($this->isPOST()) {
                $this->getValue();
                if ($this->validateValue()) {
                    $this->form->value->{$this->name} = $this->value;
                }
            }
        }

        public function render($options = null)
        {
            $this->loadLanguageFromPath("./_site/andora/components", "input-password-text");

            if (is_null($this->legend)) {
                $this->legend = $this->language->get($this->name . ".legend");
            }
            if (is_null($this->placeholder)) {
                $this->placeholder = $this->language->get($this->name . ".placeholder");
            }

            $attribute = "";
            if ($this->form->elementHasError($this->name)) {
                $attribute = "aria-invalid=\"true\"";
                $this->view->renderJS(function () {
                    echo "document.getElementById(\"" . $this->id . "\").addEventListener(\"focus\", function() {";
                    echo "this.removeAttribute(\"aria-invalid\");";
                    echo "});";
                });
            }

            $required = "";
            if ($this->required) {
                $required = "required";
            }

            $autocomplete = "";
            if (!is_null($this->autocomplete)) {
                $autocomplete = "autocomplete=\"" . $this->autocomplete . "\"";
            }

            ?>

            <fieldset class="fieldset w-full">
                <legend class="fieldset-legend text-base"><?php echo $this->legend; ?></legend>
                <input type="password" name="<?php echo $this->name; ?>" class="input validator w-full" <?php echo $attribute; ?>
                    placeholder="<?php echo $this->placeholder; ?>" <?php
                       echo $required;
                       echo " ";
                       echo $autocomplete;
                       ?> id="<?php echo $this->id; ?>" title="<?php $this->language->render("title.password"); ?>"
                    value="<?php echo $this->value; ?>" />
                <p class="validator-hint hidden">
                    <?php $this->language->render("required"); ?>.
                </p>

            </fieldset>

            <?php
        }

        public function validateValue()
        {
            if (strlen($this->required) > 0) {
                if (strlen($this->value) == 0) {
                    $this->form->setElementError($this->name, true);
                    return false;
                }
            }
            return true;
        }

        public function getValue()
        {
            $this->value = $this->form->getElementValueString($this->name, "");
        }

    }

}
