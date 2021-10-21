import axios from "axios"

class Search {
  // 1. describe and create/initiate our object
  constructor() {
    this.addSearchHTML()
    this.resultsDiv = document.querySelector("#search-overlay__results")
    this.openButton = document.querySelectorAll(".js-search-trigger")
    this.closeButton = document.querySelector(".search-overlay__close")
    this.searchOverlay = document.querySelector(".search-overlay")
    this.searchField = document.querySelector("#search-term")
    this.isOverlayOpen = false
    this.isSpinnerVisible = false
    this.previousValue
    this.typingTimer
    this.events()
  }

  // 2. events
  events() {
    this.openButton.forEach(el => {
      el.addEventListener("click", e => {
        e.preventDefault()
        this.openOverlay()
      })
    })

    this.closeButton.addEventListener("click", () => this.closeOverlay())
    document.addEventListener("keydown", e => this.keyPressDispatcher(e))
    this.searchField.addEventListener("keyup", () => this.typingLogic())
  }

  // 3. methods (function, action...)
  typingLogic() {
    if (this.searchField.value != this.previousValue) {
      clearTimeout(this.typingTimer)

      if (this.searchField.value) {
        if (!this.isSpinnerVisible) {
          this.resultsDiv.innerHTML = '<div class="spinner-loader"></div>'
          this.isSpinnerVisible = true
        }
        this.typingTimer = setTimeout(this.getResults.bind(this), 750)
      } else {
        this.resultsDiv.innerHTML = ""
        this.isSpinnerVisible = false
      }
    }

    this.previousValue = this.searchField.value
  }

  async getResults() {
    try {
      const response = await axios.get(universityData.root_url + "/wp-json/university/v1/search?term=" + this.searchField.value)
      const results = response.data
      this.resultsDiv.innerHTML = `
        <div class="row">
          <div class="one-third">
            <h2 class="search-overlay__section-title">General Information</h2>
            ${results.generalInfo.length ? '<ul class="link-list min-list">' : "<p>No general information matches that search.</p>"}
              ${results.generalInfo.map(item => `<li><a href="${item.permalink}">${item.title}</a> ${item.postType == "post" ? `by ${item.authorName}` : ""}</li>`).join("")}
            ${results.generalInfo.length ? "</ul>" : ""}
          </div>
          <div class="one-third">
            <h2 class="search-overlay__section-title">Programs</h2>
            ${results.programs.length ? '<ul class="link-list min-list">' : `<p>No programs match that search. <a href="${universityData.root_url}/programs">View all programs</a></p>`}
              ${results.programs.map(item => `<li><a href="${item.permalink}">${item.title}</a></li>`).join("")}
            ${results.programs.length ? "</ul>" : ""}

            <h2 class="search-overlay__section-title">Professors</h2>
            ${results.professors.length ? '<ul class="professor-cards">' : `<p>No professors match that search.</p>`}
              ${results.professors
          .map(
            item => `
                <li class="professor-card__list-item">
                  <a class="professor-card" href="${item.permalink}">
                    <img class="professor-card__image" src="${item.image}">
                    <span class="professor-card__name">${item.title}</span>
                  </a>
                </li>
              `
          )
          .join("")}
            ${results.professors.length ? "</ul>" : ""}

          </div>
          <div class="one-third">
            <h2 class="search-overlay__section-title">Campuses</h2>
            ${results.campuses.length ? '<ul class="link-list min-list">' : `<p>No campuses match that search. <a href="${universityData.root_url}/campuses">View all campuses</a></p>`}
              ${results.campuses.map(item => `<li><a href="${item.permalink}">${item.title}</a></li>`).join("")}
            ${results.campuses.length ? "</ul>" : ""}

            <h2 class="search-overlay__section-title">Events</h2>
            ${results.events.length ? "" : `<p>No events match that search. <a href="${universityData.root_url}/events">View all events</a></p>`}
              ${results.events
          .map(
            item => `
                <div class="event-summary">
                  <a class="event-summary__date t-center" href="${item.permalink}">
                    <span class="event-summary__month">${item.month}</span>
                    <span class="event-summary__day">${item.day}</span>  
                  </a>
                  <div class="event-summary__content">
                    <h5 class="event-summary__title headline headline--tiny"><a href="${item.permalink}">${item.title}</a></h5>
                    <p>${item.description} <a href="${item.permalink}" class="nu gray">Learn more</a></p>
                  </div>
                </div>
              `
          )
          .join("")}

          </div>
        </div>
      `
      this.isSpinnerVisible = false
    } catch (e) {
      console.log(e)
    }
  }

  keyPressDispatcher(e) {
    if (e.keyCode == 83 && !this.isOverlayOpen && document.activeElement.tagName != "INPUT" && document.activeElement.tagName != "TEXTAREA") {
      this.openOverlay()
    }

    if (e.keyCode == 27 && this.isOverlayOpen) {
      this.closeOverlay()
    }
  }

  openOverlay() {
    this.searchOverlay.classList.add("search-overlay--active")
    document.body.classList.add("body-no-scroll")
    this.searchField.value = ""
    setTimeout(() => this.searchField.focus(), 301)
    console.log("our open method just ran!")
    this.isOverlayOpen = true
    return false
  }

  closeOverlay() {
    this.searchOverlay.classList.remove("search-overlay--active")
    document.body.classList.remove("body-no-scroll")
    console.log("our close method just ran!")
    this.isOverlayOpen = false
  }

  addSearchHTML() {
    document.body.insertAdjacentHTML(
      "beforeend",
      `
      <div class="search-overlay">
        <div class="search-overlay__top">
          <div class="container">
            <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
            <input type="text" class="search-term" placeholder="What are you looking for?" id="search-term">
            <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
          </div>
        </div>
        
        <div class="container">
          <div id="search-overlay__results"></div>
        </div>

      </div>
    `
    )
  }
}

export default Search


// ***************************** JQUERY LIVE SEARCH *********************************************
// import $ from 'jquery';

// class Search {
//     // 1. Descripbe and create/initiate our object
//     constructor() {
//       this.addSearchHTML();
//       this.openButton = $(".js-search-trigger");
//       this.closeButton = $(".search-overlay__close");
//       this.searchOverlay = $(".search-overlay");
//       this.searchField = $("#search-term");
//       this.resultsDiv = $("#search-overlay--results");
//       // this makes sure that the event listeners are being called as soon as the page loads
//       this.events();
//       // This stores the state of the overlay
//       this.isOverlayOpen = false;
//       this.isSpinnerVisable = false;
//       this.previousValue;
//       this.typingTimer;
//     }
//     // 2. Events,  Where we connect the object to the methods. 
//     events() {
//       this.openButton.on("click", this.openOverlay.bind(this));
//       this.closeButton.on("click", this.closeOverlay.bind(this));
//       $(document).on("keydown", this.keyPressDispatcher.bind(this));
//       this.searchField.on("keyup", this.typingLogic.bind(this));
//     }

//     // 3. Methods (functions, actions...)
//     openOverlay() {
//       this.searchOverlay.addClass("search-overlay--active");
//       $("body").addClass("body-no-scroll");
//       this.searchField.val('');
//       // Adds focus to the search field
//       setTimeout(() => { this.searchField.focus(); }, 301)
//       this.isOverlayOpen = true;
//       return false;
//     }

//     closeOverlay() {
//       this.searchOverlay.removeClass("search-overlay--active");
//       $("body").removeClass("body-no-scroll");
//       this.isOverlayOpen = false;
//     }

//     // GET SEARCH RESULTS: This method runs after the 750ms delay in keystrokes. We use JS to send out a request to a URL, like json...
//     getResults() {
//       $.getJSON(universityData.root_url + '/wp-json/university/v1/search?term=' + this.searchField.val(), (results) => {
//         this.resultsDiv.html(`
//           <div class="row">
//             <div class="one-third">
//               <h2 class="search-overlay__section-title">General Information</h2>
//               ${results.generalInfo.length ? '<ul class="link-list min-list">' : '<p>No general information matches that search.</p>'}

//               ${results.generalInfo.map(item => `
//                 <li><a href="${item.permalink}">
//                   ${item.title}
//                   </a> ${item.postType == 'post' ? ` by ${item.authorName}` : ''}
//                 </li>`).join('')}

//               ${results.generalInfo.length ? '</ul>' : ''}
//             </div>
//             <div class="one-third">
//               <h2 class="search-overlay__section-title">Programs</h2>
//               ${results.programs.length ? '<ul class="link-list min-list">' : `<p>No programs matches that search. <a href="${universityData.root_url}/programs">View all programs</a></p>`}

//               ${results.programs.map(item => `
//                 <li><a href="${item.permalink}">
//                   ${item.title}
//                 </a></li>`).join('')}

//               ${results.programs.length ? '</ul>' : ''}

//               <h2 class="search-overlay__section-title">Professors</h2>
//               ${results.professors.length ? '<ul class="professor-cards">' : `<p>No professors matches that search.</p>`}

//               ${results.professors.map(item => `
//               <li class="professor-card__list-item">
//                 <a class="professor-card" href="${item.permalink}">
//                     <img class="professor-card__image" src="${item.image}"/>
//                     <span class="professor-card__name">
//                         ${item.title}
//                     </span>
//                 </a>
//               </li> 
//               `).join('')}

//               ${results.professors.length ? '</ul>' : ''}
//             </div>
//             <div class="one-third">
//               <h2 class="search-overlay__section-title">Campuses</h2>
//               ${results.campuses.length ? '<ul class="link-list min-list">' : `<p>No campuses matches that search. <a href="${universityData.root_url}/campuses">View all campuses</a></p>`}

//               ${results.campuses.map(item => `
//                 <li><a href="${item.permalink}">
//                   ${item.title}
//                 </a></li>`).join('')}

//               ${results.campuses.length ? '</ul>' : ''}

//               <h2 class="search-overlay__section-title">Events</h2>
//               ${results.events.length ? '' : `<p>No events matches that search. <a href="${universityData.root_url}/events">View all events</a></p>`}

//               ${results.events.map(item => `
//               <div class="event-summary">
//                 <a class="event-summary__date t-center" href="${item.permalink}">
//                     <span class="event-summary__month">
//                       ${item.month}
//                     </span>
//                     <span class="event-summary__day">
//                       ${item.day}
//                     </span>
//                 </a>
            
//                 <div class="event-summary__content">
//                     <h5 class="event-summary__title headline headline--tiny">
//                       <a href="${item.permalink}">
//                         ${item.title}  
//                       </a>
//                     </h5>
            
//                     <p>
//                       ${item.description}
//                       <a href="${item.permalink}" class="nu gray">Learn more</a>
//                     </p>
//                 </div>
//               </div>  
//               `).join('')}
//             </div>
//           </div>
//         `)
//         this.isSpinnerVisable = false;
//       })



//       // //.when() allows for multiple JS requests to be ran async && .then() is like the promisde. TThe when 
//       // //   results get mapped to the then paramater. They match up based on index.
//       // $.when(
//       //   // getJson is jQuery which uses the WP REST API URL to get, read, load the data. Once that is completed the 
//       //   // annoymnious callback function runs with the data displaying it
//       //   $.getJSON(universityData.root_url + '/wp-json/wp/v2/posts?search=' + this.searchField.val()), 
//       //   $.getJSON(universityData.root_url + '/wp-json/wp/v2/pages?search=' + this.searchField.val())
//       //   ).then((posts, pages) => {
//       //   // When we use a callback with getJSON there is only the data in there. However, since we are using
//       //   //  the when / then combo we have additional arrays so we grab the first one posts[0], because the data
//       //   //  is in there and posts[1 && 2], have additional data like success  or fail
//       //   var combinedResults = posts[0].concat(pages[0]); // This concats the posts and pages array
//       //     // TEMPLATE LITERAL: the `` is a template literal which allows us to just create html readable not like a aingle string in JS. It allows us 
//       //   //  to display just text. So we use ${} to say hey evaluate this as JS again. This code is not run unytil the server gets the data.
//       //   this.resultsDiv.html(`
//       //   <h2 class="search-overlay__section-title">General Information</h2>

//       //   ${combinedResults.length ? '<ul class="link-list min-list">' : '<p>No general information matches that search.</p>'}

//       //   ${combinedResults.map(item => `
//       //     <li><a href="${item.link}">
//       //       ${item.title.rendered}
//       //       </a> ${item.type == 'post' ? `by ${item.authorName}` : ''}
//       //     </li>`).join('')}

//       //   ${combinedResults.length ? '</ul>' : ''}
//       //   `);
//       //   // This brings the spinner back if searching more than once
//       //   this.isSpinnerVisable = false;
//       // }, () => {
//       //   this.resultsDiv.html('<p>Unexpected error: please try again.</p>');
//       // });
//     }

//     keyPressDispatcher(e) {
//       if (e.keyCode == 83 && !this.isOverlayOpen && !$("input, textarea").is(':focus')) {
//         this.openOverlay();
//       }

//       if (e.keyCode == 27 && this.isOverlayOpen) {
//         this.closeOverlay();
//       }
//     }

//     // logic that allows for search to wait until user has stopped typing
//     typingLogic() {
//       // Clear timeout allows us to reset the timer function allowing for the query to only fire one time once 
//       // Typing has stopped for 2 seconds
//       if (this.searchField.val() != this.previousValue) {
//         clearTimeout(this.typingTimer);

//         if (this.searchField.val()) {
//           if (!this.isSpinnerVisable) {
//             this.resultsDiv.html('<div class="spinner-loader"></div>');
//             this.isSpinnerVisable = true;
//           }
  
//           this.typingTimer = setTimeout(this.getResults.bind(this), 750);
//         } else {
//           this.resultsDiv.html('');
//           this.isSpinnerVisable = false;
//         }
//       }

      
//       this.previousValue = this.searchField.val();
//     }

//     addSearchHTML() {
//       $("body").append(`
//         <div class="search-overlay">
//           <div class="search-overlay__top">
//             <div class="container">
//               <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
//               <input type="text" class="search-term" id="search-term" placeholder="What are you looking for?" autocomplete="off"/>
//               <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
//             </div>
//           </div>
        
//           <div class="container">
//             <div id="search-overlay--results">
        
//             </div>
//           </div>
//         </div>
//       `)
//     }


// }

// export default Search;