<?php

namespace App\Helpers\Http;

use App\Enums\HttpRequestPurpose;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class Select2
{
    public static function renderConditional(
        array|Collection $data,
        string           $idColumn,
        string|array     $textColumn,
        ?callable        $callback = null
    ): JsonResponse {
        if (HttpRequestPurpose::FORM_SELECT->lowercase() == request()->get('purpose')) {
            return self::render(
                data: $data,
                idColumn: $idColumn,
                textColumn: $textColumn,
                callback: $callback
            );
        }

        return (new Responder())->success($data);
    }

    public static function render(
        array|Collection|\Illuminate\Support\Collection|Builder $data,
        string                                                  $idColumn,
        string|array                                            $textColumn,
        int                                                     $limit = 15,
        ?callable                                               $callback = null
    ): JsonResponse {
        // Handle passed builder
        if ($data instanceof Builder) {
            $filter = request()->get('filter') ?? [];

            if (array_key_exists('status', $filter)) {
                $tableName = $data->getModel()->getTable();
                $data->where("$tableName.status", $filter['status']);
            }

            $data = $data->limit($limit)->get();
        }

        $separator = ' ';
        if (is_array($textColumn) && array_key_exists('separator', $textColumn)) {
            $separator = $textColumn['separator'];
            unset($textColumn['separator']);
        }

        $returnValue = [];

        foreach ($data as $index => $datum) {
            $colData = null;
            if (is_array($textColumn)) {
                foreach ($textColumn as $i => $colName) {
                    $colData .= sprintf("%s%s", ($i > 0 ? $separator : ''), $datum[$colName]);
                }
            } else {
                $colData = $datum[$textColumn];
            }

            $row = [
                'id' => $datum[$idColumn],
                'text' => trim($colData),
            ];

            if ($callback) {
                $row = $callback($row, $data, $index);
            }

            $returnValue[] = $row;
        }

        return response()->json([
            'count' => count($returnValue),
            'results' => $returnValue,
        ]);
    }
}
