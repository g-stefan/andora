<?php
// Andora
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Apache-2.0

namespace Andora\Admin\Settings {

    defined("XYO_WEB") or die("Forbidden");

    require_once("./_site/xyo/web/web.php");
    require_once("./_site/xyo/lucide-icons/lucide-icons.php");
    require_once("./_site/andora/components/form.php");
    require_once("./_site/andora/models/setup.php");

    use \XYO\LucideIcons;
    use \Andora\Models\Setup;

    class SMTPFormTestConfiguration extends \Andora\Components\Form
    {

        public function init($options = null)
        {
            parent::init($options);

            LucideIcons::register($this, "icons");
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

            $this->renderFormAJAX(function () {
                ?>

                <button type="submit" class="btn btn-neutral">
                    <?php $this->language->render("smtp-form-test-configuration.submit"); ?>
                </button>

                <?php
            }, "flex flex-col gap-2");

        }

        public function renderContainer($options = null)
        { ?>
            <div class="relative w-full flex flex-col mt-4 text-center" id="<?php echo $this->id; ?>">
                <?php $this->renderAJAX(); ?>
            </div>
            <?php
        }
    }
}
