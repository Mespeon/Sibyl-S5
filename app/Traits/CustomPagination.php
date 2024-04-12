<?php
namespace App\Traits;

trait CustomPagination {
    /**
     * Custom Pagination
     * 
     * @param object paginationData - array containing default pagination data.
     */
    public function customPagination(object $paginationData) {
        return [
            'data' => $paginationData->items(),
            'current_page' => $paginationData->currentPage(),
            'last_page' => $paginationData->lastPage(),
            'per_page' => $paginationData->perPage(),
            'total' => $paginationData->total(),
            'links' => [
                'next_page' => $paginationData->nextPageUrl(),
                'previous_page' => $paginationData->previousPageUrl(),
                'first_page' => $paginationData->url(1),
                'last_page' => $paginationData->url($paginationData->lastPage()),
                'self' => $paginationData->url($paginationData->currentPage())
            ]
        ];
    }
}