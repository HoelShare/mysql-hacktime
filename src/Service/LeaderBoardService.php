<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\DBAL\Connection;
use Twig\Environment;

class LeaderBoardService
{
    public function __construct(
        private Connection $connection,
        private LevelService $levelService,
        private Environment $twig,
    ) {
    }

    public function render(string $user): string
    {
        $level = $this->levelService->getCurrentLevel($user);
        $leaderboard = $this->getLeaderBoard($user, $level);

        return $this->twig->render('leaderboard.html.twig', ['leaderBoard' => $leaderboard, 'user' => $user]);
    }

    private function getLeaderBoard(string $user, int $level): array
    {
        return $this->connection->fetchAllAssociative(
            <<<'SQL'
SELECT IF(`level` <= :userLevel, `level`, null) as `level`, `user`, `rank` 
FROM (SELECT 
    MAX(number) AS `level`, user, row_number() over (order by max(number) desc, min(created_at) asc) as `rank`
FROM
    solution_try
WHERE
    (success = 1 AND number > 0)
        OR user = :user
GROUP BY user) board
where `user` = :user or `rank` <= 10
SQL
            ,
            ['userLevel' => $level, 'user' => $user]
        );
    }
}
