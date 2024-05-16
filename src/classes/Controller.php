<?php

declare(strict_types=1);

include_once PATH_PROJECT . '/classes/DataBase.php';

class Controller
{
    /**
     * @var DataBase
     */
    private DataBase $db;

    /**
     * @param DataBase $db
     */
    public function __construct()
    {
        $this->db = new DataBase();
    }

    public function getUsersWithTransaction()
    {
        $query = 'SELECT users.id, users.name FROM user_accounts
                    INNER JOIN transactions
                    ON user_accounts.id = transactions.account_from OR user_accounts.id = transactions.account_to
                    INNER JOIN users   
                    ON user_accounts.user_id = users.id
                    GROUP BY  users.id;';

        return $this->db->executeQuery($query);
    }

    public function getUserBalanceByMonth(string $userId): array
    {
        // Получение суммы пополнений за каждый месяц по всем счетам пользователя
        $query = "SELECT SUM(amount) as amount, DATE_FORMAT(transactions.trdate, '%Y-%m') as trdate 
                    FROM user_accounts
                    INNER JOIN transactions
                    ON user_accounts.id = transactions.account_to
                    WHERE  user_accounts.user_id = $userId
                    GROUP BY DATE_FORMAT(transactions.trdate, '%Y-%m');";

        $replenishment = $this->db->executeQuery($query);

        // Получение суммы растрат за каждый месяц по всем счетам пользователя
        $query = "SELECT SUM(amount) as amount, DATE_FORMAT(transactions.trdate, '%Y-%m') as trdate 
                    FROM user_accounts
                    INNER JOIN transactions
                    ON user_accounts.id = transactions.account_from
                    WHERE  user_accounts.user_id =  $userId
                    GROUP BY DATE_FORMAT(transactions.trdate, '%Y-%m');";

        $loss = $this->db->executeQuery($query);

        // Создание ассоциативных массивов, где ключ => месяц транзакции, значение => сумма денег
        $replenishment = $this->prepareData($replenishment);
        $loss = $this->prepareData($loss);

        // Вычисление баланса по месяцам
        foreach ($loss as $date => $amount) {
            if (isset($replenishment[$date])) {
                $replenishment[$date] -= $amount;
            } else {
                $replenishment[$date] = -$amount;
            }
        }

        return $replenishment;
    }

    private function prepareData(array $data): array
    {
        $result = [];

        foreach ($data as $item) {
            $result[$item['trdate']] = (float)$item['amount'];
        }

        return $result;
    }
}