<script>
  import axios from 'axios';
  import Footer from '../../components/Footer.vue';
  import { activityService } from '../../services/activity.services';
  import { accountService } from '../../services/account.services';
  import { userService } from '../../services/user.services';
  import Loader from '../../components/Loader.vue';

  export default {
    name: 'event',
    data() {
      return {
        activities: [],
        user: {},
        isLoading: true
      };
    },
    computed: {},
    methods: {
      parameterGame() {
        this.$router.push('../event/registration');
      },
    },
    
    async mounted() {      
      await activityService.getActivities().then((response) => {
        this.activities = response.data;
        console.log(this.activities);
      });      

      const idUser = await accountService.getId();
      this.user = await userService.getUserById(idUser);

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

      this.isLoading = false;            
    },
    components: {
        Footer,
        Loader
    }
}
</script>

<template>
  <Loader :isLoading="isLoading" class="loader-basique" />
  <main v-if="!isLoading">   
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
    <div class="planning" v-if="activities">
      <h3 class="titleActivity">Les activités présentent à l'évènement</h3>
      <ul class="listeActivities">
        <li v-for="activity in activities">
          <div v-if="!activity.isValidate">
            <h3>{{ activity.name }}</h3>
            <p class="descriptionActivity">{{ activity.description }}</p>
          </div>
        </li>
      </ul>
    </div>
  </main>
  <Footer class="footer" />
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

.listeActivities{
  display: flex;
  flex-direction: column;
}
.footer{
  position: inherit;
}

.participate{
  margin-top: 40px;
}
.titleActivity{
  margin: 40px 0;
}
.countdown{
  background-color: var(--secondary);
  padding-bottom: 20px ;
}

.invitation a{
  text-decoration: none;
  margin: 0 5rem;
  font-size: large;
  padding: 1rem;
}
.listeActivities li{
  text-transform: none;
}
h1 {
  margin: 0!important;
  padding-top: 100px;
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