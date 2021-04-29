<?php

namespace App\Http\Livewire;

use App\Models\Question;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\WithPagination;

class Questions extends Component
{
    use WithPagination;
    public $q = '';
    public $sortBy = 'id';
    public $sortAsc = true;
    public $selectedCategory;
    public $selectedCategoryName;
    public $confirmingQuestionDeletion = false;
    public $confirmingQuestionAdd = false;
    public $question = array();

    protected $queryString = [
        'q' => ['except' => ''],
        'sortBy' => ['except' => 'id'],
        'sortAsc' => ['except' => true],
    ];

    protected $rules = [
        'question.text' => 'required',
        'question.category_id' => 'required',
        'question.type' => 'required',
        'question.difficulty' => 'required',
        'question.correct_answer' => 'required',
        'question.incorrect_answers' => 'required'
    ];

    public function mount()
    {
        if (Session::get('categoryId')) {
            $this->selectedCategory = Session::get('categoryId');
        }
    }

    public function render()
    {
        Session::put('categoryId', $this->selectedCategory);
        $questions = Question::select('*', DB::raw('(SELECT text as correct_answer FROM answers WHERE answers.question_id=questions.id AND answers.is_correct = 1) as correct_answer'), DB::raw('(SELECT GROUP_CONCAT(text) as correct_answer FROM answers WHERE answers.question_id=questions.id AND answers.is_correct = 0 GROUP BY answers.question_id) as incorrect_answers'))
            ->whereNotNull('created_at')
            ->with('category')
            ->when($this->selectedCategory, function ($query) {
                return $query->where(function ($query) {
                    $query->where('category_id', $this->selectedCategory);
                });
            })
            ->when($this->q, function ($query) {
                return $query->where(function ($query) {
                    $query->where('text', 'like', '%' . $this->q . '%')
                        ->orwhere('type', 'like', '%' . $this->q . '%')
                        ->orwhere('difficulty', 'like', '%' . $this->q . '%');
                });
            })
            ->orderBy($this->sortBy, $this->sortAsc ? 'ASC' : 'DESC');
        $query = $questions->toSql();
        $questions = $questions->paginate(10);
        $categories = DB::table('categories')
            ->where('status', '1')
            ->get()->toArray();
        foreach ($categories as $index => $category) {
            if ($this->selectedCategory == $category->id) {
                $this->selectedCategoryName = $category->name;
            }
        }
        Session::put('categoryName', $this->selectedCategoryName);
        return view('livewire.questions', [
            'questions' => $questions,
            'categories' => $categories,
            'query' => $query,
        ]);
    }

    public function updatingQ()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($field == $this->sortBy) {
            $this->sortAsc = !$this->sortAsc;
        }
        $this->sortBy = $field;
    }

    public function confirmQuestionDeletion($confirmingQuestionDeletion)
    {
        $this->confirmingQuestionDeletion = $confirmingQuestionDeletion;
    }

    public function deleteQuestion(Question $question)
    {
        $question->delete();
        $this->confirmingQuestionDeletion = false;
        session()->flash('success', 'Question Deleted Successfully');
    }

    public function confirmQuestionAdd()
    {
        $this->reset(['question']);
        $this->confirmingQuestionAdd = true;
    }

    public function confirmQuestionEdit(Question $question)
    {
        $this->resetErrorBag();
        $this->question = $question;
        $this->confirmingQuestionAdd = true;
    }

    public function saveQuestion()
    {
        $validate = $this->validate();
        if (isset($this->question->id)) {
            $this->question->save();
            $Insertid = $this->question->id;
            session()->flash('success', 'Question Saved Successfully');
        } else {
            $Insertid = Question::create([
                'text' => trim($this->question['text']),
                'category_id' => $this->question['category_id'],
                'type' => $this->question['type'],
                'difficulty' => $this->question['difficulty'],
            ])->id;
            DB::table('answers')->insert(
                array(
                    'is_correct' => 1,
                    'text' => $this->question['correct_answer'],
                    'question_id' => $Insertid,
                )
            );
            if ($this->question['incorrect_answers'] != ""){
                $incorrectAnswersArr = explode(',',$this->question['incorrect_answers']);
                for($i=0;$i< count($incorrectAnswersArr); $i++){ 
                    DB::table('answers')->insert(
                        array(
                            'is_correct' => 0,
                            'text' => $incorrectAnswersArr[$i],
                            'question_id' => $Insertid,
                        )
                    );
                }
            }
            session()->flash('success', 'Question Added Successfully');
        }
        $this->confirmingQuestionAdd = false;
    }
}
