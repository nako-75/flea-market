<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
    public function rules(){
        $user = auth()->user();

        return [
            'name' => 'required|max:20',
            'postcode' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address' => 'required',
            'image' => ($user && $user->profile_image) ? 'nullable|image|mimes:jpeg,png' : 'required|image|mimes:jpeg,png',
        ];
    }

    public function messages() {
        return [
            'name.required' => 'お名前を入力してください',
            'name.max' => 'お名前は20文字以内で入力してください',
            'postcode.required' => '郵便番号を入力してください',
            'postcode.regex' => '郵便番号はハイフンありの8文字で入力してください',
            'address.required' => '住所を入力してください',
            'image.required' => '画像を選択してください',
            'image.image' => '画像ファイルを選択してください',
            'image.mimes' => '画像はjpegまたはpng形式でアップロードしてください',
        ];
    }
}
