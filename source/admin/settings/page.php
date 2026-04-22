<?php
// Andora
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Apache-2.0

namespace Andora\Admin\Settings {

    defined("XYO_WEB") or die("Forbidden");

    require_once("./_site/xyo/web/web.php");
    require_once("./_site/andora/models/dashboard.php");
    require_once("./_site/andora/components/message-error.php");
    require_once("./_site/andora/components/message-ok.php");
    require_once("./_site/andora/components/data-table.php");
    require_once("./_site/andora/models/setup.php");

    require_once("./admin/settings/smtp-configuration.php");

    use \Andora\Models\Setup;
    use \Andora\Models\Dashboard;
    use \Andora\Components\MessageError;
    use \Andora\Components\MessageOk;
    use \Andora\Components\DataTable;

    class Page extends \XYO\Web\Page
    {
        protected $formSMTP = null;

        public function init($options = null)
        {
            $this->setTitle("Admin - Settings");
            Dashboard::setApplicationTitle("Settings");

            SMTPConfiguration::register($this, "smtp-configuration");
        }

        public function process($options = null)
        {
            
        }

        public function render($options = null)
        { ?>

            <?php $this->renderComponent("smtp-configuration"); ?>

        <?php }

    }

    return Page::class;
}
