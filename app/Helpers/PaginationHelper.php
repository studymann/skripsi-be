<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class PaginationHelper
{
    public static function getNextPageUrl(Request $request, $currentPage, $perPage, $total)
    {
        if ($currentPage * $perPage < $total) {
            return $request->fullUrlWithQuery(['page' => $currentPage + 1]);
        }

        return null;
    }

    public static function getPrevPageUrl(Request $request, $currentPage, $perPage)
    {
        if ($currentPage > 1) {
            return $request->fullUrlWithQuery(['page' => $currentPage - 1]);
        }

        return null;
    }
}