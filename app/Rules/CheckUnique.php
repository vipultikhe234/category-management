<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

/**
 * Class CheckUnique
 *
 * A reusable Laravel validation rule that ensures a column value is unique
 * within the scope of another column.
 *
 * This rule supports an optional exception for a given record ID (useful during updates).
 *
 */
class CheckUnique implements Rule
{
    /**
     * @var string The table to query.
     */
    protected string $table;

    /**
     * @var string The field to validate for uniqueness.
     */
    protected string $column;

    /**
     * @var string The secondary (scoping) column for conditional uniqueness.
     */
    protected string $commonColumn;

    /**
     * @var mixed The value of the scoping column.
     */
    protected $commonValue;

    /**
     * @var mixed|null Optional ID to exclude from the check (e.g., during update).
     */
    protected $exceptId;


    /**
     * Create a new rule instance.
     *
     * @param string $table        The table to check.
     * @param string $column       The field that must be unique.
     * @param string $commonColumn The scoping field.
     * @param mixed  $commonValue  The value of the scoping field.
     * @param mixed|null $exceptId ID to exclude from check (used in update).
     */
    public function __construct(string $table, string $column, string $commonColumn, $commonValue, $exceptId = null)
    {
        $this->table = $table;
        $this->column = $column;
        $this->commonColumn = $commonColumn;
        $this->commonValue = $commonValue;
        $this->exceptId = $exceptId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * Checks if the value exists in the table with the same scoping value,
     * excluding the current record ID if provided.
     *
     * @param  string  $attribute The attribute name under validation.
     * @param  mixed   $value     The value being validated.
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $query = DB::table($this->table)
            ->where($this->column, $value)
            ->where($this->commonColumn, $this->commonValue);

        if ($this->exceptId) {
            $query->where('id', '!=', $this->exceptId);
        }

        return !$query->exists();
    }
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute has already been taken for the selected category.';
    }
}
