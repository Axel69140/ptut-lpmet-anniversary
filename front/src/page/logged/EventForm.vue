<script>
    import axios from 'axios';  
    import Footer from '../../components/Footer.vue';     
    import { userService } from '../../services/user.services';
    import { accountService } from '../../services/account.services';
    import { participantService } from '../../services/participant.services';

    export default {
        name: 'eventForm',
        data: () => ({ 
          guest: [],
          
          }),       
        computed: {            
        },
        methods: { 
          
        },
        mounted() {    
          /*idUser = accountService.getId().then((response) => {
              this.guests = participantService.getParticipantByUser(response).then((response2) =>{
                  console.log(this.guests);  
              });
          });*/

          const second = 1000,
        minute = second * 60,
        hour = minute * 60,
        day = hour * 24;

        //I'm adding this section so I don't have to keep updating this pen every year :-)
        //remove this if you don't need it
        let today = new Date(),
        dd = String(today.getDate()).padStart(2, "0"),
        mm = String(today.getMonth() + 1).padStart(2, "0"),
        yyyy = today.getFullYear() +1,
        nextYear = yyyy + 1,
        dayMonth = "05/05/",
        birthday = dayMonth + yyyy;
        

        today = mm + "/" + dd + "/" + yyyy;
        if (today > birthday) {
            birthday = dayMonth + nextYear;
        }
        //end

        const countDown = new Date(birthday).getTime(),
        x = setInterval(function() {

            const now = new Date().getTime(),
            distance = countDown - now;

            const daysElement = document.getElementById("days");
            if (daysElement) {
              daysElement.innerText = Math.floor(distance / (day));
            }
            const hoursElement = document.getElementById("hours");
            if (hoursElement) {
              hoursElement.innerText = Math.floor((distance % (day)) / (hour));
            }
            const minutesElement = document.getElementById("minutes");
            if (minutesElement) {
              minutesElement.innerText = Math.floor((distance % (hour)) / (minute));
            }
            const secondsElement = document.getElementById("seconds");
            if (secondsElement) {
              secondsElement.innerText = Math.floor((distance % (minute)) / second);
            }

            //do something later when date is reached
            if (distance < 0) {
            document.getElementById("headline").innerText = "It's my birthday!";
            document.getElementById("countdown").style.display = "none";
            document.getElementById("content").style.display = "block";
            clearInterval(x);
            }
            //seconds
        }, 0)
    },
        components: {
            Footer
        }
    }
</script>


<template>
  <main>
    <div class="countdown">
        <h1 id="headline">Décompte de l'anniversaire du département informatique</h1>
        <div id="countdown">
            <ul>
                <li><span id="days"></span>days</li>
                <li><span id="hours"></span>Hours</li>
                <li><span id="minutes"></span>Minutes</li>
                <li><span id="seconds"></span>Seconds</li>
            </ul>
        </div>
    </div>
    <div class="isParticipate">
        <label class="containerInput">Je participe à l'évènement
            <input id="inputEvent" type="checkbox">
            <div class="checkmark"></div>
        </label>
    </div>
    <div class="invitation"><!--Récupérere si il participe + si il a inviter quelequ'un deja ou non-->

    </div>
    <div>
        <button class="btn-custom">Enregistrer</button>
    </div>
  </main>

  <Footer/>
</template>

<style scoped>

:root {
  --smaller: .75;
}

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

html, body {
  height: 100%;
  margin: 0;
}

body {
  align-items: center;
  background-color: #ffd54f;
  display: flex;
  font-family: -apple-system, 
    BlinkMacSystemFont, 
    "Segoe UI", 
    Roboto, 
    Oxygen-Sans, 
    Ubuntu, 
    Cantarell, 
    "Helvetica Neue", 
    sans-serif;
}

.countdown{
  background-color: var(--secondary);
}

h1 {
  margin: 0!important;
  padding-top: 100px;
  font-weight: normal;
  letter-spacing: .125rem;
  text-transform: uppercase;
}

.container {
  color: #333;
  margin: 0 auto;
  text-align: center;
}

li {
  display: inline-block;
  font-size: 1.5em;
  list-style-type: none;
  padding: 1em;
  text-transform: uppercase;
}

li span {
  display: block;
  font-size: 4.5rem;
}

.emoji {
  display: none;
  padding: 1rem;
}

.emoji span {
  font-size: 4rem;
  padding: 0 .5rem;
}

@media all and (max-width: 768px) {
  h1 {
    font-size: calc(1.5rem * var(--smaller));
  }
  
  li {
    font-size: calc(1.125rem * var(--smaller));
  }
  
  li span {
    font-size: calc(3.375rem * var(--smaller));
  }
}

.btn-custom {
  display: inline-block;
  padding: 0.9rem 1.8rem;
  font-size: 16px;
  font-weight: 700;
  color: var(--bg-navbar-link) !important;
  border: 3px solid var(--primary);
  cursor: pointer;
  position: relative;
  background-color: transparent;
  text-decoration: none;
  overflow: hidden;
  z-index: 1;
  font-family: inherit;
}

.btn-custom::before {
  content: "";
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: var(--primary);
  transform: translateX(-100%);
  transition: all .3s;
  z-index: -1;
}

.btn-custom:hover::before {
  transform: translateX(0);
}

.btn-custom:hover {
  color: var(--button-color-disable) !important;
}
.isParticipate{
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 40px;
}
.containerInput input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

.containerInput {
  display: flex;
  gap: 10px;
  font-size: x-large;
}

/* Create a custom checkbox */
.checkmark {
  position: relative;
  box-shadow: var(--primary) 0px 0px 0px 2px;
  background-color: transparent;
  height: 20px;
  width: 20px;
  margin-right: 10px;
  flex-shrink: 0;
  transition: all 0.2s ease 0s;
  cursor: pointer;
  transform-origin: 0px 10px;
  border-radius: 4px;
  margin: 10px 10px 0px 0px;
  padding: 0px;
  box-sizing: border-box;
}

.containerInput input:checked ~ .checkmark {
  box-shadow: var(--primary);
  background-color: var(--primary);
  height: 20px;
  width: 20px;
  margin-right: 10px;
  flex-shrink: 0;
  transition: all 0.2s ease 0s;
  cursor: pointer;
  transform-origin: 0px 0px;
  border-radius: 4px;
  padding: 0px;
  box-sizing: border-box;
}

.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

.containerInput input:checked ~ .checkmark:after {
  display: block;
}

.containerInput .checkmark:after {
  left: 0.35em;
  top: 0.15em;
  width: 0.25em;
  height: 0.5em;
  border: solid white;
  border-width: 0 0.15em 0.15em 0;
  transform: rotate(45deg);
  transition: all 500ms ease-in-out;
}

/* END custom checkbox */
</style>