<script>
    import axios from 'axios';  
    import Footer from '../../components/Footer.vue';     
    import { userService } from '../../services/user.services';
    import { accountService } from '../../services/account.services';
    import { participantService } from '../../services/participant.services';

    export default {
        name: 'eventForm',
        data: () => ({ users: [] }),       
        computed: {            
        },
        methods: {  

        },
        mounted() {       
        },
        components: {
            Footer
        }
    }
</script>


<template>
  <main>
    <h1>Participer à l'évènement</h1>
    <div class="isParticipate">
        <label for="inputEvent">Je participe à l'évènement</label>
        <div class="cntr">
            <input type="checkbox" id="inputEvent" class="hidden-xs-up">
            <label for="inputEvent" class="cbx"></label>
        </div>        
    </div>
    <div class="invitation"><!--Récupérere si il participe + si il a inviter quelequ'un deja ou non-->

    </div>
    <div>
        <button class="save">Enregistrer</button>
    </div>
  </main>

  <Footer/>
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

.save:hover{
    transform: scale(1.1);
}
.isParticipate{
    display: flex;
    justify-content: center;
    align-items: center;
}
</style>