let likeContainer = document.getElementById("like-container");
let likeLink = likeContainer.querySelector("a");
likeLink.addEventListener("click", function1);

function function1(event) {
  event.preventDefault();
  fetch(like_post_url)
  .then(function(res) {
    return res.json()
  })
  .then(function(json){
    let spanLikes = likeContainer.querySelector('span');
    spanLikes.textContent = json.likes;
  })
}

// document.querySelector("#form_replyTo").lables[0].style.display = 'none';
// document.querySelector("#form_replyTo").parentNode.style.display = 'none';

let replyLinks = document.querySelectorAll(".show_reply");
let replyToField = document.querySelector('#form_replyTo')
replyLinks.forEach(link => {
    link.addEventListener('click', event => {
      event.preventDefault();
      let replyToId = link.getAttribute('comment-id');
      replyToField.value = replyToId;
      document.querySelector("#form_replyTo").value= replyToId;
      document.querySelector("#form_content").labels[0].textContent = 'Reply';
      document.querySelector("#form_save").textContent = 'Add Reply';
  });
})