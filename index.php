<?php

// set error reporting level
if (version_compare(phpversion(), '5.3.0', '>=') == 1)
  error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
else
  error_reporting(E_ALL & ~E_NOTICE);

session_start();
if (isset($_POST['submit'])) {

    $sLogin = escape($_POST['login']);
    $sPass = escape($_POST['pass']);
    $sCPass = escape($_POST['cpass']);
    $sEmail = escape($_POST['email']);
    $iGender = (int)$_POST['gender'];

    $sErrors = '';
    if (strlen($sLogin) >= 1 and strlen($sLogin) <= 25) {
        if (strlen($sPass) >= 1 and strlen($sPass) <= 25) {
            if (strlen($sEmail) >= 1 and strlen($sEmail) <= 55) {
                if ($sPass == $sCPass) {
                    if (preg_match('^[a-zA-Z0-9\-\.]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$', $sEmail)) {
                        if ($iGender == '1' xor $iGender == '0') {
                            $sVcode = escape($_POST['vcode']);
                            if (! isset($_SESSION['valdiation_code'])) {
                                // Here you can send him some verification code (by email or any another ways)
                                $sCode = uniqid(rand(), true);
                                $_SESSION['valdiation_code'] = md5($sCode);

                            } elseif (md5($sVcode) == $_SESSION['valdiation_code']) {
                                // Here you can add him to database
                                // mysql_query('INSERT INTO ....

                                // display step 3 (final step)
                                echo strtr(file_get_contents('templates/step3.html'), array());
                                exit;
                            } else {
                                $sErrors = 'Verification code is wrong';
                            }
                        } else {
                            $sErrors = 'Gender is wrong';
                        }
                    } else {
                        $sErrors = 'Email is wrong';
                    }
                } else {
                    $sErrors = 'Passwords are not the same';
                }
            } else {
                $sErrors = 'Address email is too long';
            }
        } else {
            $sErrors = 'Password is too long';
        }
    } else {
        $sErrors = 'Login is too long';
    }

    // display step 2
    $aParams = array(
        '{errors}' => $sErrors,
        '{login}' => $sLogin,
        '{pass}' => $sPass,
        '{cpass}' => $sCPass,
        '{gender}' => $iGender,
        '{email}' => $sEmail,
        '{vcode}' => $sCode
    );
    echo strtr(file_get_contents('templates/step2.html'), $aParams);
    exit;
}

// unset validation code if exists
unset($_SESSION['valdiation_code']);

// draw registration page
echo strtr(file_get_contents('templates/main_page.html'), array());

// extra useful function to make POST variables more safe
function escape($s) {
    //return mysql_real_escape_string(strip_tags($s)); // uncomment in when you will use connection with database
    return strip_tags($s);
}