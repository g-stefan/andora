<?php
// Andora
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Apache-2.0


namespace Andora\Models {

    defined("XYO_WEB") or die("Forbidden");
    require_once("./_site/vendor/autoload.php");

    require_once("./_site/xyo/web/web.php");
    require_once("./_site/andora/models/user.php");

    require_once("./_site/andora/datasources/table-components.php");
    require_once("./_site/andora/datasources/table-settings.php");
    require_once("./_site/andora/datasources/table-user-group-x-user-groups.php");
    require_once("./_site/andora/datasources/table-user-groups.php");
    require_once("./_site/andora/datasources/table-user-settings.php");
    require_once("./_site/andora/datasources/table-user-x-user-groups.php");
    require_once("./_site/andora/datasources/table-user-group-settings.php");
    require_once("./_site/andora/datasources/table-users.php");

    use \Andora\DataSources\TableComponents;
    use \Andora\DataSources\TableSettings;
    use \Andora\DataSources\TableUserGroupXUserGroups;
    use \Andora\DataSources\TableUserGroups;
    use \Andora\DataSources\TableUserSettings;
    use \Andora\DataSources\TableUserXUserGroups;
    use \Andora\DataSources\TableUsers;
    use \Andora\DataSources\TableUserGroupSettings;
    use \Andora\Models\User;

    use \PHPMailer\PHPMailer\PHPMailer;

    class Setup
    {
        public static function dsCreateStorage(&$reason)
        {
            $table = new TableComponents();
            if (!$table->_connector->createStorage()) {
                $reason = "database-error";
                return false;
            }

            $table = new TableSettings();
            if (!$table->_connector->createStorage()) {
                $reason = "database-error";
                return false;
            }

            $table = new TableUsers();
            if (!$table->_connector->createStorage()) {
                $reason = "database-error";
                return false;
            }

            $table = new TableUserSettings();
            if (!$table->_connector->createStorage()) {
                $reason = "database-error";
                return false;
            }

            $table = new TableUserGroups();
            if (!$table->_connector->createStorage()) {
                $reason = "database-error";
                return false;
            }

            $table = new TableUserGroupSettings();
            if (!$table->_connector->createStorage()) {
                $reason = "database-error";
                return false;
            }

            $table = new TableUserXUserGroups();
            if (!$table->_connector->createStorage()) {
                $reason = "database-error";
                return false;
            }

            $table = new TableUserGroupXUserGroups();
            if (!$table->_connector->createStorage()) {
                $reason = "database-error";
                return false;
            }

            return true;
        }

        public static function writeLanguageConfigFile($language, &$reason)
        {

            $content = "<" . "?php\r\n";
            $content .= "// Andora\r\n";
            $content .= "// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>\r\n";
            $content .= "// SPDX-License-Identifier: Apache-2.0\r\n";
            $content .= "\r\n";
            $content .= "defined(\"XYO_WEB\") or die(\"Forbidden\");\r\n";
            $content .= "\r\n";
            $content .= "\$config = new \stdClass();\r\n";
            $content .= "\$config->language = \"" . $language . "\";\r\n";
            $content .= "\r\n";
            $content .= "return \$config;\r\n";
            $content .= "\r\n";

            if (!file_put_contents("config.01.language.php", $content)) {
                $reason = "unable-to-write-configuration";
                return false;
            }

            return true;
        }

        public static function writeDatabaseConfigFile(
            $databaseType,
            $username,
            $password,
            $databaseServer,
            $databasePort,
            $databaseName,
            $tablePrefix,
            &$reason
        ) {
            $content = "<" . "?php\r\n";
            $content .= "// Andora\r\n";
            $content .= "// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>\r\n";
            $content .= "// SPDX-License-Identifier: Apache-2.0\r\n";
            $content .= "\r\n";
            $content .= "defined(\"XYO_WEB\") or die(\"Forbidden\");\r\n";
            $content .= "\r\n";
            $content .= "\$config = new \stdClass();\r\n";
            $content .= "\$config->dataSource = new \stdClass();\r\n";
            $content .= "\$config->dataSource->connections = array(\r\n";
            $content .= "\t\"db\" => array(\r\n";
            $content .= "\t\t\"type\" => \"" . $databaseType . "\",\r\n";
            if ($databaseType == "sqlite") {
                $content .= "\t\t\"database\" => \"./_repository/andora.sqlite\",\r\n";
            }
            if ($databaseType == "mysql" || $databaseType == "postgresql") {
                $content .= "\t\t\"user\" => \"" . $username . "\",\r\n";
                $content .= "\t\t\"password\" => \"" . $password . "\",\r\n";
                $content .= "\t\t\"server\" => \"" . $databaseServer . "\",\r\n";
                $content .= "\t\t\"port\" => \"" . $databasePort . "\",\r\n";
                $content .= "\t\t\"database\" => \"" . $databaseName . "\",\r\n";
            }

            $content .= "\t\t\"prefix\" => \"" . $tablePrefix . "\"\r\n";
            $content .= "\t)\r\n";
            $content .= ");\r\n";
            $content .= "\r\n";
            $content .= "return \$config;\r\n";
            $content .= "\r\n";

            if (!file_put_contents("config.02.datasource.php", $content)) {
                $reason = "unable-to-write-configuration";
                return false;
            }

            return true;
        }

        public static function checkDatabaseConnection(&$reason)
        {
            $config = \XYO\Web\Config::instance();
            $config->includeFile("config.02.datasource.php");
            \XYO\Web\DataSource\Connections::processConfig();
            $connections = \XYO\Web\DataSource\Connections::instance();
            $db = $connections->get();
            if (!$db->isOpen()) {
                $reason = "database-connection-error";
                return false;
            }
            return true;
        }

        public static function writeUserConfigFile(&$reason)
        {

            $content = "<" . "?php\r\n";
            $content .= "// Andora\r\n";
            $content .= "// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>\r\n";
            $content .= "// SPDX-License-Identifier: Apache-2.0\r\n";
            $content .= "\r\n";
            $content .= "defined(\"XYO_WEB\") or die(\"Forbidden\");\r\n";
            $content .= "\r\n";
            $content .= "\$config = new \stdClass();\r\n";
            $content .= "\$config->user = new \stdClass();\r\n";
            $content .= "\$config->user->passwordRecoSalt = \"" . User::generateHash512() . "\";\r\n";
            $content .= "\$config->user->loginSalt = \"" . User::generateHash512() . "\";\r\n";
            $content .= "\r\n";
            $content .= "return \$config;\r\n";
            $content .= "\r\n";

            if (!file_put_contents("config.03.user.php", $content)) {
                $reason = "unable-to-write-configuration";
                return false;
            }

            return true;
        }

        public static function writeConfiguredConfigFile(&$reason)
        {

            $content = "<" . "?php\r\n";
            $content .= "// Andora\r\n";
            $content .= "// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>\r\n";
            $content .= "// SPDX-License-Identifier: Apache-2.0\r\n";
            $content .= "\r\n";
            $content .= "defined(\"XYO_WEB\") or die(\"Forbidden\");\r\n";
            $content .= "\r\n";
            $content .= "\$config = new \stdClass();\r\n";
            $content .= "\$config->configured = true;\r\n";
            $content .= "\r\n";
            $content .= "return \$config;\r\n";
            $content .= "\r\n";

            if (!file_put_contents("config.04.configured.php", $content)) {
                $reason = "unable-to-write-configuration";
                return false;
            }

            return true;
        }

        public static function writeSMTPConfigFile(
            $name,
            $username,
            $password,
            $server,
            $port,
            $isTested,
            &$reason
        ) {

            $content = "<" . "?php\r\n";
            $content .= "// Andora\r\n";
            $content .= "// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>\r\n";
            $content .= "// SPDX-License-Identifier: Apache-2.0\r\n";
            $content .= "\r\n";
            $content .= "defined(\"XYO_WEB\") or die(\"Forbidden\");\r\n";
            $content .= "\r\n";
            $content .= "\$config = new \stdClass();\r\n";
            $content .= "\$config->smtp = new \stdClass();\r\n";
            $content .= "\$config->smtp->name = \"" . $name . "\";\r\n";
            $content .= "\$config->smtp->username = \"" . $username . "\";\r\n";
            $content .= "\$config->smtp->password = \"" . $password . "\";\r\n";
            $content .= "\$config->smtp->server = \"" . $server . "\";\r\n";
            $content .= "\$config->smtp->port = \"" . $port . "\";\r\n";
            $content .= "\$config->smtp->isTested = " . ($isTested ? "true" : "false") . ";\r\n";
            $content .= "\r\n";
            $content .= "return \$config;\r\n";
            $content .= "\r\n";

            if (!file_put_contents("config.05.smtp.php", $content)) {
                $reason = "unable-to-write-configuration";
                return false;
            }

            return true;
        }

        public static function testSMTPConfiguration(&$reason)
        {
            $config = \XYO\Web\Config::instance();
            if (property_exists($config, "smtp")) {
                $mail = new PHPMailer(true);
                try {

                    $mail->isSMTP();
                    $mail->Host = $config->smtp->server;
                    $mail->SMTPAuth = true;
                    $mail->Username = $config->smtp->username;
                    $mail->Password = $config->smtp->password;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = $config->smtp->port;

                    //Recipients
                    $mail->setFrom($config->smtp->username, $config->smtp->name);
                    $mail->addAddress($config->smtp->username, $config->smtp->name);

                    //Content
                    $mail->isHTML(true);
                    $mail->Subject = "Andora - SMTP configuration test";

                    $mail->Body = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" /></head><body style=\"font-size: 10pt; font-family: Verdana,Geneva,sans-serif\">";
                    $mail->Body .= "<p>Hi,</p><br />";
                    $mail->Body .= "<p>This is a test e-mail!</p>";
                    $mail->Body .= "<br /><p>Thank you!</p>";
                    $mail->Body .= "</body></html>";

                    $mail->AltBody = "Hi,\r\n\r\n";
                    $mail->AltBody .= "This is a test e-mail!\r\n";
                    $mail->AltBody .= "\r\nThank you!\r\n";

                    $mail->send();

                    return true;

                } catch (\Exception $e) {
                    $reason = "smtp-mailer-error";
                    return false;
                }

            }
            $reason = "configuration-error";
            return false;
        }

    }

}
