<script>
    import axios from 'axios';    

    export default {
    name: 'event',
    data: () => ({ activities: [] }),       
    computed: {},      
    methods: {
      parameterGame() {
        this.$router.push('../event/registration');
      }
    },
    mounted() {   
        axios.get('https://127.0.0.1:8000/activity/api/activity').then(response => {
            console.log(response.data);
            this.activities = response.data;
        });        
        
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

            document.getElementById("days").innerText = Math.floor(distance / (day));
            document.getElementById("hours").innerText = Math.floor((distance % (day)) / (hour));
            document.getElementById("minutes").innerText = Math.floor((distance % (hour)) / (minute));
            document.getElementById("seconds").innerText = Math.floor((distance % (minute)) / second);

            //do something later when date is reached
            if (distance < 0) {
            document.getElementById("headline").innerText = "It's my birthday!";
            document.getElementById("countdown").style.display = "none";
            document.getElementById("content").style.display = "block";
            clearInterval(x);
            }
            //seconds
        }, 0)
    }
}
</script>

<template>
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
    <div class="participate">
      <div class="divParticipate">
        <p class="isParticipate" @click="parameterGame()">Je participe à l'évènement</p><!--Changer la pour récupérere si l'utilisateur est inscrit à l'évènement ou non-->
      </div>
      <div class="invitation">
          <a href="../logged/EventForm.vue">Inviter</a>
          <a href="../logged/EventForm.vue">Voir ses invités</a>
      </div>
    </div>
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

.invitation a{
  text-decoration: none;
  color: black;
  border: solid 1px #333;
  border-radius: 15px;
  padding: 0.5%;
  margin: 15px 10px;
  transition: all .2s ease-in-out; 
}

.invitation a:hover{
  color: #ffffff;
  border-color: var(--primary);
  background-color: var(--primary);
  transform: scale(1.1);
}

.isParticipate{
  border: #333 solid 1px;
  padding: 0.5%;
  border-radius: 15px;
  transition: all .2s ease-in-out; 
  cursor: pointer;
}

.isParticipate:hover{
  color: #ffffff;
  border-color: var(--primary);
  background-color: var(--primary);
  transform: scale(1.1);
}

.invitation{
  display: flex;
  justify-content: center;
}

.divParticipate{
  display: flex;
  justify-content: center;
  margin-top: 20px;
}

.container {
  color: #333;
  margin: 0 auto;
  text-align: center;
}

h1 {
  font-weight: normal;
  letter-spacing: .125rem;
  text-transform: uppercase;
  margin: 0;
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
</style>