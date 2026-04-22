<?php
// Andora
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Apache-2.0

namespace Andora\Admin {
    defined("XYO_WEB") or die("Forbidden");

    require_once("./_site/xyo/web/web.php");
    require_once("./_site/xyo/overlay-scrollbars/overlayscrollbars.php");
    require_once("./_site/xyo/lucide-icons/lucide-icons.php");
    require_once("./_site/xyo/inter-font/inter-font.php");
    require_once("./_site/andora/client.php");
    require_once("./_site/andora/components/theme-change.php");
    require_once("./_site/andora/components/logo-andora.php");
    require_once("./_site/andora/components/sidebar.php");
    require_once("./_site/andora/models/dashboard.php");

    use \Andora\Client;
    use \XYO\OverlayScrollbars;
    use \XYO\LucideIcons;
    use \XYO\InterFont;
    use \Andora\Components\ThemeChange;
    use \Andora\Components\LogoAndora;
    use \Andora\Components\Sidebar;
    use \Andora\Models\Dashboard;

    class Layout extends \XYO\Web\Layout
    {
        public function init($options = null)
        {
            Dashboard::init();

            Client::register($this);
            OverlayScrollbars::register($this);
            InterFont::register($this, "inter-font");
            LucideIcons::register($this, "icons");
            ThemeChange::register($this, "theme-change");
            LogoAndora::register($this, "logo-andora");

            Sidebar::register($this, "sidebar");

            $this->view->bodyClasses->set("bg-base-100 text-base-content min-h-screen flex antialiased overflow-hidden");
        }

        public function renderLayout(&$page = null)
        { 
            $dashboard = Dashboard::instance();

            ?><!DOCTYPE html>
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
                <?php $this->renderComponent("sidebar"); ?>

                <!-- Main Content Container -->
                <main class="flex-1 flex flex-col h-screen overflow-hidden relative z-10 w-full">

                    <!-- Top Header -->
                    <header class="h-[60px] flex items-center px-4 md:px-8 border-b border-base-300 shrink-0 gap-2">
                        <!-- Sidebar Toggle Button -->
                        <button id="sidebar-toggle"
                            class="btn btn-ghost btn-sm btn-square text-base-content/70 hover:text-base-content transition-colors"
                            aria-label="Toggle Sidebar">
                            <?php $this->renderComponent("icons", array("icon" => "menu", "class" => "text-xl")); ?>
                        </button>

                        <div class="w-px h-5 bg-base-300 mx-2 hidden md:block"></div>
                        <h1 class="font-semibold text-base-content text-sm tracking-tight"><?php echo $dashboard->applicationTitle; ?></h1>
                        <div class="ml-auto">
                            <?php $this->renderComponent("theme-change"); ?>
                        </div>
                    </header>

                    <!-- Main Scrollable Area -->
                    <div class="flex-1 overflow-y-auto p-4 md:p-8">
                        <?php $this->renderPage($page); ?>
                    </div>
                </main>


                <?php $this->renderScripts(); ?>
            </body>

            </html>

        <?php }

    }

    return Layout::class;
}

