<?php
// Andora
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Apache-2.0

namespace Andora\Layouts {
    defined("XYO_WEB") or die("Forbidden");

    require_once("./_site/xyo/web/web.php");
    require_once("./_site/xyo/overlay-scrollbars/overlayscrollbars.php");
    require_once("./_site/andora/client.php");
    require_once("./_site/andora/components/logo-andora.php");
    require_once("./_site/andora/components/theme-change.php");

    use \XYO\OverlayScrollbars;
    use \Andora\Client;
    use \Andora\Components\LogoAndora;
    use \Andora\Components\ThemeChange;

    class Setup extends \XYO\Web\Layout
    {
        public function init($options = null)
        {
            OverlayScrollbars::register($this);
            Client::register($this);
            LogoAndora::register($this, "logo-andora");
            ThemeChange::register($this, "theme-change");
        }

        public function renderLayout(&$page = null)
        { ?><!DOCTYPE html>
            <html <?php
            $this->renderLanguage();
            $this->renderHTMLClasses();
            ?>>

            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <?php $this->renderHead(); ?>
            </head>

            <body <?php $this->renderBodyClasses(); ?>>
                <div class="w-full lg:grid lg:min-h-[600px] lg:grid-cols-2 xl:min-h-[800px]">
                    <div>
                        <div class="flex justify-end py-3 px-3"><?php $this->renderComponent("theme-change"); ?></div>
                        <div class="flex flex-col items-center justify-center">
                            <?php $this->renderPage($page); ?>
                        </div>
                        <div class="h-16"></div>
                    </div>
                    <div class="lg:block"><?php $this->renderComponent("logo-andora"); ?></div>
                </div>
                <?php $this->renderScripts(); ?>
            </body>

            </html>

        <?php }

    }

}
