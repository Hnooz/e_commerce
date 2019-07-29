<?php

function lang( $pharse) {

    static $lang = array(

     //Navbar link
        'HOME_ADMIN'  => 'Home',
        'CATEGORIES'  => 'Categories',
        'ITEMS'       => 'Items',
        'MEMBERS'     => 'Members',
        'STATISTICS'  => 'Statistics',
        'COMMENTS'    =>'Comments',
        'LOGS'        => 'Logs',
        ''=>'',
        ''=>'',
        ''=>'',
        ''=>'',
    );
    return $lang[$pharse];
}