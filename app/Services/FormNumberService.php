<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class FormNumberService
{
    /**
     * Generate a form number with format: YY[form_code]-00001
     * Example: 2411-00001 (year 24, form code 11, sequence 1)
     *
     * @param string $formTypeCode Two-digit form type code (e.g., '11', '12', '21')
     * @param string $tableName Database table name
     * @return string Formatted form number
     */
    public static function generateFormNumber(string $formTypeCode, string $tableName): string
    {
        $year = now()->format('y');
        $prefix = $year . $formTypeCode . '-';

        $lastNumber = DB::table($tableName)
            ->where('form_number', 'like', $prefix . '%')
            ->orderBy('form_number', 'desc')
            ->value('form_number');

        if ($lastNumber) {
            $lastSequence = (int) substr($lastNumber, strpos($lastNumber, '-') + 1);
            $sequence = $lastSequence + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }
}

