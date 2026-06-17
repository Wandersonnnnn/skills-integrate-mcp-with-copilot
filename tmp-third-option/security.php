<?php

function ensure_session_started()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function is_logged_in()
{
    return isset($_SESSION['stduid2']) && isset($_SESSION['stdutype2']) && isset($_SESSION['auth_ok']);
}

function require_login($redirect = 'default.php')
{
    ensure_session_started();
    if (!is_logged_in()) {
        header('Location: ' . $redirect);
        exit;
    }
}

function require_role($roles, $redirect = 'default.php')
{
    ensure_session_started();

    if (!is_logged_in()) {
        header('Location: ' . $redirect);
        exit;
    }

    if (!in_array($_SESSION['stdutype2'], $roles, true)) {
        header('Location: ' . $redirect);
        exit;
    }
}

function require_master_verification($redirect = 'deladmin.php')
{
    ensure_session_started();
    $maxAgeSeconds = 300;
    $verifiedAt = isset($_SESSION['master_verified']) ? (int)$_SESSION['master_verified'] : 0;
    if ($verifiedAt <= 0 || (time() - $verifiedAt) > $maxAgeSeconds) {
        header('Location: ' . $redirect);
        exit;
    }
}

function set_login_session($userId, $userType)
{
    ensure_session_started();
    session_regenerate_id(true);

    $_SESSION['stduid2'] = $userId;
    $_SESSION['stdutype2'] = $userType;
    $_SESSION['auth_ok'] = true;

    // Backward-compatible marker for old checks without storing plain passwords.
    $_SESSION['stdpwd2'] = 'AUTHENTICATED';
}
