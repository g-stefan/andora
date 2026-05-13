<?php
// Andora
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Apache-2.0

namespace Andora\Admin\Settings {

    defined("XYO_WEB") or die("Forbidden");

    require_once("./_site/xyo/web/web.php");
    require_once("./_site/andora/model/dashboard.php");
    require_once("./_site/andora/component/message-error.php");
    require_once("./_site/andora/component/message-ok.php");
    require_once("./_site/andora/component/data-table.php");
    require_once("./_site/andora/model/setup.php");

    require_once("./admin/settings/smtp-configuration.php");

    use \Andora\Model\Setup;
    use \Andora\Model\Dashboard;
    use \Andora\Component\MessageError;
    use \Andora\Component\MessageOk;
    use \Andora\Component\DataTable;

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
