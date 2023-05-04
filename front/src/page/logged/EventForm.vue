<script>
    
import axios from 'axios';  
import Footer from '../../components/Footer.vue';     
import { userService } from '../../services/user.services';
import { accountService } from '../../services/account.services';
import { guestService } from '../../services/guest.service';
import { settingService } from '../../services/setting.service';
import Loader from '../../components/Loader.vue';

export default {
  name: 'eventForm',
  data: () => ({ 
    guests: {},
    user:[],
    settings:[],
    isInputChecked: false,
    isLoading: true
  }),       
  computed: {            
  },
  methods: { 
    addGuest(){
      this.save(false);
      this.$router.push('../event/invitation');
    },
    async save(btnSave){
      const idUser = await accountService.getId();
      userService.editUser(
        idUser,
        {
          isParticipated: this.isInputChecked,
        }
      );
      if(btnSave){
        this.$router.push('../event');
      }
    },
    deleteGuest(guestId){
      guestService.deleteGuest(guestId);
      this.$router.push('../event');
    }
  },
  async mounted() {
    const idUser = await accountService.getId();
    this.user = await userService.getUserById(idUser);
    const guests = await userService.getGuestsByUser(idUser);
    this.settings = await settingService.getSettings();
    if(this.user.data.isParticipated){
      this.isInputChecked = true;
    }
    this.guests = guests;
    
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
    <form @submit.prevent="submitForm">
      <div class="isParticipate">
          <label class="participate" for="inputEvent">Je participe à l'évènement</label>
          <div class="cntr">
              <input type="checkbox" id="inputEvent" class="hidden-xs-up" v-model="isInputChecked" @change="onInputChange">
              <label for="inputEvent" class="cbx"></label>
          </div> 
      </div>
      <div class="invitations">
        <div class="invitation" v-for="guest in guests.data">
          <div class="guest">
            <div class="name">
              <p>Prénom : </p>
              <p>{{ guest.lastName }}</p>
            </div>
            <div class="name">
              <p>Nom : </p>
              <p>{{ guest.firstName }}</p>
            </div>
            <div class="email">
              <p>Email : </p>
              <p>{{ guest.email }}</p>
            </div>
            <div class="info" >
              <p class="deleteGuest" v-bind:class="guest.id" v-on:click="deleteGuest(guest.id)">Supprimer</p>
            </div>
          </div>
          
        </div>
        <div v-if="guests.data.length < settings.data[0].allowedFunctions.length && isInputChecked" class="invitation" id="addGuest" @click="addGuest()"><!-- Settings à la place du 1 -->
          <p class="infoAddGuets">Invité une personne à l'évènement</p>
          <p class="iconAdd">+</p>
        </div>
      </div>
      
      <div>
          <button class="btn-custom" @click="save(true)">Enregistrer</button>
      </div>
    </form>    
  </main>

  <Footer class="footer" />
</template>

<style scoped>
#inputEvent:checked ~ .cbx {
  border-color: transparent;
  background: var(--primary);
  animation: jelly 0.6s ease;
}

#inputEvent:checked ~ .cbx:after {
  opacity: 1;
  transform: rotate(45deg) scale(1);
}

.save{
    border-radius: 10px;
    border: solid 1px black;
    color: white;
    background-color: #1f8d15;
    padding: 0.5%;
    transition: all .2s ease-in-out; 
}
.invitation{
  width: 40%;
  border: solid 2px var(--primary);
  border-radius: 15px;
  background-color: #fff;
}
.info, .name, .email{
  display: flex;
}

.info{
  cursor: pointer;
  margin-top: 10px;
  justify-content: center;
  color: red;
}
.footer{
  position: inherit;
}

#addGuest{
  cursor: pointer;
}

.guest{
  padding: 5%;
}
.infoAddGuets{
  font-size: larger;
}

.iconAdd{
  font-size: xx-large;
}

.invitations{
  display: flex;
  justify-content: space-around;
  margin: 30px 1% 30px 1%;
}

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

.isParticipate{
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 40px;
}
.participate{
  text-transform: uppercase;
  margin-right: 1rem;
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

@media (max-width: 690px){
  #addGuest{
    width: 50%;
  }

  .invitations{
    flex-direction: column;
    align-items: center;
  }

  .invitation{
    margin: 20px 0;
    width: 70%;
  }
}


</style>