tinymce.init({
    selector: '#form_content',
    width: 600,
    height: 300,
});

let submitBtn = document.querySelector("#form_save");
submitBtn.addEventListener('click', ()=>{
    tinymce.triggerSave();
})