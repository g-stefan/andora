<?php
// Andora
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Apache-2.0

namespace Andora\Models {

    defined("XYO_WEB") or die("Forbidden");
    require_once("./_site/vendor/autoload.php");

    require_once("./_site/xyo/web/web.php");
    require_once("./_site/andora/datasources/table-users.php");

    use \Andora\DataSources\TableUsers;
    use \PHPMailer\PHPMailer\PHPMailer;

    class User
    {
        public static function addUser($name, $email, $password, &$reason)
        {
            $name = trim($name);
            $email = trim($email);

            if (strlen($name) < 2) {
                $reason = "invalid-name";
                return false;
            }

            if (strlen($email) == 0) {
                $reason = "invalid-email";
                return false;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $reason = "invalid-email";
                return false;
            }

            if (strlen($password) < 12) {
                $reason = "invalid-password";
                return false;
            }

            $pattern = "/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-_]).{8,}$/";
            if (!preg_match($pattern, $password)) {
                $reason = "invalid-password";
                return false;
            }

            $table = new TableUsers();
            $table->clear();
            $table->email = $email;
            if ($table->load(0, 1)) {
                $reason = "already-registered";
                return false;
            }

            $guid = "";
            for ($count = 0; $count < 16; $count++) {
                $guid = self::generateGUID();
                $table->clear();
                $table->guid = $guid;
                if ($table->load(0, 1)) {
                    $guid = "";
                    continue;
                }
                break;
            }
            if (strlen($guid) == 0) {
                $reason = "guid-already-exists";
                return false;
            }

            $table->clear();
            $table->name = $name;
            $table->email = $email;
            $table->guid = $guid;
            $table->password = self::setPasswordHash($table->email, $password, "hash");
            $table->enabled = 1;
            $table->is_confirmed = 0;
            $table->is_forgot_password = 0;

            if (!$table->save()) {
                $reason = "database-error";
                return false;
            }

            return true;
        }

        public static function signUp($name, $email, $password, &$reason)
        {
            if (!self::addUser($name, $email, $password, $reason)) {
                return false;
            }

            $table = new TableUsers();
            $table->clear();
            $table->email = $email;
            if (!$table->load(0, 1)) {
                $reason = "email-not-found";
                return false;
            }

            return self::sendCheckEmail($table->guid, $reason);
        }

        public static function sendCheckEmail($guid, &$reason)
        {
            $config = \XYO\Web\Config::instance();
            if (property_exists($config, "smtp")) {
                $mail = new PHPMailer(true);
                try {
                    $table = new TableUsers();
                    $table->clear();
                    $table->guid = $guid;

                    if (!$table->load(0, 1)) {
                        $reason = "user-not-found";
                        return false;
                    }

                    $name = $table->name;

                    $mail->isSMTP();
                    $mail->Host = $config->smtp->server;
                    $mail->SMTPAuth = true;
                    $mail->Username = $config->smtp->username;
                    $mail->Password = $config->smtp->password;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = $config->smtp->port;

                    //Recipients
                    $mail->setFrom($config->smtp->username, $config->smtp->name);
                    $mail->addAddress($table->email, $name);

                    //Content
                    $mail->isHTML(true);
                    $mail->Subject = "Andora - Confirm your email address";

                    $mail->Body = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" /></head><body style=\"font-size: 10pt; font-family: Verdana,Geneva,sans-serif\">";
                    $mail->Body .= "<p>Hi " . $name . ",</p>";
                    $mail->Body .= "<p>Thanks for signing up! Your Andora account is almost complete. Click the link below to confirm your email address.</p>";
                    $mail->Body .= "<p><a href=\"" . $config->siteURL . "/email-confirm?guid=" . $guid . "\" target=\"_blank\" style=\"padding:6px;margin:3px;background-color:#ffffff;border:1px solid #000000;border-radius:6px;\" rel=\"noopener\">Confirm email address</a></p>";
                    $mail->Body .= "<p>&nbsp;</p><p>Thank you!</p>";
                    $mail->Body .= "</body></html>";

                    $mail->AltBody = "Hi " . $name . ",\r\n\r\n";
                    $mail->AltBody .= "Thanks for signing up! Your Andora account is almost complete. Click the link below to confirm your email address.\r\n";
                    $mail->AltBody .= $config->siteURL . "/email-confirm?guid=" . $guid . "\r\n";
                    $mail->AltBody .= "\r\nThank you!\r\n";

                    $mail->send();

                    $table->confirmation_sent_at = "NOW";

                    if (!$table->save()) {
                        $reason = "database-error";
                        return false;
                    }
                    return true;

                } catch (Exception $e) {
                    $reason = "smtp-mailer-error";
                    return false;
                }

            }
            $reason = "configuration-error";
            return false;
        }

        public static function emailConfirm($guid, &$reason)
        {
            $table = new TableUsers();
            $table->clear();
            $table->guid = $guid;
            $table->enabled = 1;

            if (!$table->load(0, 1)) {
                $reason = "user-not-found";
                return false;
            }

            if ($table->is_confirmed) {
                $reason = "already-confirmed";
                return false;
            }

            $table->is_confirmed = 1;
            $table->confirmed_at = "NOW";
            if (!$table->save()) {
                $reason = "database-error";
                return false;
            }

            return true;
        }

        public static function forgotPassword($email, &$reason)
        {
            $table = new TableUsers();
            $table->clear();
            $table->email = $email;
            $table->enabled = 1;

            if (!$table->load(0, 1)) {
                $reason = "user-not-found";
                return false;
            }

            $table->is_forgot_password = 1;
            $table->forgot_password_sent_at = "NOW";
            $table->save();

            return self::sendPasswordChangeEmail($table->guid, $reason);
        }

        public static function sendPasswordChangeEmail($guid, &$reason)
        {
            $config = \XYO\Web\Config::instance();
            if (property_exists($config, "smtp")) {
                $mail = new PHPMailer(true);
                try {
                    $table = new TableUsers();
                    $table->clear();
                    $table->guid = $guid;

                    if (!$table->load(0, 1)) {
                        $reason = "user-not-found";
                        return false;
                    }

                    $name = $table->name;

                    $mail->isSMTP();
                    $mail->Host = $config->smtp->server;
                    $mail->SMTPAuth = true;
                    $mail->Username = $config->smtp->username;
                    $mail->Password = $config->smtp->password;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = $config->smtp->port;

                    //Recipients
                    $mail->setFrom($config->smtp->username, $config->smtp->name);
                    $mail->addAddress($table->email, $name);

                    //Content
                    $mail->isHTML(true);
                    $mail->Subject = "Andora - Forgot Password";

                    $mail->Body = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" /></head><body style=\"font-size: 10pt; font-family: Verdana,Geneva,sans-serif\">";
                    $mail->Body .= "<p>Hi " . $name . ",</p>";
                    $mail->Body .= "<p>Forgot Password request recorded. Click the link below to set a new password.</p>";
                    $mail->Body .= "<p><a href=\"" . $config->siteURL . "/password-reset?guid=" . $guid . "\" target=\"_blank\" style=\"padding:6px;margin:3px;background-color:#ffffff;border:1px solid #000000;border-radius:6px;\" rel=\"noopener\">Change password</a></p>";
                    $mail->Body .= "<p>&nbsp;</p><p>Thank you!</p>";
                    $mail->Body .= "</body></html>";

                    $mail->AltBody = "Hi " . $name . ",\r\n\r\n";
                    $mail->AltBody .= "Forgot Password request recorded. Click the link below to set a new password.\r\n";
                    $mail->AltBody .= $config->siteURL . "/password-reset?guid=" . $guid . "\r\n";
                    $mail->AltBody .= "\r\nThank you!\r\n";

                    $mail->send();

                    $table->confirmation_sent_at = "NOW";

                    if (!$table->save()) {
                        $reason = "database-error";
                        return false;
                    }
                    return true;

                } catch (Exception $e) {
                    $reason = "smtp-mailer-error";
                    return false;
                }

            }
            $reason = "configuration-error";
            return false;
        }

        public static function passwordReset($guid, $password, $reason)
        {
            $table = new TableUsers();
            $table->clear();
            $table->guid = $guid;
            $table->enabled = 1;

            if (!$table->load(0, 1)) {
                $reason = "user-not-found";
                return false;
            }

            $table->is_forgot_password = 0;
            $table->forgot_password_confirmed_at = "NOW";
            $table->password = self::setPasswordHash($table->email, $password, "hash");

            if (!$table->save()) {
                $reason = "database-error";
                return false;
            }

            return self::sendPasswordResetEmail($guid, $reason);
        }

        public static function sendPasswordResetEmail($guid, &$reason)
        {
            $config = \XYO\Web\Config::instance();
            if (property_exists($config, "smtp")) {
                $mail = new PHPMailer(true);
                try {
                    $table = new TableUsers();
                    $table->clear();
                    $table->guid = $guid;

                    if (!$table->load(0, 1)) {
                        $reason = "user-not-found";
                        return false;
                    }

                    $name = $table->name;

                    $mail->isSMTP();
                    $mail->Host = $config->smtp->server;
                    $mail->SMTPAuth = true;
                    $mail->Username = $config->smtp->username;
                    $mail->Password = $config->smtp->password;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = $config->smtp->port;

                    //Recipients
                    $mail->setFrom($config->smtp->username, $config->smtp->name);
                    $mail->addAddress($table->email, $name);

                    //Content
                    $mail->isHTML(true);
                    $mail->Subject = "Andora - Password Changed";

                    $mail->Body = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" /></head><body style=\"font-size: 10pt; font-family: Verdana,Geneva,sans-serif\">";
                    $mail->Body .= "<p>Hi " . $name . ",</p>";
                    $mail->Body .= "<p>This is a confirmation, that your password was changed. You can login now with your new password.</p>";
                    $mail->Body .= "<p><a href=\"" . $config->siteURL . "/login\" target=\"_blank\" style=\"padding:6px;margin:3px;background-color:#ffffff;border:1px solid #000000;border-radius:6px;\" rel=\"noopener\">Login</a></p>";
                    $mail->Body .= "<p>&nbsp;</p><p>Thank you!</p>";
                    $mail->Body .= "</body></html>";

                    $mail->AltBody = "Hi " . $name . ",\r\n\r\n";
                    $mail->AltBody .= "This is a confirmation, that your password was changed. You can login now with your new password.\r\n";
                    $mail->AltBody .= $config->siteURL . "/login\r\n";
                    $mail->AltBody .= "\r\nThank you!\r\n";

                    $mail->send();

                    $table->confirmation_sent_at = "NOW";

                    if (!$table->save()) {
                        $reason = "database-error";
                        return false;
                    }
                    return true;

                } catch (Exception $e) {
                    $reason = "smtp-mailer-error";
                    return false;
                }

            }
            $reason = "configuration-error";
            return false;
        }

        public static function login($email, $password, &$reason)
        {
            $info = \XYO\Web\Info::instance();
            $config = \XYO\Web\Config::instance();

            $table = new TableUsers();
            $table->clear();
            $table->email = $email;
            $table->enabled = 1;
            if (!$table->load(0, 1)) {
                $reason = "user-not-found";
                return false;
            }

            if (!self::checkPasswordHash($table->email, $table->password, $password, "hash")) {
                $reason = "invalid-password";
                return false;
            }

            $user_rnd = hash("sha512", date("Y-m-d H:i:s") . " - " . rand(), false);
            $user_session = hash("sha512", $user_rnd . "." . $config->user->loginSalt . "." . $table->password, false);
            $user_id = $table->id;

            $table->session = $user_session;
            $table->logged_at = "NOW";
            $table->logged_in = 1;
            $table->action = 1;
            $table->action_at = "NOW";

            if (!$table->save()) {
                $reason = "database-error";
                return false;
            }

            setcookie("_user_id", $user_id, [
                "path" => $info->sitePath,
                "httponly" => true,
                "samesite" => "Strict"
            ]);
            setcookie("_user_session", $user_session, [
                "path" => $info->sitePath,
                "httponly" => true,
                "samesite" => "Strict"
            ]);

            return true;
        }

        public static function checkSession($id, $session, &$reason)
        {

            $table = new TableUsers();
            $table->clear();
            $table->id = $id;
            $table->enabled = 1;
            if (!$table->load(0, 1)) {
                $reason = "user-not-found";
                return false;
            }

            if (strcmp($table->session, $session) != 0) {
                $reason = "invalid-session";
                return false;
            }

            $table->action = 1;
            $table->action_at = "NOW";

            if (!$table->save()) {
                $reason = "database-error";
                return false;
            }

            return true;
        }

        public static function checkCurrentSession(&$reason)
        {
            if (!array_key_exists("_user_id", $_COOKIE)) {
                $reason = "invalid-cookie";
                return false;
            }
            if (!array_key_exists("_user_session", $_COOKIE)) {
                $reason = "invalid-cookie";
                return false;
            }

            $id = trim($_COOKIE["_user_id"]);
            $session = trim($_COOKIE["_user_session"]);

            if (strlen($id) == 0) {
                $reason = "invalid-user";
                return false;
            }

            $id = intval("" . $id);
            if ($id == 0) {
                $reason = "invalid-user";
                return false;
            }

            if (strlen($session) == 0) {
                $reason = "invalid-session";
                return false;
            }

            return self::checkSession($id, $session, $reason);
        }

        public static function getCurrenUserId(&$reason)
        {
            if (!array_key_exists("_user_id", $_COOKIE)) {
                $reason = "invalid-cookie";
                return 0;
            }
            $id = trim($_COOKIE["_user_id"]);
            $id = intval("" . $id);
            if ($id == 0) {
                $reason = "invalid-user";
                return 0;
            }
            return $id;
        }

        public static function logoutCurrentSession(&$reason)
        {
            if (!array_key_exists("_user_id", $_COOKIE)) {
                $reason = "invalid-cookie";
                return 0;
            }
            if (!array_key_exists("_user_session", $_COOKIE)) {
                $reason = "invalid-cookie";
                return 0;
            }

            $id = trim($_COOKIE["_user_id"]);
            $session = trim($_COOKIE["_user_session"]);

            if (strlen($id) == 0) {
                $reason = "invalid-user";
                return;
            }

            $id = intval("" . $id);
            if ($id == 0) {
                $reason = "invalid-user";
                return;
            }

            if (strlen($session) == 0) {
                $reason = "invalid-session";
                return;
            }

            $table = new TableUsers();
            $table->clear();
            $table->id = $id;
            $table->enabled = 1;
            if (!$table->load(0, 1)) {
                $reason = "user-not-found";
                return false;
            }

            if (strcmp($table->session, $session) != 0) {
                $reason = "invalid-session";
                return false;
            }

            $table->action = 1;
            $table->action_at = "NOW";
            $table->session = "";

            if (!$table->save()) {
                $reason = "database-error";
                return false;
            }

            $info = \XYO\Web\Info::instance();
            setcookie("_user_id", 0, [
                "path" => $info->sitePath,
                "httponly" => true,
                "samesite" => "Strict"
            ]);
            setcookie("_user_session", "", [
                "path" => $info->sitePath,
                "httponly" => true,
                "samesite" => "Strict"
            ]);

            return true;
        }

        public static function setPasswordHash($username, $passwordPlain, $mode)
        {
            $config = \XYO\Web\Config::instance();

            if ($mode === "hash") {
                $salt = hash("sha256", date("Y-m-d H:i:s") . " - " . rand(), false);
                return "hash:" . $salt . "." . hash("sha512", $salt . hash("sha512", $passwordPlain), false);
            }
            if ($mode === "reco") {
                $key = hash("sha512", hash("sha512", strtolower($username), true) . hash("sha512", $config->user->passwordRecoSalt, false), false);
                return "reco:" . self::recoEncode($passwordPlain, pack("H*", $key));
            }
            if ($mode === "plain") {
                return "plain:" . $passwordPlain;
            }
            return "";
        }

        public static function getPasswordHash($username, $passwordPlain, $salt, $mode)
        {
            $config = \XYO\Web\Config::instance();

            if ($mode === "hash") {
                return "hash:" . $salt . "." . hash("sha512", $salt . hash("sha512", $passwordPlain), false);
            }
            if ($mode === "reco") {
                $key = hash("sha512", hash("sha512", strtolower($username), true) . hash("sha512", $config->user->passwordRecoSalt, false), false);
                return "reco:" . self::recoEncode($passwordPlain, pack("H*", $key));
            }
            if ($mode === "plain") {
                return "plain:" . $passwordPlain;
            }
            return "";
        }

        public static function checkPasswordHash($username, $passwordHash, $passwordPlain, $mode)
        {
            if ($mode === "hash") {
                $list1 = explode(":", $passwordHash);
                $list2 = explode(".", $list1[1]);
                $salt = $list2[0];
                $passwordCheck = self::getPasswordHash($username, $passwordPlain, $salt, $mode);
                return strcmp($passwordHash, $passwordCheck) == 0;
            }
            if ($mode === "reco") {
                $passwordCheck = self::getPasswordHash($username, $passwordPlain, "", $mode);
                return strcmp($passwordHash, $passwordCheck) == 0;
            }
            if ($mode === "plain") {
                $passwordCheck = self::getPasswordHash($username, $passwordPlain, "", $mode);
                return strcmp($passwordHash, $passwordCheck) == 0;
            }
            return false;
        }

        public static function recoEncode($in, $key_)
        {
            $out = array();
            $seed = strlen($in);

            for ($k = 0; $k < $seed; ++$k) {
                $out[$k] = ord($in[$k]);
            }

            for ($k = 1; $k < $seed; ++$k) {
                $out[$k] = ($out[$k] ^ $out[$k - 1] ^ $k) & 0xFF;
            }

            $k_ln = strlen($key_);
            if ($k_ln > 0) {
                $k_k = 0;
                for ($k = 0; $k < $seed; ++$k) {
                    $out[$k] = $out[$k] ^ ord($key_[$k_k]);
                    ++$k_k;
                    if ($k_k == $k_ln) {
                        $k_k = 0;
                    }
                }
            }

            for ($k = 0; $k < $seed; ++$k) {
                $out[$k] = sprintf("%02X", $out[$k]);
            }
            return implode("", $out);
        }

        public static function recoDecode($in, $key_)
        {
            $out = array();
            $in = pack("H*", $in);
            $seed = strlen($in);

            for ($k = 0; $k < $seed; ++$k) {
                $out[$k] = ord($in[$k]);
            }

            $k_ln = strlen($key_);
            if ($k_ln > 0) {
                $k_k = 0;
                for ($k = 0; $k < $seed; ++$k) {
                    $out[$k] = $out[$k] ^ ord($key_[$k_k]);
                    ++$k_k;
                    if ($k_k == $k_ln) {
                        $k_k = 0;
                    }
                }
            }

            $in = $out;

            $out[0] = chr($in[0]);
            for ($k = 1; $k < $seed; ++$k) {
                $out[$k] = chr(($in[$k] ^ $in[$k - 1] ^ $k) & 0xFF);
            }

            return implode("", $out);
        }

        public static function generateGUID()
        {
            $data = openssl_random_pseudo_bytes(16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
            return vsprintf("%s%s-%s-%s-%s-%s%s%s", str_split(bin2hex($data), 4));
        }

        public static function generateHash512()
        {
            $salt = hash("sha256", date("Y-m-d H:i:s") . " - " . rand(), false);
            return hash("sha512", $salt . hash("sha512", self::generateGUID()), false);
        }

        public static function getSidebarMenu()
        {
            $menu = array(
                array("link", "circle-user", "Account", "#"),
                array("link", "credit-card", "Billing", "#"),
                array("link", "bell", "Notifications", "#"),
                array("separator"),
                array("link", "log-out", "Log out", "user/logout"),
            );

            $info = \XYO\Web\Info::instance();
            foreach ($menu as &$row) {
                if (count($row) > 2) {
                    if (!strncmp($row[3], "#", 1) == 0) {
                        $row[3] = $info->sitePath . $row[3];
                    }
                }
            }

            return $menu;
        }

    }

}
