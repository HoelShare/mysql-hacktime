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
        $showAll = $level !== null;
        if ($level === null) {
            $level = $this->levelService->getCurrentLevel($user)->getLevel() - 1;
        }

        $leaderboard = $this->getLeaderBoard($user, $level, $showAll);

        return $this->twig->render('leaderboard.html.twig', ['leaderBoard' => $leaderboard, 'user' => $user, 'showAll' => $showAll]);
    }

    private function getLeaderBoard(?string $user, int $level, bool $showAll): array
    {
        return $this->connection->fetchAllAssociative(
            <<<'SQL'
SELECT *
FROM (SELECT RANK() OVER (ORDER BY TIMESTAMPDIFF(SECOND, start, end)) AS `totalRank`,
             RANK() OVER (ORDER BY TIMESTAMPDIFF(SECOND, lastSuccess, created_at)) AS `rankLastLevel`,
             user,
             TIMESTAMPDIFF(SECOND, start, end) / 60                   AS totalMinutes,
             TIMESTAMPDIFF(SECOND, lastSuccess, created_at) /
             60                                                       AS lastLevelInMinutes
      FROM (SELECT user,
                   created_at,
                   FIRST_VALUE(created_at) OVER w           AS start,
                   LAST_VALUE(created_at) OVER w            AS end,
                   LAST_VALUE(success) OVER w = 1           AS lastTry,
                   LAST_VALUE(number) OVER w                AS lastLevel,
                   COALESCE(MAX(CASE WHEN success THEN created_at END)
                                OVER (PARTITION BY user ORDER BY created_at, id ROWS BETWEEN UNBOUNDED PRECEDING AND 1 PRECEDING),
                            FIRST_VALUE(created_at) OVER w) AS lastSuccess
            FROM solution_try
            WHERE user <> :testUser
                WINDOW w AS (PARTITION BY user ORDER BY id)) a
      WHERE lastLevel = :userLevel
        AND lastTry = 1) userRanked
WHERE (`rankLastLevel` <= 10 OR user = :user OR :showAll = 1)
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
