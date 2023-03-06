<script>
    import axios from 'axios';    

    export default {
        name: 'eventForm',
        data: () => ({ users: [] }),       
        computed: {            
        },
        methods: {      
        },
        mounted() {       
        }
    }
</script>


<template>
    <h1>Participer à l'évènement</h1>
    <div class="isParticipate">
        <label class="containerInput">Je participe à l'évènement
            <input id="inputEvent" type="checkbox">
            <div class="checkmark"></div>
        </label>
        
    </div>
    <div class="invitation"><!--Récupérere si il participe + si il a inviter quelequ'un deja ou non-->

    </div>
    <div>
        <button class="save">Enregistrer</button>
    </div>
</template>

<style scoped>
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
}

/* Create a custom checkbox */
.checkmark {
  position: relative;
  box-shadow: var(--primary) 0px 0px 0px 2px;
  background-color: var(--bg-secondary);
  height: 20px;
  width: 20px;
  margin-right: 10px;
  flex-shrink: 0;
  transition: all 0.2s ease 0s;
  cursor: pointer;
  transform-origin: 0px 10px;
  border-radius: 4px;
  margin: 2px 10px 0px 0px;
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
  transform-origin: 0px 10px;
  border-radius: 4px;
  margin: 2px 10px 0px 0px;
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
  left: 0.45em;
  top: 0.25em;
  width: 0.25em;
  height: 0.5em;
  border: solid white;
  border-width: 0 0.15em 0.15em 0;
  transform: rotate(45deg);
  transition: all 500ms ease-in-out;
}

/* END custom checkbox */
</style>