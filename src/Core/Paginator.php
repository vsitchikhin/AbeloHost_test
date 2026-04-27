<?php

declare(strict_types=1);

namespace App\Core;

class Paginator
{
    /**
     * @return array{currentPage: int, perPage: int, total: int, totalPages: int, offset: int}
     */
    public static function make(int $total, int $page, int $perPage): array
    {
        $currentPage = max(1, $page);
        $safePerPage = max(1, $perPage);
        $totalPages  = max(1, (int) ceil($total / $safePerPage));

        if ($currentPage > $totalPages) {
            $currentPage = $totalPages;
        }

        return [
            'currentPage' => $currentPage,
            'perPage'     => $safePerPage,
            'total'       => max(0, $total),
            'totalPages'  => $totalPages,
            'offset'      => ($currentPage - 1) * $safePerPage,
        ];
    }
}
