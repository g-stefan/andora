<?php
// Andora
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Apache-2.0


namespace Andora\Model {

    defined("XYO_WEB") or die("Forbidden");
    require_once("./_site/vendor/autoload.php");

    require_once("./_site/xyo/web/web.php");
    require_once("./_site/andora/model/user.php");

    require_once("./_site/andora/datasource/table-component.php");
    require_once("./_site/andora/datasource/table-setting.php");
    require_once("./_site/andora/datasource/table-user-group-x-user-group.php");
    require_once("./_site/andora/datasource/table-user-group.php");
    require_once("./_site/andora/datasource/table-user-setting.php");
    require_once("./_site/andora/datasource/table-user-x-user-group.php");
    require_once("./_site/andora/datasource/table-user-group-setting.php");
    require_once("./_site/andora/datasource/table-user.php");

    use \Andora\DataSource\TableComponent;
    use \Andora\DataSource\TableSetting;
    use \Andora\DataSource\TableUserGroupXUserGroup;
    use \Andora\DataSource\TableUserGroup;
    use \Andora\DataSource\TableUserSetting;
    use \Andora\DataSource\TableUserXUserGroup;
    use \Andora\DataSource\TableUser;
    use \Andora\DataSource\TableUserGroupSetting;
    use \Andora\Model\User;

    use \PHPMailer\PHPMailer\PHPMailer;

    class Setup
    {
        public static function dsCreateStorage(&$reason)
        {
            $table = new TableComponent();
            if (!$table->createStorage()) {
                $reason = "database-error";
                return false;
            }

            $table = new TableSetting();
            if (!$table->createStorage()) {
                $reason = "database-error";
                return false;
            }

            $table = new TableUser();
            if (!$table->createStorage()) {
                $reason = "database-error";
                return false;
            }

            $table = new TableUserSetting();
            if (!$table->createStorage()) {
                $reason = "database-error";
                return false;
            }

            $table = new TableUserGroup();
            if (!$table->createStorage()) {
                $reason = "database-error";
                return false;
            }

            $table = new TableUserGroupSetting();
            if (!$table->createStorage()) {
                $reason = "database-error";
                return false;
            }

            $table = new TableUserXUserGroup();
            if (!$table->createStorage()) {
                $reason = "database-error";
                return false;
            }

            $table = new TableUserGroupXUserGroup();
            if (!$table->createStorage()) {
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
            $content .= "return array(\"language\" => \"" . $language . "\");\r\n";
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
            $content .= "return array(\r\n";
            $content .= "\t\"dataSource\" => array(\r\n";
            $content .= "\t\t\"connection\" => array(\r\n";
            $content .= "\t\t\t\"db\" => array(\r\n";
            $content .= "\t\t\t\t\"type\" => \"" . $databaseType . "\",\r\n";
            if ($databaseType == "sqlite") {
                $content .= "\t\t\t\t\"database\" => \"./_repository/andora.sqlite\",\r\n";
            }
            if ($databaseType == "mysql" || $databaseType == "postgresql") {
                $content .= "\t\t\t\t\"user\" => \"" . $username . "\",\r\n";
                $content .= "\t\t\t\t\"password\" => \"" . $password . "\",\r\n";
                $content .= "\t\t\t\t\"server\" => \"" . $databaseServer . "\",\r\n";
                $content .= "\t\t\t\t\"port\" => \"" . $databasePort . "\",\r\n";
                $content .= "\t\t\t\t\"database\" => \"" . $databaseName . "\",\r\n";
            }

            $content .= "\t\t\t\t\"prefix\" => \"" . $tablePrefix . "\"\r\n";
            $content .= "\t\t\t)\r\n";
            $content .= "\t\t)\r\n";
            $content .= "\t)\r\n";
            $content .= ");\r\n";
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
            \XYO\Web\DataSource\Connection::processConfig();
            $connections = \XYO\Web\DataSource\Connection::instance();
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
            $content .= "return array(\r\n";
            $content .= "\t\"user\" => array(\r\n";
            $content .= "\t\t\"passwordRecoSalt\" => \"" . User::generateHash512() . "\",\r\n";
            $content .= "\t\t\"loginSalt\" => \"" . User::generateHash512() . "\"\r\n";
            $content .= "\t)\r\n";
            $content .= ");\r\n";            
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
            $content .= "return array(\"configured\" => true);\r\n";
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
            $content .= "return array(\r\n";
            $content .= "\t\"smtp\" => array(\r\n";
            $content .= "\t\t\"name\" => \"" . $name . "\",\r\n";
            $content .= "\t\t\"username\" => \"" . $username . "\",\r\n";
            $content .= "\t\t\"password\" => \"" . $password . "\",\r\n";
            $content .= "\t\t\"server\" => \"" . $server . "\",\r\n";
            $content .= "\t\t\"port\" => \"" . $port . "\",\r\n";
            $content .= "\t\t\"isTested\" => " . ($isTested ? "true" : "false") . ",\r\n";
            $content .= "\t)\r\n";
            $content .= ");\r\n";
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
                    $configSMTP = $config->get("smtp");

                    $mail->isSMTP();
                    $mail->Host = $configSMTP->get("server");
                    $mail->SMTPAuth = true;
                    $mail->Username = $configSMTP->get("username");
                    $mail->Password = $configSMTP->get("password");
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = $configSMTP->get("port");

                    //Recipients
                    $mail->setFrom($configSMTP->get("username"), $configSMTP->get("name"));
                    $mail->addAddress($configSMTP->get("username"), $configSMTP->get("name"));

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
