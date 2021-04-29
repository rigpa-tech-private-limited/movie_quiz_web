<?php

namespace App\Http\Livewire;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\WithPagination;

class Categories extends Component
{
    use WithPagination;
    public $q = '';
    public $sortBy = 'id';
    public $sortAsc = true;
    public $category;
    public $confirmingCategoryDeletion = false;
    public $confirmingCategoryAdd = false;

    protected $queryString = [
        'q' => ['except' => ''],
        'sortBy' => ['except' => 'id'],
        'sortAsc' => ['except' => true],
    ];

    protected $rules = [
        'category.name' => 'required'
    ];

    public function mount()
    {
        
    }

    public function render()
    {
        $categories = Category::whereNotNull('created_at')
            ->where('status', '!=', '2')
            ->when($this->q, function ($query) {
                return $query->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->q . '%');
                });
            })
            ->orderBy($this->sortBy, $this->sortAsc ? 'ASC' : 'DESC');
        $query = $categories->toSql();
        $categories = $categories->paginate(10);
        
        return view('livewire.categories', [
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

    public function confirmCategoryDeletion($confirmingCategoryDeletion)
    {
        $this->confirmingCategoryDeletion = $confirmingCategoryDeletion;
    }

    public function deleteCategory(Category $category)
    {
        $updated = DB::table('categories')
            ->where('id', $category->id)
            ->update([
                'status' => 2,
            ]);

        if ($updated) {
            $this->confirmingCategoryDeletion = false;
            session()->flash('message', 'Category Deleted Successfully');
        }
    }

    public function confirmCategoryAdd()
    {
        $this->reset(['category']);
        $this->confirmingCategoryAdd = true;
    }

    public function confirmCategoryEdit(Category $category)
    {
        $this->resetErrorBag();
        $this->category = $category;
        $this->confirmingCategoryAdd = true;
    }

    public function saveCategory()
    {
        $this->validate();
        if (isset($this->category->id)) {
            $this->category->save();
            session()->flash('message', 'Category Saved Successfully');
        } else {
            Category::create([
                'name' => $this->category['name']
            ]);
            session()->flash('message', 'Category Added Successfully');
        }

        $this->confirmingCategoryAdd = false;
    }
}
