// JQUERY VERSION  -----------------------------------------------------------------------
// import $ from 'jquery';

// class Like {
//     constructor() {
//         this.events();
//     }

//     events() {
//         $(".like-box").on("click", this.ourClickDispacher.bind(this))
//     }

//     // Methods
//     ourClickDispacher(e) {
//         // This is so if someone clicks on an <i></i> it will still find the closest parent or grandparent
//         //  So this variable allows only the heart box to be liked. We can add other boxes if wanted.
//         var currentLikeBox = $(e.target).closest(".like-box");
//         // if you want to pull in fresh updated attr values use attr() with the name NOT data() so the page updates on the fly.
//         if (currentLikeBox.attr('data-exists') == 'yes') {
//             this.deleteLike(currentLikeBox);
//         } else {
//             this.createLike(currentLikeBox);
//         }
//     }
    
//     createLike(currentLikeBox) {
//         $.ajax({
//             beforeSend: (xhr) => {
//                 xhr.setRequestHeader('X-WP-Nonce', universityData.nonce); // This is the nonce for WP we created in functions.php
//             },
//             url: universityData.root_url + '/wp-json/university/v1/manageLike',
//             type: 'POST',
//             data: {'professorId': currentLikeBox.data('professor')}, // We can also tack on to the end of the url: /manageLike?professorId=789
//             success: (response) => { 
//                 // Dynamically change the color of the heart full
//                 currentLikeBox.attr('data-exists', 'yes');
//                 //parseInt() will parse a string of text as a number. We then increament it and diaplay it
//                 var likeCount = parseInt(currentLikeBox.find(".like-count").html(), 10);
//                 likeCount++;
//                 currentLikeBox.find(".like-count").html(likeCount);
//                 currentLikeBox.attr('data-like', response) // set the value of the data-like 
//                 console.log('CREATE LIKE SUCCESS', response);
//             },
//             error: (response) => { console.log('CREATE LIKE ERROR', response)}
//         });
//     }

//     deleteLike(currentLikeBox) {
//         $.ajax({
//             beforeSend: (xhr) => {
//                 xhr.setRequestHeader('X-WP-Nonce', universityData.nonce); // This is the nonce for WP we created in functions.php
//             },
//             url: universityData.root_url + '/wp-json/university/v1/manageLike',
//             type: 'DELETE',
//             data: {'like': currentLikeBox.attr('data-like')},
//             success: (response) => { 
//                 currentLikeBox.attr('data-exists', 'no');
//                 var likeCount = parseInt(currentLikeBox.find(".like-count").html(), 10);
//                 likeCount--;
//                 currentLikeBox.find(".like-count").html(likeCount);
//                 currentLikeBox.attr("data-like", '')
//                 console.log('DELETE LIKE SUCCESS', response)
//             },
//             error: (response) => { console.log('DELETE LIKE ERROR', response.responseText)}
//         });
//     }
// }

// export default Like;

import axios from "axios"

class Like {
  constructor() {
    if (document.querySelector(".like-box")) {
      axios.defaults.headers.common["X-WP-Nonce"] = universityData.nonce
      this.events()
    }
  }

  events() {
    document.querySelector(".like-box").addEventListener("click", e => this.ourClickDispatcher(e))
  }

  // methods
  ourClickDispatcher(e) {
    let currentLikeBox = e.target
    while (!currentLikeBox.classList.contains("like-box")) {
      currentLikeBox = currentLikeBox.parentElement
    }

    if (currentLikeBox.getAttribute("data-exists") == "yes") {
      this.deleteLike(currentLikeBox)
    } else {
      this.createLike(currentLikeBox)
    }
  }

  async createLike(currentLikeBox) {
    try {
      const response = await axios.post(universityData.root_url + "/wp-json/university/v1/manageLike", { "professorId": currentLikeBox.getAttribute("data-professor") })
      if (response.data != "Only logged in users can create a like.") {
        currentLikeBox.setAttribute("data-exists", "yes")
        var likeCount = parseInt(currentLikeBox.querySelector(".like-count").innerHTML, 10)
        likeCount++
        currentLikeBox.querySelector(".like-count").innerHTML = likeCount
        currentLikeBox.setAttribute("data-like", response.data)
      }
      console.log(response.data)
    } catch (e) {
      console.log("Sorry")
    }
  }

  async deleteLike(currentLikeBox) {
    try {
      const response = await axios({
        url: universityData.root_url + "/wp-json/university/v1/manageLike",
        method: 'delete',
        data: { "like": currentLikeBox.getAttribute("data-like") },
      })
      currentLikeBox.setAttribute("data-exists", "no")
      var likeCount = parseInt(currentLikeBox.querySelector(".like-count").innerHTML, 10)
      likeCount--
      currentLikeBox.querySelector(".like-count").innerHTML = likeCount
      currentLikeBox.setAttribute("data-like", "")
      console.log(response.data)
    } catch (e) {
      console.log(e)
    }
  }
}

export default Like
