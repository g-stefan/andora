<?php
// Andora
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Apache-2.0

namespace Andora\Admin\Users {

    defined("XYO_WEB") or die("Forbidden");

    require_once("./_site/xyo/web/web.php");
    require_once("./_site/andora/datasources/table-users.php");
    require_once("./_site/andora/models/dashboard.php");
    require_once("./_site/andora/models/user.php");
    require_once("./_site/andora/components/data-table.php");
    require_once("./_site/andora/components/data-table-action.php");
    require_once("./_site/andora/components/dialog.php");
    require_once("./_site/andora/components/message-ok.php");
    require_once("./_site/andora/components/message-error.php");
    require_once("./admin/users/form-add.php");

    use \Andora\DataSources\TableUsers;
    use \Andora\Models\Dashboard;
    use \Andora\Models\User;
    use \Andora\Components\DataTable;
    use \Andora\Components\DataTableAction;
    use \Andora\Components\Dialog;
    use \Andora\Components\MessageOk;
    use \Andora\Components\MessageError;

    class Page extends \XYO\Web\Page
    {

        protected $comForm = null;
        protected $comDialog = null;
        protected $comDataTable = null;

        public function init($options = null)
        {
            $this->setTitle("Admin - Users");
            Dashboard::setApplicationTitle("Users");

            /*$messageOk = MessageOk::register($this, "message-ok",array(
                "title" => $this->language->get("page.title"),
                "message" => $this->language->get("thank-you")
            ));*/
            $this->comForm = FormAdd::register($this, "form");

            $this->comDialog = Dialog::register($this, "dialog-add", array(
                "component" => &$this->comForm,
                "class" => "flex items-start justify-center"
            ));

            $this->comDataTable = DataTable::register($this, "datatable", array(
                "descriptor" => array(
                    "id" => array(
                        "type" => "selector",
                        "name" => "",
                        "primaryKey" => true
                    ),
                    "name" => array(
                        "type" => "text",
                        "name" => "Name",
                        "sort" => "ascendent",
                        "search" => true
                    ),
                    "description" => array(
                        "type" => "text",
                        "name" => "Description",
                        "search" => true
                    ),
                    "email" => array(
                        "type" => "text",
                        "name" => "E-Mail",
                        "search" => true
                    ),
                    "enabled" => array(
                        "type" => "text",
                        "name" => "Enabled"
                    ),
                    "is_confirmed" => array(
                        "type" => "text",
                        "name" => "Is confirmed"
                    ),
                    "#" => array(
                        "type" => "component",
                        "select" => false,
                        "name" => "",
                        "class" => "w-4",
                        "component" => array(
                            DataTableAction::class,
                            array(
                                array("link", "folder", "Open", "#"),
                                array("link", "square-pen", "Edit", "#"),
                                array("separator"),
                                array("link", "trash-2", "Delete", "#", "text-error hover:text-base-content hover:bg-error/10", "text-error group-hover/link:text-error"),
                            )
                        )
                    )
                ),
                "datasource" => TableUsers::class,
                "has-search" => true,
                "has-add" => true,
                "addFn" => function () {
                    $this->comForm->renderJSAjax();
                    $this->comDialog->renderJSOpen();
                }
            ));
        }

        public function process($options = null)
        {
            if (!$this->isPOST()) {
                return;
            }
            if (!$this->comForm->isComponentAJAX()) {
                return;
            }
            if ($this->comForm->hasError()) {
                return;
            }

            $reason = "";
            if (
                !User::addUser(
                    $this->comForm->value->name,
                    $this->comForm->value->email,
                    $this->comForm->value->password,
                    $reason
                )
            ) {
                MessageError::registerAndInit($this, "form", array(
                    "title" => "Setup",
                    "message" => $this->language->get("an-error-occurred"),
                    "reason" => $reason
                ));
                $this->view->renderJS(function () {
                    echo "setTimeout(function(){";
                    $this->comDialog->renderJSClose();
                    echo "setTimeout(function(){";
                    $this->comForm->renderAJAXRequestGet();
                    echo "},500);";
                    echo "},1500);";
                });
                return;
            }

            // ---
            MessageOk::registerAndInit($this, "form", array(
                "title" => $this->language->get("page.title"),
                "message" => $this->language->get("thank-you-for-your-registration...")
            ));

            $this->view->renderJS(function () {
                echo "setTimeout(function(){";
                $this->comDialog->renderJSClose();
                $this->comDataTable->renderAJAXRequestGet();
                echo "setTimeout(function(){";
                $this->comForm->renderAJAXRequestGet();
                echo "},500);";
                echo "},500);";
            });
        }

        public function render($options = null)
        {
            $this->renderComponent("datatable");
            $this->renderComponent("dialog-add");
        }

    }

    return Page::class;
}
