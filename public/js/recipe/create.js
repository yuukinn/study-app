
window.onload = function () {

    // preview
    // ①preview属性設定
    let preview = document.getElementById('preview');
    let image = document.getElementById('image');
    image.addEventListener('change', function(evt){
        let file = evt.target.files[0];
        if(file) {
            let reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    let steps = document.getElementById('steps');

    // ドラックアンドドロップで並べ替え
    Sortable.create(steps, {
        animation: 150,
        hadle: '.handle',
        onEnd: function() {
            let items = steps.querySelectorAll('.step');
            items.forEach(function(item, index){
                item.querySelector('.step-number').innerHTML = '手順' + (index + 1);
            });
        },
    });

    // 削除機能
    steps.addEventListener('click', function(evt){
        if (evt.target.classList.contains('step-delete') || evt.target.closest('.step-delete')) {
            evt.target.closest('.step').remove();
            let items = steps.querySelectorAll('.step');
            items.forEach(function(item, index){
                item.querySelector('.step-number').innerHTML = '手順' + (index + 1);
            })
        }
    })

    // 追加機能
    let addStep = document.getElementById('step-add');

    addStep.addEventListener('click', function(){
        let stepCount = steps.querySelectorAll('.step').length;
        let step = document.createElement('div');
        step.classList.add('step', 'flex', 'justify-between', 'items-center', 'mb-4');
        step.innerHTML = `

        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-10 h-10 handle text-gray-600">
            <path fill-rule="evenodd" d="M3 6.75A.75.75 0 0 1 3.75 6h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 6.75ZM3 12a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 12Zm0 5.25a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" />
        </svg>

        <p class="step-number w-16">手順${stepCount + 1}</p>
        <input type="text" name="steps[]" placeholder="手順を入力" class="border border-gray-300 p-2 w-full rounded">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-10 h-10 step-delete ml-4 text-gray-600">
        <path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 
        1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 
        52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z" clip-rule="evenodd" />
        </svg>`;
        steps.appendChild(step);
    })

    // ドラックアンドドロップで並べ替え
    let ingredients = document.getElementById('ingredients');
    Sortable.create(ingredients, {
        animation: 150,
        hadle: '.handle',
        onEnd: function() {
            let items = ingredients.querySelectorAll('.ingredient');
            items.forEach(function(item, index){
                item.querySelector('.ingredient-name').name = `ingredients[${index}][name]`;
                item.querySelector('.ingredient-quantity').name = `ingredients[${index}][quantity]`;
            });
        },
    });

    // 削除機能
    ingredients.addEventListener('click', function(evt){
        if (evt.target.classList.contains('ingredient-delete') || evt.target.closest('.ingredient-delete')) {
            evt.target.closest('.ingredient').remove();
            let items = ingredients.querySelectorAll('.ingredient');
            items.forEach(function(item, index){
                item.querySelector('.ingredient-name').name = `ingredients[${index}][name]`;
                item.querySelector('.ingredient-quantity').name = `ingredients[${index}][quantity]`;
            })
        }
    });

    // 追加機能
    let addIngredient = document.getElementById('ingredient-add');
    addIngredient.addEventListener('click', function(){
        let ingredientCount = steps.querySelectorAll('.step').length;
        let ingredient = document.createElement('div');
        ingredient.classList.add('ingredient', 'flex', 'items-center', 'mb-4');
        ingredient.innerHTML = `

        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-10 h-10 handle text-gray-600">
            <path fill-rule="evenodd" d="M3 6.75A.75.75 0 0 1 3.75 6h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 6.75ZM3 12a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 12Zm0 5.25a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" />
        </svg>

        <input type="text" name="ingredients[${ ingredientCount + 1 }][name]" placeholder="材料名" class="ingredient-name border border-gray-300 p-2 ml-4 w-full rounded">
            <p class="mx-2">:</p>
            <input type="text" name="ingredients[${ ingredientCount + 1 }][quantity]" placeholder="分量" class="ingredient-quantity border border-gray-300 p-2 w-full rounded">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-10 h-10 ml-4 ingredient-delete">
            <path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 
            1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 
            52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z" clip-rule="evenodd" />
            </svg>`;
      
        ingredients.appendChild(ingredient);
    });

    // destory confirm
    let destroy = document.getElementById('delete');

    destroy.addEventListener('click', function(evt){
        if (!confirm('本当に削除しますか？')){
            evt.preventDefault();
        }
    });

};//window.onload = function(){}