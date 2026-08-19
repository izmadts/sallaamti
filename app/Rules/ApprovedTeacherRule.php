<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ApprovedTeacherRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (blank($value)) {
            return;
        }

        $teacher = User::find($value);

        if (!$teacher || !$teacher->isApprovedTeacher()) {
            $fail('This teacher has not been vetted/approved yet — approve them from Users before assigning them to a class.');
        }
    }
}
