<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
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
            'title' => 'required|min:3',  // не менее 3 символов
            'body' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Поле :attribute обязательно к заполнению - не одобряю, такие проверки надо делать на фронте, а если пусто, то лепить туда просто ЗАГОЛОВОК, будет красиво и без перезагрузки страницы',
            'min' => 'Поле :attribute должно состоять минимум из трёх символов - не одобряю, такие проверки надо делать на фронте, а если пусто, то лепить туда просто ЗАГОЛОВОК',
        ];
    }
}
