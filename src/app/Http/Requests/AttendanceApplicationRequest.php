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
            'applied_clock_out' => ['required', 'date_format:H:i', 'after_or_equal:applied_clock_in'],

            'applied_break_start.*' => ['nullable', 'date_format:H:i'],
            'applied_break_end.*' => ['nullable', 'date_format:H:i', 'after_or_equal:applied_break_start.*'],

            'applied_remarks' => ['required', 'string'],
        ];
    }

    public function messages() {
        return [
            'applied_clock_in.required' => '出勤時間は必須です',
            'applied_clock_in.date_format' => '出勤時間が不適切な値です',
            'applied_clock_out.required' => '退勤時間は必須です',
            'applied_clock_out.date_format' => '退勤時間が不適切な値です',
            'applied_clock_out.after_or_equal' => '出勤時間もしくは退勤時間が不適切な値です',

            'applied_break_start.*.date_format' => '休憩開始時間が不適切な値です',
            'applied_break_end.*.date_format' => '休憩終了時間が不適切な値です',
            'applied_break_end.*.after_or_equal' => '休憩開始時間もしくは休憩終了時間が不適切な値です',

            'applied_remarks.required' => '備考を記入してください',
        ];
    }

    public function withValidator($validator) {
        $validator->after(function ($validator) {
            $clockIn = $this->input('applied_clock_in');
            $clockOut = $this->input('applied_clock_out');
            $starts = $this->input('applied_break_start', []);
            $ends = $this->input('applied_break_end', []);

            if ($clockIn && $clockOut) {
                foreach ($starts as $i => $start) {
                    $end = $ends[$i] ?? null    ;

                    if ($start && !$end) {
                        $validator->errors()->add("applied_break_end.$i", '休憩終了時間を入力してください');
                        continue;
                    }
                    if ($end && !$start) {
                        $validator->errors()->add("applied_break_start.$i", '休憩開始時間を入力してください');
                        continue;
                    }

                    if($start && $end) {
                        if ($start < $clockIn || $start > $clockOut) {
                            $validator->errors()->add("applied_break_start.$i", '休憩時間が不適切な値です');
                        }

                        if ($end > $clockOut) {
                            $validator->errors()->add("applied_break_end.$i", '休憩時間もしくは退勤時間が不適切な値です');
                        }
                    }
                }
                for($i = 0; $i < count($starts); $i++) {
                    if(empty($starts[$i]) || empty($ends[$i])) continue;

                    for($j = $i + 1; $j < count($starts); $j++) {
                        if(empty($starts[$j]) || empty($ends[$j])) continue;

                        if($starts[$i] < $ends[$j] && $ends[$i] > $starts[$j]) {
                            $validator->errors()->add("applied_break_start.$i", '休憩時間が他と重複しています');
                            $validator->errors()->add("applied_break_start.$j", '休憩時間が他と重複しています');
                        }
                    }
                }
            }
        });
    }
}
