<?php

declare(strict_types=1);

class DataBase
{
    private const HOST_NAME = 'localhost';

    private const USER_NAME = 'root';

    private const PASSWORD = 'root';

    private const DATABASE = 'inter';

    private false|mysqli $mysql;

    public function __construct()
    {
        $this->mysql = mysqli_connect(self::HOST_NAME, self::USER_NAME, self::PASSWORD, self::DATABASE);
    }

    public function executeQuery($query): array
    {
        $result = mysqli_query($this->mysql, $query);

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}