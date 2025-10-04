<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'applied_clock_in' => ['required', 'date_format:H:i'],
            'applied_clock_out' => ['required', 'date_format:H:i', 'after:applied_clock_in'],

            'applied_break_start.*' => ['nullable', 'date_format:H:i'],
            'applied_break_end.*' => ['nullable', 'date_format:H:i', 'after:applied_break_start.*'],

            'applied_remarks' => ['required', 'string'],
        ];
    }

    public function messages() {
        return [
            'applied_clock_in.required' => '出勤時間は必須です',
            'applied_clock_in.date_format' => '出勤時間が不適切な値です',
            'applied_clock_out.required' => '退勤時間は必須です',
            'applied_clock_out.date_format' => '退勤時間が不適切な値です',
            'applied_clock_out.after' => '出勤時間もしくは退勤時間が不適切な値です',

            'applied_break_start.*.date_format' => '休憩開始時間が不適切な値です',
            'applied_break_end.*.date_format' => '休憩開始時間が不適切な値です',
            'applied_break_end.*.after' => '休憩時間が不適切な値です',

            'applied_remarks.required' => '備考を記入してください',
        ];
    }

    public function withValidator($validator) {
        $validator->after(function ($validator) {
            $clockIn = $this->input('applied_clock_in');
            $clockOut = $this->input('applied_clock_out');

            if ($clockIn && $clockOut) {
                foreach ($this->input('applied_break_start', []) as $i => $start) {
                    $end = $this->input("applied_break_end.$i");

                    if ($start && ($start < $clockIn || $start > $clockOut)) {
                        $validator->errors()->add("applied_break_start.$i", '休憩時間が不適切な値です');
                    }

                    if ($end && $end > $clockOut) {
                        $validator->errors()->add("applied_break_end.$i", '休憩時間もしくは退勤時間が不適切な値です');
                    }
                }
            }
        });
    }
}
