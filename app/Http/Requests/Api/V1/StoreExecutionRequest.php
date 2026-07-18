<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\ExecutionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExecutionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
 * @return array<string, array<int, mixed>>
 */
    public function rules(): array
    {
        return [
            'status'      => ['sometimes', Rule::enum(ExecutionStatus::class)->only([ExecutionStatus::Success, ExecutionStatus::Failed])],
            'duration_ms' => ['nullable', 'integer', 'min:0'],
            'metadata'    => ['nullable', 'array'],
        ];
    }
}
