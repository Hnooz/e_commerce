<?php

/*
* GET ALL FUNCTION V2.0
* FUNCTION TO GET LATEST ITEMS FROM DATABASE [ USERS | ITEMS | COMMENTS ]

*/

function getAllFrom($field, $table, $where = NULL,$and = NULL, $orderfield, $ordering = "DESC"){
    global $con;

    $getAll = $con->prepare("SELECT $field FROM $table $where $and order by $orderfield $ordering");

    $getAll->execute();

    $all = $getAll->fetchAll();

    return $all;
}


/*  function v1.0
 ** title function that echo page title in case the page
 *
 */

    function getTitle() {
        global $pageTitle;

        if(isset($pageTitle)) {
            echo $pageTitle;

        } else {
            echo 'default';
        }
    }

    /*  redirect function [accept parameter] v2.0
        $theMsg = echo the message [ error | success | warning ]
        $url = the link you want to redirect to
        $seconds = seconds before redirecting
    */

    function redirectHome($theMsg, $url = null, $seconds =3){

        if($url === null){

            $url ='index.php';

            $link ='Homepge';
        } else {

            if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] !==''){

                $url = $_SERVER['HTTP_REFERER'];

                $link = 'Previous Page';

            } else {
                $url ='index.php';
                $link= 'Homapage';
            }
        }
        echo $theMsg;
        echo "<div class='alert alert-info'>You Will Be redirected to $link after $seconds seconds .</div>";

        header("refresh:$seconds;url=$url");

        exit();
    }


    /*
     * check items function v1.0
     * function to check items in database [function accept parameters]
     * $select = the item to select from [ Ex :user ,item, category ]
     * $from = the table to select from [ Ex : users ,item , categories ]
     * $value =the value of select  [ EX : osama, BOX, HNOOZ ]
     *
     **/

        function checkItem($select, $from, $value){

            global $con;

            $statement = $con->prepare("SELECT $select FROM $from WHERE $select = ?");

            $statement->execute(array($value));

            $count = $statement->rowCount();

            return $count;
        }


     /*
      * COUNT NUMBERS OF ITEMS FUNCTION V1.0
      * FUNCTION TO COUNT NUMBER OF ITEMS ROWS
      * $items = the item to count
      * $table = the table to choose from
      */

     function countItems($item, $table){

         global $con;

         $stmt2 = $con->prepare("SELECT COUNT($item) FROM $table");

         $stmt2->execute();

         return $stmt2->fetchColumn();

     }

    /*
     * GET LATEST RECORDS FUNCTION V1.0
     * FUNCTION TO GET LATEST ITEMS FROM DATABASE [ USERS | ITEMS | COMMENTS ]
     * $select = FIELD TO SELECT
     * $TABLE = THE TABLE TO CHOOSE FROM
     * $ORDER = THE DESC ORDER
     * $LIMIT = NUMBER OF RECORD TO GET
     */

    function getLatest($select, $table, $order,$limit = 5){
        global $con;
        $getStmt = $con->prepare("SELECT $select FROM $table ORDER BY $order DESC LIMIT $limit");

        $getStmt->execute();

        $rows = $getStmt->fetchAll();

        return $rows;
    }