<script>
    import axios from 'axios';    
    import _ from 'lodash';
    import { chunk } from 'lodash';

    export default {
        name: 'home',
        data: () => ({ 
            selectedOption: 'tous',
            eventOption: false,
            minNumber: 1993,
            maxNumber: 2023,
            users: []
         }),       
        computed: {  
            filteredUsers: function() {
                let filtered = this.users;

                if (this.selectedOption !== 'tous') {
                    filtered = filtered.filter(user => user.function === this.selectedOption);
                }

                if (this.eventOption) {
                    filtered = filtered.filter(user => user.isParticipated);
                }

                if (this.minNumber && this.maxNumber) {
                    if(this.minNumber < this.maxNumber){
                        filtered = filtered.filter(user => (user.activeYears[0] >= this.minNumber && user.activeYears[0] <= this.maxNumber)||(user.activeYears[1] >= this.minNumber && user.activeYears[1] <= this.maxNumber)||(user.activeYears[0] < this.minNumber && user.activeYears[1] > this.maxNumber));
                    }else if(this.minNumber > this.maxNumber){
                        this.minNumber = 1993;
                        this.maxNumber = 2023;
                    }else{
                        filtered = filtered.filter(user => (user.activeYears[0] <= this.minNumber && user.activeYears[1] >= this.minNumber));
                    }
                }

                return filtered;
            }
        },
        methods: {      
            // Returns the first letter of a string and replaces the other characters with asterisks
            obfuscateName(name) {
                return name.charAt(0) + '*'.repeat(name.length - 1);
            },

            test(array,nbr) {
                return _.chunk(array,nbr);
            }
        },
        mounted() {    
            axios.get('https://127.0.0.1:8000/users').then(response => {
                console.log(response.data);
                this.users = response.data;
            });        
        }
    }
    
</script>


<template>
    
    <h1>Users</h1>
    <div class="fullPage">
        <div class="filters">
            <div class="filterFunction">
                <label class="container">Participe à l'évènement
                    <input id="inputEvent" type="checkbox" v-model="eventOption">
                    <div class="checkmark"></div>
                </label>
                
            </div>
            <div class="filterFunction">
                <label>fonction au sein de l'IUT</label>
                <select id="inputFunction" v-model="selectedOption" name="inputFunction">
                    <option value="tous">tous</option>
                    <option value="élève">élève</option>
                    <option value="enseignant">enseignant</option>
                    <option value="autre">autre</option>
                </select>
            </div>
            <div class="filterYear form-row">
                <label for="inputYear1">Année au sein de l'IUT de</label>
                <input id="monInput" type="number" name="monInput" min="1993" max="2023" class="form-row__input" v-model="minNumber">
                <label for="inputYear1">à</label>
                <input id="monInput" type="number" name="monInput" min="1993" max="2023" class="form-row__input" v-model="maxNumber">
            </div>
        </div>
        <div class="users">
            <ul v-for="(chunk, index) in test(filteredUsers, 6)" :key="index" class="usersPart">
                <li v-for="(user, i) in chunk" :key="i" class="user">
                    <img src="https://media.istockphoto.com/id/1200677760/fr/photo/verticale-de-jeune-homme-de-sourire-beau-avec-des-bras-crois%C3%A9s.jpg?s=612x612&w=0&k=20&c=0TDS1aTXZzWLzI_X9eGBhqS_QZAz49zKEDKT8xsHZfU=" width="198" height="198">
                    <div class="fullName">
                        <div class="firstName">{{ user.firstName }}</div>   
                        <div v-if="user.isPublic" class="lastName">{{ user.lastName }}</div>
                        <div v-else class="lastName">{{ obfuscateName(user.lastName) }}</div>
                    </div>
                    <div class="activeYears">Année à l'IUT <strong>{{ user.activeYears[0] }} - {{ user.activeYears[1] }}</strong></div>
                    <div v-if="user.isPublic && user.function != 'autre'" class="function">En tant qu'{{ user.function }}</div>
                    <div v-if="user.isParticipated" class="isParticipated" >participe</div>
                    <div v-else class="isNotParticipated">participe pas</div>
                </li>
            </ul>
        </div>
    </div>
    
</template>

<style scoped>
    li{
        list-style: none;
        margin: 5px;
    }

    ul.users{
        display: flex;
    }

    .user{
        border: solid black 1px;
    }

    .isParticipated{
        color: green;
    }

    .isNotParticipated{
        color: red;
    }

    .fullName {
        display: flex;
        margin: 20px 5px 0 5px;
    }

    .firstName{
        margin-right:7px;
    }

.fullPage{
    margin: 0 5%;
}
li.user{
    list-style: none;
    margin: 5px;
    max-width: 200px;
    min-width: 200px;
    background-color: #fcfcfc;
    border: solid var(--third) 1px;
    box-shadow: 0 15px 10px 0 #d4d4d4;
    transition: all .2s ease-in-out; 
}

ul.usersPart{
    display: flex;
    justify-content:space-evenly;
}

.user:hover{
    transform: scale(1.1);
}

.isParticipated{
    color: green;
    margin-bottom: 10px;
}

.isNotParticipated{
    color: red;
    margin-bottom: 10px;
}

label{
    margin: 0 10px 0 0;
}

.fullName {
    display: flex;
    justify-content: center;
    margin: 20px 0 0 0;
}

.firstName{
    margin-right:7px;
}

.filters{
    display: flex;
    justify-content: space-evenly;
    align-items: center;
}

.filterYear{
    display: flex;
}

.container input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

.container {
  display: flex;
  gap: 10px;
}

#inputFunction{
    padding: 8px;
    border: solid 2px var(--primary);
    border-radius: 8px;
    background: var(--input-bg);
    font-weight: 500;
    font-size: 16px;
    flex: 1;
    min-width: 100px;
    color: black;
}

#inputFunction:focus-visible{
    outline: var(--primary);
}

#inputFunction option{
    background: var(--input-bg);
}


/* Create custom label */
.form-row {
  display: flex;
  align-items: center;
  margin: 16px 0px;
  gap:16px;
  flex-wrap: wrap;
}

.form-row__input {
  padding:8px;
  border: solid 2px var(--primary);
  border-radius: 8px;
  background: var(--input-bg);
  font-weight: 500;
  font-size: 16px;
  flex:1;
  min-width: 100px;
  color: black;
}

.form-row__input:focus-visible{
    outline: 1px solid var(--primary);
}

.form-row__input::placeholder {
  color: var(--input-placeholder);
}
/* END custom label */ 

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

.container input:checked ~ .checkmark {
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

.container input:checked ~ .checkmark:after {
  display: block;
}

.container .checkmark:after {
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