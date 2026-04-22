<?php
// Andora
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Apache-2.0

namespace Andora\Components {

    defined("XYO_WEB") or die("Forbidden");

    require_once("./_site/xyo/web/web.php");
    require_once("./_site/xyo/lucide-icons/lucide-icons.php");

    use \XYO\LucideIcons;

    class Dialog extends \XYO\Web\Component
    {
        public $comObject = null;
        public $comOptions = null;
        public $cssClass = null;

        public function init($options = null)
        {
            LucideIcons::register($this, "icons");

            $this->comObject = null;
            if (array_key_exists("component", $options)) {
                $this->comObject = $options["component"];
            }
            $this->comOptions = null;
            if (array_key_exists("options", $options)) {
                $this->comOptions = $options["options"];
            }
            $this->cssClass = "";
            if (array_key_exists("class", $options)) {
                $this->cssClass = $options["class"];
            }
        }

        public function process($options = null)
        {
            if (!$this->isComponentAJAX()) {
                return;
            }

            $this->comObject->init();
            $this->comObject->initComponents();
            $this->comObject->process();

        }

        public function renderAJAX($options = null)
        {
            $this->comObject->renderContainer($this->comOptions);
        }

        public function renderContainer($options = null)
        { ?>
            <dialog id="<?php echo $this->id; ?>_dialog" class="modal">
                <div class="modal-box">
                    <div id="<?php echo $this->id; ?>" class="<?php echo $this->cssClass; ?>">
                        <?php $this->comObject->renderContainer($this->comOptions); ?>
                    </div>
                    <form method="dialog">
                        <button
                            class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 active:bg-error/10 active:border-error/10 hover:bg-error/10 hover:border-error/80 group/button">
                            <?php $this->renderComponent("icons", array("icon" => "x", "class" => "text-base group-active/button:text-error group-hover/button:text-error")); ?>
                        </button>
                    </form>
                </div>
            </dialog>
            <?php
        }

        public function renderJSOpen($options = null)
        {
            echo "document.getElementById(\"" . $this->id . "_dialog\").showModal();";
        }

        public function renderJSClose($options = null)
        {
            echo "document.getElementById(\"" . $this->id . "_dialog\").close();";
        }
    }

}
