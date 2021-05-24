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

    public function render(?string $user, ?int $level = null): string
    {
        $showAll = false;
        if ($level === null) {
            $level = $this->levelService->getCurrentLevel($user)->getLevel() - 1;
            $showAll = true;
        }

        $leaderboard = $this->getLeaderBoard($user, $level, $showAll);

        return $this->twig->render('leaderboard.html.twig', ['leaderBoard' => $leaderboard, 'user' => $user]);
    }

    private function getLeaderBoard(?string $user, int $level, bool $showAll): array
    {
        return $this->connection->fetchAllAssociative(
            <<<'SQL'
SELECT * FROM (
select  rank() over (order by  timestampdiff(second, start, end)) as `rank`, user, timestampdiff(second, start, end) / 60 as `minutes` from (
SELECT 
    user,
        FIRST_VALUE(created_at) over w as `start`, 
        LAST_VALUE(created_at) over w as `end`,
        last_value(success) over w = 1 as `lastTry`,
        last_value(`number`) over w as `lastLevel`
FROM
    solution_try
    where user <> :testUser
WINDOW w as (partition by user order by id)) a
where lastLevel = :userLevel and lastTry = 1) userRanked
where (`rank` <= 10 or `user` = :user or :showAll = 1) 
SQL
            ,
            [
                'testUser' => 'test',
                'userLevel' => $level,
                'user' => $user,
                'showAll' => (int) $showAll,
            ]
        );
    }
}
