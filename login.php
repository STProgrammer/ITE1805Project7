<?php
//logg out
if ($request->request->has('logout') && XsrfProtection::verifyMac("Logout")) {
    $session->clear();
    $referer = $request->server->get('HTTP_REFERER');
    header('location: '.$referer);
    exit();
}

// if logged in
if ($session->has('loggedin')) {
    $user = $session->get('User'); // get the user data
    
}
// if login submitted
elseif ($request->request->has('login')) {
    if(XsrfProtection::verifyMac("Login") && User::login($db, $request, $session)) {
        $user = $session->get('User');
        if ($session->get('loggedin') && $user->verifyUser($request)) {
            $referer = $request->server->get('HTTP_REFERER');
            header('location: '.$referer);
            exit();
        }
    } //if login submitted but failed to login
    else {
        $get_info = "?loginfail=1";
        header("Location: ".$rel."login/".$get_info);
        exit();
    }
}
else $user = null;

?>