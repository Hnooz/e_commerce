<?php

    /*
        categories => [ manage | edit | update | add | insert | delete | stats ]
     */

    $do =isset($_GET['do']) ?$_GET['do'] : 'Manage';

    if ($do == 'Manage'){
        echo 'welcome you are in manage category page';
        echo '<a href="?do=Add">Add new caegory +</a>';
    } elseif ($do == 'Add'){
        echo 'welcome you are in add category page';
    } elseif ($do == 'Insert'){
        echo 'welcome you are in insert category page';
    } else{
        echo 'error there\'s no page with this name';
    }