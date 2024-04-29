<x-app-layout>
    <div class="p-4 mx-auto bg-white rounded">
        {{ Breadcrumbs::render('show', $recipe) }}
        <!-- レシピ詳細 -->
        <div class="grid grid-cols-2 rounded border border-gray-500 mt-4">
            <div class="col-span-1">
                <img src="{{ $recipe['image'] }}" alt="{{ $recipe['title'] }}" class="object-cover w-full aspect-square">
            </div>
            <div class="col-span-1 p-4 flex justify-between">
                <div class="">
                    <p class="mb-4">{{ $recipe['description'] }}</p>
                    <p class="mb-4 text-gray-500">{{ $recipe['user']['name'] }}</p>
                    <h4 class="text-2xl font-bold mb-2">材料</h4>
                    <ul class="text-gray-500 ml-6">
                @foreach($recipe['ingredients'] as $i)
                        <li>{{ $i['name']}} : {{ $i['quantity'] }}</li>
                @endforeach
                    </ul>
                </div>
                <div class="justify-end">
                    <button id="favorite">
                        <svg xmlns="http://www.w3.org/2000/svg" id="favorite-svg" fill=" {{ ($is_favorite['favorite'] ?? false) ? 'yellow' : 'none' }} " viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <input type="hidden" id="is_favorite" value="{{ $is_favorite['favorite'] ?? false ? $is_favorite['favorite'] : ''}}">
        <br>
        <!-- steps -->
        <div class="">
            <h4 class="text-2xl font-bold mb-6">作り方</h4>
            <div class="grid grid-cols-4 gap-4">
                @foreach($recipe['steps'] as $s)
                    <div class="mb-2 background-color p-2">
                        <div class="w-10 h-10 flex items-center justify-center bg-gray-100 rounded-full mr-4 mb-2">
                            {{ $s['step_number']}}
                        </div>
                        <p>{{ $s['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @if($is_my_recipe)
    <a href="{{ route('recipe.edit', ['id' => $recipe['id']]) }}" class="block w-2/12 p-4 my-4 mx-auto bg-white rounded text-center text-green-500 border border-green-500 hover:bg-green-500 hover:text-white">編集する</a>
    @endif

    <!-- review -->
    @guest
        <p class="text-center text-gray-500">レビューを投稿するには<a href="{{ route('login') }}" class="text-blue-500">ログイン</a>してください</p>
    @endguest
    @auth
    @if($is_reviewed)
        <p class="text-center text-gray-500 mb-4">レビューは投稿済みです</p>
    @elseif($is_my_recipe)
        <p class="text-center text-gray-500 mb-4">自分のレシピにはレビューできません</p>
    @else
        <div class="p-4 mx-auto bg-white rounded mb-4">
            <form action="{{ route('review.store', ['id' => $recipe['id']]) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="rating" class="block text-gray-700 text-sm font-bold mb-2">
                        評価
                    </label>
                    <select name="rating" id="rating" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-2 px-4 pr-8 rounded">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="comment" class="bock text-gray-700 text-sm font-bold mb-2">
                        コメント
                    </label>
                    <textarea name="comment" id="comment" cols="30" rows="10" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-2 pr-8 rounded"></textarea>
                </div>
                <div class="flex items-center justify-between">
                    <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" type="submit">
                        レビュ-を投稿する
                    </button>
                </div>
            </form>
        </div>
    @endif
    @endauth
    <div class="p-4 mx-auto bg-white rounded">
        <h4 class="text-2xl font-bold mb-2">レビュー</h4>
        @if(count($recipe['reviews']) == CommonConst::EMPTY)
        <p>レビューがまだありません</p>
        @endif
    @foreach($recipe['reviews'] as $r)
        <div class="background-color rounded mb-4 p-4">
            <div class="flex mb-4">
            @for($i = 0; $i < $r['rating']; $i++)
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 text-yellow-400">
                    <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 
                    5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 
                    2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 
                    2.082-5.005Z" clip-rule="evenodd" />
                </svg>
            @endfor
                <p class="ml-2">{{ $r['comment'] }}</p>
            </div>
            <p class="text-gray-600 font-bold">{{ $r['user']['name'] }}</p>
        </div>
    @endforeach
   
    </div>
</x-app-layout>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    document.getElementById('favorite').addEventListener('click', function(){

    let csrfToken = $('meta[name="csrf-token"]').attr('content');

    let currentUrl = document.URL;

    let is_favorite = document.getElementById('is_favorite');
    
    console.log(typeof is_favorite);

    if (is_favorite.value == ''){
        console.log("o")
        $.ajax({
            url:currentUrl + '/favorite/store',
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfToken
            },
            success: function(res){
                let favorite = document.getElementById('is_favorite');
                favorite.value = res;
                let savgElement = document.getElementById('favorite-svg')
                savgElement.setAttribute('fill', 'yellow');
            },
            error: function(xhr, status, error){
                console.log('Ajaxデータ送信エラー:', error);
            }
        });
    } else {
        $.ajax({
            url:currentUrl + '/favorite/edit',
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfToken
            },
            success: function(res){
                console.log(res);
                let favorite = document.getElementById('is_favorite');
                favorite.value = res;
                // 非同期通信を行なっているので、色の値をセット
                if (res) {
                    let savgElement = document.getElementById('favorite-svg')
                    savgElement.setAttribute('fill', 'yellow');
                } else {
                    let savgElement = document.getElementById('favorite-svg')
                    savgElement.setAttribute('fill', 'none');
                }
            },
            error: function(xhr, status, error){
                console.log('Ajaxデータ送信エラー:', error);
            }
        });

    }

});
</script>