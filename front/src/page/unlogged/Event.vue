<script>
  import axios from 'axios';
  import Footer from '../../components/Footer.vue';
  import { activityService } from '../../services/activity.services';
  import { accountService } from '../../services/account.services';
  import { userService } from '../../services/user.services';

  export default {
    name: 'event',
    data() {
      return {
        activities: [],
        user: {},
      };
    },
    computed: {},
    methods: {
      parameterGame() {
        this.$router.push('../event/registration');
      },
    },
    
    async mounted() {
      activityService.getActivities().then((response) => {
        console.log(response.data);
        this.activities = response.data;
      });

      const idUser = await accountService.getId();
      this.user = await userService.getUserById(idUser);
      console.log(this.user);

      const second = 1000;
      const minute = second * 60;
      const hour = minute * 60;
      const day = hour * 24;

      // I'm adding this section so I don't have to keep updating this pen every year :-)
      // remove this if you don't need it
      let today = new Date();
      const dd = String(today.getDate()).padStart(2, '0');
      const mm = String(today.getMonth() + 1).padStart(2, '0');
      const yyyy = today.getFullYear() + 1;
      const nextYear = yyyy + 1;
      const dayMonth = '05/05/';
      let birthday = dayMonth + yyyy;

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
    <div class="participate">
      <div class="divParticipate" v-if="user && user.data && !user.data.isParticipated">
          <p class="btn-custom" @click="parameterGame()">Je participe à l'évènement</p>
      </div>

      <div class="invitation" v-else>
          <a class="btn-custom" @click="">Inviter</a>
          <a class="btn-custom" @click="parameterGame()">Voir ses invités</a>
      </div>
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
  padding-bottom: 20px ;
}

.invitation a{
  text-decoration: none;
  margin: 0 20px;
}


.isParticipate{
  color: #ffffff;
  border: solid 3px #ffffff;
  background-color: var(--secondary);
  padding: 0.5%;
  border-radius: 15px;
  transition: all .2s ease-in-out; 
  cursor: pointer;
}

h1 {
  margin: 0!important;
  padding-top: 100px;
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