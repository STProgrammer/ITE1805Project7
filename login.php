<?php
//logg ut
if ($request->request->has('logout')) {
    $session->clear();
    header("Location:. ");
    exit();
}

// if logged in
if ($session->has('loggedin')) {
    $user = $session->get('User'); // get the user data
}
// if login submitted
elseif ($request->request->has('login')) {
    if(User::login($db, $request, $session)) {
        $user = $session->get('User');
        if ($session->get('loggedin') && $user->verifyUser($request)) {
            header("Location: .");
            exit();
        }
    } //if login submitted but failed to login
    else {
        $get_info = "?loginfail=1";
        header("Location: ".$homepath."login/".$get_info);
        exit();
    }
}
else $user = null;

?>