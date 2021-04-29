<div class="p-6 sm:px-20 bg-white border-b border-gray-200">
    @if (session()->has('message'))
        <div x-data="{ show: false }" x-init="() => {
                setTimeout(() => show = true, 500);
                setTimeout(() => show = false, 15000);
            }" x-show="show"
            class="fixed inset-x-0 top-10 flex items-end justify-center px-4 py-6 pointer-events-none sm:p-6 sm:items-start sm:justify-end z-50">
            <div x-description="Notification panel, show/hide based on alert state." @click.away="show = false"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-90"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-90"
                class="bg-green-100 max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto">
                <div class="rounded-lg shadow-xs overflow-hidden">
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                {{-- <x-svg.icons.check-circle class="h-6 w-6 text-green-400"/> --}}
                            </div>
                            <div class="ml-3 w-0 flex-1 pt-0.5">
                                <p class="text-sm leading-5 font-medium text-gray-900">
                                    {{ session('message') }}
                                </p>
                            </div>
                            <div class="ml-4 flex-shrink-0 flex">
                                <button @click="show = false"
                                    class="inline-flex text-gray-400 focus:outline-none focus:text-gray-500 transition ease-in-out duration-150">
                                    {{-- <x-svg.icons.x class="h-5 w-5" /> --}}
                                    <svg class="fill-current h-6 w-6 text-gray-700" role="button"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <title>Close</title>
                                        <path
                                            d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                                    </svg>

                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="mt-8 text-2xl flex justify-between">
        <div class="page-head">
            Questions <span class="text-sm">
                @if ($this->selectedCategory != '' && $this->selectedCategoryName)(
                    {{ $this->selectedCategoryName }} ) @endif
            </span>
        </div>
        <div>
            <x-jet-button wire:click="confirmQuestionAdd" class="bg-blue-500 hover:bg-blue-700">
                {{ __('Add New') }}
            </x-jet-button>
        </div>
    </div>
    {{-- {{$query}} --}}

    <div class="mt-6">
        <div class="flex justify-between">
            <div class="select-box w-200">
                <select name="selectedCategory" wire:model="selectedCategory"
                    class="block w-300 border shadow bg-white">
                    <option value=''>Choose an category</option>
                    @foreach ($categories as $category)
                        <option value={{ $category->id }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="search-field">
                <input autocomplete="off" wire:model.debounce.500ms="q" type="search" placeholder="Search"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
            </div>
        </div>
        @if ($questions->count())
            <div class="flex flex-col mt-4">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        @if ($this->selectedCategory == '')
                                            <th scope="col"
                                                class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Category Name
                                            </th>
                                        @endif
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Question
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Difficulty
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Correct Answer
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Incorrect Answers
                                        </th>
                                        <th scope="col" class="relative px-6 py-3">

                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" x-max="1">
                                    @foreach ($questions as $question)
                                        <tr>
                                            @if ($this->selectedCategory == '')
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $question->category->name }}
                                                </td>
                                            @endif
                                            <td class="px-6 py-4 whitespace-nowrap" title="{{ $question->text }}">
                                                {{ strlen($question->text) > 50 ? substr($question->text, 0, 50) . '...' : $question->text }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    {{ $question->difficulty }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $question->correct_answer }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if ($question->incorrect_answers != '')
                                                    <div>
                                                        @foreach (explode(',', $question->incorrect_answers) as $answer)
                                                            {{ $answer }}<br />
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex justify-center">
                                                    <div wire:click="confirmQuestionEdit({{ $question->id }})"
                                                        class="flex items-center justify-center cursor-pointer">
                                                        <svg class="fill-current h-4 w-4 text-indigo-600 hover:text-indigo-900"
                                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                            fill="currentColor">
                                                            <path
                                                                d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                                            <path fill-rule="evenodd"
                                                                d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        <a class="ml-1 text-indigo-600 hover:text-indigo-900">Edit</a>
                                                    </div>
                                                    @if (auth()->user()->role_id == 1)
                                                        <div wire:click="confirmQuestionDeletion({{ $question->id }})"
                                                            class="flex items-center justify-center cursor-pointer ml-4">
                                                            <svg class="fill-current h-4 w-4 text-red-600 hover:text-red-900"
                                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                                fill="currentColor">
                                                                <path fill-rule="evenodd"
                                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                            <a class="ml-1 text-red-600 hover:text-red-900">Delete</a>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="mt-4 bg-white overflow-hidden">
                <div class="header-container container flex justify-between p-10">
                    <p class="w-full text-center">No Data Found</p>
                </div>
            </div>
        @endif
    </div>

    <div class="mt-4">
        {{ $questions->links() }}
    </div>

    <!-- Delete Question Confirmation Modal -->
    <x-jet-dialog-modal wire:model="confirmingQuestionDeletion">
        <x-slot name="title">
            {{ __('Delete Question') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you want to delete question?') }}
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$set('confirmingQuestionDeletion', false)">
                {{ __('Cancel') }}
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-2" wire:click="deleteQuestion({{ $confirmingQuestionDeletion }})">
                {{ __('Delete') }}
            </x-jet-danger-button>
        </x-slot>
    </x-jet-dialog-modal>

    <!-- Add Question Modal -->
    <x-jet-dialog-modal wire:model="confirmingQuestionAdd">
        <x-slot name="title">
            <div class="font-bold">{{ isset($this->question->id) ? 'Edit Question' : 'Add Question' }}</div>
        </x-slot>

        <form wire:submit.prevent="saveQuestion">
            <x-slot name="content">
                <!-- Category -->
                <div class="col-span-6 sm:col-span-4">
                    <x-jet-label for="category_id" value="{{ __('Category') }}" />
                    <select name="category_id" wire:model="question.category_id"
                        class="mt-1 block w-full border shadow p-2 bg-white">
                        <option value=''>Choose category</option>
                        @foreach ($categories as $category)
                            <option value={{ $category->id }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <x-jet-input-error for="question.category_id" class="mt-2" />
                </div>
                <!-- Question -->
                <div class="col-span-6 sm:col-span-4 mt-4">
                    <x-jet-label class="font-semibold" for="text" value="{{ __('Question') }}" />
                    <x-jet-input id="text" type="text" class="mt-1 block w-full" wire:model.defer="question.text"
                        autocomplete="off" />
                    <x-jet-input-error for="question.text" class="mt-2" />
                </div>
                <!-- Type -->
                <div class="col-span-6 sm:col-span-4 mt-4">
                    <x-jet-label for="type" value="{{ __('Type') }}" />
                    <select name="type" wire:model="question.type" class="mt-1 block w-full border shadow p-2 bg-white">
                        <option value=''>Choose type</option>
                        <option value='multiple'>Multiple Choice</option>
                        <option value='boolean'>Yes/No</option>
                    </select>
                    <x-jet-input-error for="question.type" class="mt-2" />
                </div>
                <!-- Difficulty -->
                <div class="col-span-6 sm:col-span-4 mt-4">
                    <x-jet-label for="difficulty" value="{{ __('Difficulty') }}" />
                    <select name="difficulty" wire:model="question.difficulty"
                        class="mt-1 block w-full border shadow p-2 bg-white">
                        <option value=''>Choose difficulty</option>
                        <option value='easy'>Easy</option>
                        <option value='medium'>Medium</option>
                        <option value='hard'>Hard</option>
                    </select>
                    <x-jet-input-error for="question.difficulty" class="mt-2" />
                </div>
                <!-- Correct Answer -->
                <div class="col-span-6 sm:col-span-4 mt-4">
                    <x-jet-label class="font-semibold" for="correct_answer" value="{{ __('Correct Answer') }}" />
                    <x-jet-input id="correct_answer" type="text" class="mt-1 block w-full"
                        wire:model.defer="question.correct_answer" autocomplete="off" />
                    <x-jet-input-error for="question.correct_answer" class="mt-2" />
                </div>
                <!-- Incorrect Answers -->
                <div class="col-span-6 sm:col-span-4 mt-4">
                    <x-jet-label class="font-semibold" for="incorrect_answers"
                        value="{{ __('Incorrect Answers') }}" />
                    <x-jet-input id="incorrect_answers" type="text" class="mt-1 block w-full"
                        wire:model.defer="question.incorrect_answers" autocomplete="off" />
                    <x-jet-input-error for="question.incorrect_answers" class="mt-2" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <div wire:loading wire:target="saveQuestion" class="text-sm text-gray-500 italic">Saving Data...</div>
                <x-jet-secondary-button wire:click="$set('confirmingQuestionAdd', false)">
                    {{ __('Cancel') }}
                </x-jet-secondary-button>

                <x-jet-danger-button wire:loading.class="opacity-50 cursor-not-allowed" type="submit" class="ml-2"
                    wire:click="saveQuestion()">
                    {{ __('Save') }}
                </x-jet-danger-button>
            </x-slot>
        </form>
    </x-jet-dialog-modal>
</div>
