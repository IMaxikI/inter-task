<?php

const PATH_PROJECT = __DIR__;

include_once 'classes/Controller.php';

$userCollection = new Controller();

echo json_encode([$userCollection->getUserBalanceByMonth($_GET['userId'])]);

