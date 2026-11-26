
<?php

if (session_status() === PHP_SESSION_NONE) {

    session_set_cookie_params([
        "httponly" => true,
        "samesite" => "Strict",
        "secure" => false
    ]);

    session_start();

}

?>
