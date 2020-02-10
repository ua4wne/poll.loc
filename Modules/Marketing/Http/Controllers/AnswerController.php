<?php

namespace Modules\Marketing\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Marketing\Entities\Answer;
use Modules\Marketing\Entities\Catalog;
use Modules\Marketing\Entities\Question;

class AnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index($id)
    {
        if(!User::hasRole('admin') && !User::hasRole('market')){
            abort(503);
        }
        if(view()->exists('marketing::answers')){
            $question = Question::find($id);
            $title='Ответы на вопрос "'.$question->name.'"';
            $rows = Answer::where(['question_id'=>$id])->get();
            $data = [
                'title' => $title,
                'rows' => $rows,
                'question' => $question,
            ];
            return view('marketing::answers',$data);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request, $id)
    {
        if(!User::hasRole('admin') && !User::hasRole('market')){
            abort(503);
        }
        if($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
        }
        if(view()->exists('marketing::answer_add')){
            $question = Question::find($id);
            $htmlsel = array ('tmail' => 'Контактный email','tphone' => 'Контактный телефон','taddr' => 'Контактный адрес',
                'tradio' => 'Единичный выбор','tcheck' => 'Множественный выбор', 'tonetext' => 'Свой вариант (единичный выбор)',
                'tmultext' => 'Свой вариант (множественный выбор)', 'tonesel' => 'Выбор из списка (единичный выбор)',
                'tmulsel' => 'Выбор из списка (множественный выбор)');
            //выбираем доступные справочники
            $books = Catalog::all();
            $refsel = array();
            foreach ($books as $book){
                $refsel[$book->nameEN] = $book->nameRU;
            }
            $data = [
                'title' => 'Новая запись',
                'question' => $question,
                'htmlsel' => $htmlsel,
                'refsel' => $refsel,
            ];
            return view('marketing::answer_add', $data);
        }
        abort(404);
    }


    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function delete($id)
    {
        //
    }
}
