<?php
function authentication_required() {
    $accounts = array(
        'mattino' => '123'
    );

    if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
        $username = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];
        $hash = md5($username . $password);
        $key = md5($username);
        if (isset($_COOKIE[$key]) && $_COOKIE[$key] == $hash) {
            return false;
        }
        if (isset($accounts[$username]) && $accounts[$username] == $password) {
            setcookie($key, $hash);
            return false;
        }
    }
    header('WWW-Authenticate: Basic realm="Mattino dev environment"');
    header('HTTP/1.0 401 Unauthorized');
    return true;
}
if (
authentication_required()
) {
    die('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

require_once 'app/Mage.php';
umask(0);
$app = Mage::app('default');

Mage::getSingleton('core/session', array('name' => 'adminhtml'));

// supply username

$username =$_GET['username'];


    $user = Mage::getModel('admin/user')->loadByUsername($username); // user your admin username


    if (Mage::getSingleton('adminhtml/url')->useSecretKey()) {
        Mage::getSingleton('adminhtml/url')->renewSecretUrls();
    }
    $session = Mage::getSingleton('admin/session');
    $session->setIsFirstVisit(true);
    $session->setUser($user);
    $session->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());
    Mage::dispatchEvent('admin_session_user_login_success', array('user' => $user));

    if ($session->isLoggedIn()) {
        echo "Logged in";
    } else {
        echo 'Not Logged';
    }

header('Location: ' . Mage::helper('adminhtml')->getUrl("adminhtml"));

