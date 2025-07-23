<?php

namespace App\Helpers;

use App\Dto\UserNameDto;
use JetBrains\PhpStorm\ArrayShape;

class NameComparator
{
    /**
     * Compares two strings and returns the percentage similarity.
     */
    public static function compareNames(string $left, string $right): float
    {
        $left = strtolower(trim($left));
        $right = strtolower(trim($right));

        $distance = levenshtein($left, $right);
        $maxLen = max(strlen($left), strlen($right));
        return $maxLen > 0 ? 100 - ($distance / $maxLen * 100) : 100;
    }

    /**
     * Compares first name and last name from the database and API response.
     *
     * @param UserNameDto $dbRecord data from the database.
     * @param UserNameDto $apiResponse data from the API.
     * @return array Array containing similarity percentages for first and last names.
     */
    #[ArrayShape(['first_name_similarity' => "float", 'last_name_similarity' => "float"])]
    public static function comparePersonInfo(UserNameDto $dbRecord, UserNameDto $apiResponse): array
    {
        return [
            'first_name_similarity' => self::compareNames($dbRecord->firstName, $apiResponse->firstName),
            'last_name_similarity' => self::compareNames($dbRecord->lastName, $apiResponse->lastName),
        ];
    }
}
