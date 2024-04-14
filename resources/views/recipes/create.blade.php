<x-app-layout>
    <x-slot name="script">
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.13.0/Sortable.min.js"></script>
        <script src="/js/recipe/create.js"></script>
    </x-slot>
<form action="{{ route('recipe.store' )}}" method="POST" class="w-10/12 p-4 mx-auto bg-white rounded" enctype="multipart/form-data">
    @csrf
   {{ Breadcrumbs::render('create')}} 
    <div class="grid grid-cols-2 rounded border border-gray-500 mt-4">
        <div class="col-span-1">
            <img src="/images/recipe-dummy.png" alt="レシピイメージ" class="object-cover w-full aspect-video">
            <input type="file" name="image" class="border border-gray-300 p-2 mb-4 w-full rounded">
        </div>
        <div class="col-span-1 p-4">
            <input type="text" name="title" placeholder="レシピ名" class="border border-gray-300 p-2 mb-4 w-full">
            <textarea name="description" placeholder="レシピの説明" class="border border-gray-300 p-2 mb-4 w-full rounded"></textarea>
            <!-- select -->
            <select name="category" class="border border-gray-300 p-2 mb-4 w-full rounded">
                <option value="">カテゴリ</option>
    @foreach($categories as $c)
                <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
    @endforeach
            </select>
            <!-- submit -->
            <div class="flex justify-end">
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">レシピを投稿</button>
            </div>
        </div>
    </div>
    <!-- under Line -->
    <hr class="my-4">
    <h4 class="text-bold text-xl mb-4">手順を入力</h4>
    <div id="steps">
    @for($i = 1; $i < 4; $i++)
        <div class="step flex justify-between">
            @include('components.bars-3')
            <p class="step-number">手順{{ $i }}</p>
            <input type="text" name="steps[]" placeholder="手順を入力" class="border border-gray-300 p-2 mb-4 w-full rounded">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
            <path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 
            1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 
            52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z" clip-rule="evenodd" />
            </svg>
        </div>
    @endfor
    </div>
</form>

</x-app-layout>