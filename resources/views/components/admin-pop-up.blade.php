<div class="fixed top-0 left-0 w-screen h-screen z-50 hidden" id="popup">
    <div class="w-full h-full px-2 sm:px-4 lg:px-32 xl:px-72 py-8 relative z-50 flex items-center justify-center">
        <div class="w-full px-8 py-12 bg-white dark:bg-slate-800 rounded-md">
            <h1 class="dark:text-white text-2xl font-bold my-2" id="popup_title">
                Are you sure?
            </h1>
            <hr class="dark:border-slate-700">
            <p class="my-2 dark:text-white text-lg" id="popup_content">
                This action cannot be undone.
            </p>
            <div class="mt-4 flex">
                <div id="form">
                    <a href="#"><button id="popup_action" class="py-2 px-4 bg-green-400 hover:bg-green-300 dark:bg-green-700 dark:hover:bg-green-600 mr-2 rounded-sm dark:text-white">Continue</button></a>
                    <button id="popup_cancel" class="py-2 px-4 bg-gray-400 hover:bg-gray-300 dark:bg-slate-700 dark:hover:bg-slate-600 mx-2 rounded-sm dark:text-white">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <div class="absolute top-0 left-0 w-full h-full bg-black opacity-50 z-40">
    </div>
    <div class="absolute top-0 left-0 w-full h-full  backdrop-blur-sm z-40">
    </div>
</div>
<script>
    var popup = document.getElementById('popup');
    var popup_title = popup.querySelector('#popup_title');
    var popup_content = popup.querySelector('#popup_content');
    var popup_action = popup.querySelector('#popup_action');
    var popup_cancel = popup.querySelector('#popup_cancel');
    popup_cancel.onclick = function(){
        this.closest('#popup').style.display="none";
    }
    function display_popup(element){
        var content = element.getAttribute('data-content');
        var title = element.getAttribute('data-title');
        var action_url = element.getAttribute('data-action');
        var method = element.getAttribute('data-method');
        popup_title.innerHTML = title;
        popup_content.innerHTML = content;
        if(method != null && method != undefined && method != "GET"){
            var form = document.createElement('form');
            if(method == "DELETE")
                form.method = "POST";
            else
                form.method = method;
            var method_input = document.createElement('input');
            method_input.type = "hidden";
            method_input.name = "_method";
            method_input.value = method;
            form.appendChild(method_input);
            form.action = action_url;
            form.style.display = "none";
            var input = document.createElement('input');
            input.type = "hidden";
            input.name = "_token";
            input.value = "{{ csrf_token() }}";
            form.appendChild(input);
            document.body.appendChild(form);
            popup_action.onclick = function(){
                form.submit();
            }
        }else{
            popup_action.parentElement.href = action_url;
        }
        popup.style.display = "block";
    }
</script>