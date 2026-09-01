<?php

namespace App\Http\Controllers\Api\Concerns;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

// The paginated list shape every community endpoint returns. The web renders
// paginator links in Blade; a mobile client instead needs to know whether to
// keep loading, so it gets the page numbers rather than markup.
trait PaginatesApiResponses
{
    /**
     * @param  callable  $map  turns one model into its payload array
     */
    protected function paginated(LengthAwarePaginator $paginator, string $key, callable $map): array
    {
        return [
            $key => collect($paginator->items())->map($map)->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'has_more' => $paginator->hasMorePages(),
            ],
        ];
    }
}
