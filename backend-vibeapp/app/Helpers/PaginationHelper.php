<?php

namespace App\Helpers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
class PaginationHelper
{
    /**
     * Format pagination data.
     *
     * @param LengthAwarePaginator $paginator
     * @return array
     */
    public static function formatPagination(LengthAwarePaginator $paginator): array
    {
        return [
            'data' => $paginator->items(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
            'next_page' => $paginator->currentPage() == $paginator->lastPage() ? "" : $paginator->currentPage() + 1,
        ];
    }

    /**
     * Apply search and paginate results.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $searchTerm
     * @param array $searchColumns
     * @param int $perPage
     * @return array
     */
    public static function searchAndPaginate(
        $query,
        $searchTerm = null,
        $searchColumns = [],
        $perPage = 10,
        $paginate = false,
        $sortColumn = 'id',
        $sortOrder = 'DESC',
        $searchRelation = []
    ): array {
        // Jika ada search term, filter data berdasarkan kolom
        if (!empty($searchRelation) && $searchTerm && !empty($searchColumns)) {
            foreach ($searchRelation as $columnRelation) {
                $query->whereHas($columnRelation, function ($q) use ($searchTerm, $searchColumns) {
                    foreach ($searchColumns as $column) {
                        $q->Where($column, 'like', '%' . $searchTerm . '%');
                    }
                });
            }
            // $query->whereHas($searchRelation, function ($q) use ($searchTerm, $searchColumns) {
            //     foreach ($searchColumns as $column) {
            //         $q->Where($column, 'like', '%' . $searchTerm . '%');
            //     }
            // });
        } elseif (empty($searchRelation) && $searchTerm && !empty($searchColumns)) {
            $query->where(function ($q) use ($searchTerm, $searchColumns) {
                foreach ($searchColumns as $column) {
                    $q->orWhere($column, 'like', '%' . $searchTerm . '%');
                }
            });
        }
        
        $query->orderBy($sortColumn, strtoupper($sortOrder));

        if ($paginate) {
            return self::formatPagination($query->paginate($perPage));
        }

        return ["data" => $query->get()];
    }

    public static function searchAndPaginateRandom(
        $query,
        $searchTerm = null,
        $searchColumns = [],
        $perPage = 10,
        $paginate = false,
        $sortColumn = 'id',
        $sortOrder = 'DESC',
        $searchRelation = []
    ): array {
        // Filter the data based on search term and search columns
        if (!empty($searchRelation) && $searchTerm && !empty($searchColumns)) {
            foreach ($searchRelation as $columnRelation) {
                $query->whereHas($columnRelation, function ($q) use ($searchTerm, $searchColumns) {
                    foreach ($searchColumns as $column) {
                        $q->where($column, 'like', '%' . $searchTerm . '%');
                    }
                });
            }
        } elseif (empty($searchRelation) && $searchTerm && !empty($searchColumns)) {
            $query->where(function ($q) use ($searchTerm, $searchColumns) {
                foreach ($searchColumns as $column) {
                    $q->orWhere($column, 'like', '%' . $searchTerm . '%');
                }
            });
        }
    
        // Order by the specified column and order
        // $query->orderBy($sortColumn, strtoupper($sortOrder));
    
        // Fetch all the data first, without pagination
        $data = $query->get();
    
        // Randomize the data using shuffle()
        $shuffledData = $data->shuffle();
        // $shuffledData = $shuffledData->sortByDesc('rating');

        // If pagination is required
        if ($paginate) {
            // Get the current page from the request, defaulting to page 1
            $currentPage = Paginator::resolveCurrentPage();
    
            // Get the offset and slice the data for the current page
            $offset = ($currentPage - 1) * $perPage;
            $paginatedData = $shuffledData->slice($offset, $perPage);
    
            // Create the paginator instance
            $paginator = new LengthAwarePaginator(
                $paginatedData, // The items for the current page
                $shuffledData->count(), // Total items in the collection
                $perPage, // Items per page
                $currentPage, // Current page
                ['path' => Paginator::resolveCurrentPath()] // Path for pagination links
            );
    
            return self::formatPagination($paginator);
        }
    
        // Return data without pagination
        return ["data" => $shuffledData];
    }
    
    public static function searchAndPaginateFriendship(
        $query,
        $searchTerm = null,
        $searchColumns = [],
        $perPage = 10,
        $paginate = false,
        $sortColumn = 'id',
        $sortOrder = 'DESC',
        $searchRelation = []
    ): array {
        // Jika ada search term, filter data berdasarkan kolom
        if (!empty($searchRelation) && $searchTerm && !empty($searchColumns)) {
            // foreach ($searchRelation as $columnRelation) {
            //     $query->whereHas($columnRelation, function ($q) use ($searchTerm, $searchColumns) {
            //         foreach ($searchColumns as $column) {
            //             $q->Where($column, 'like', '%' . $searchTerm . '%');
            //         }
            //     });
            // }
            $query->whereHas($searchRelation[0], function ($q) use ($searchTerm, $searchColumns) {
                // foreach ($searchColumns as $column) {
                $q->Where('name', 'like', '%' . 'sss' . '%');
                // }
            });
        } elseif (empty($searchRelation) && $searchTerm && !empty($searchColumns)) {
            $query->where(function ($q) use ($searchTerm, $searchColumns) {
                foreach ($searchColumns as $column) {
                    $q->orWhere($column, 'like', '%' . '$searchTerm' . '%');
                }
            });
        }

        $query->orderBy($sortColumn, strtoupper($sortOrder));


        if ($paginate) {
            return self::formatPagination($query->paginate($perPage));
        }

        return ["data" => $query->get()];
    }

}
