<script>
    import axios from 'axios';    

    export default {
        name: 'home',
        data: () => ({ 
            selectedOption: 'tous',
            eventOption: false,
            minNumber: 2000,
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
                console.log(this.minNumber);
                console.log(this.maxNumber);
                if(this.minNumber < this.maxNumber){
                    filtered = filtered.filter(user => (user.activeYears.substring(0,4) >= this.minNumber && user.activeYears.substring(0,4) <= this.maxNumber)||(user.activeYears.substring(user.activeYears.length - 4) >= this.minNumber && user.activeYears.substring(user.activeYears.length - 4) <= this.maxNumber));
                }else if(this.minNumber > this.maxNumber){
                    this.minNumber = 2000;
                    this.maxNumber = 2023;
                }else{
                    console.log("testeeeeeeee" + this.minNumber);
                    filtered = filtered.filter(user => (user.activeYears.substring(0,4) <= this.minNumber && user.activeYears.substring(user.activeYears.length - 4) >= this.minNumber));
                }
            }

            return filtered;
        }        
        },
        methods: {      
            // Returns the first letter of a string and replaces the other characters with asterisks
            obfuscateName(name) {
                return name.charAt(0) + '*'.repeat(name.length - 1);
            }
        },
        mounted() {    
            axios.get('https://127.0.0.1:8000/api/user').then(response => {
                console.log(response.data);
                this.users = response.data;
            });        
        }
    }
</script>


<template>
    <h1>Users</h1>
    <div class="filters">
        <div class="filterFunction">
            <label>Participe à l'évènement</label>
            <input id="inputEvent" type="checkbox" v-model="eventOption">
        </div>
        <div class="filterFunction">
            <label>fonction au sein de l'IUT</label>
            <select id="inpuFunction" v-model="selectedOption" name="inputFunction">
                <option value="tous">tous</option>
                <option value="élève">élève</option>
                <option value="enseignant">enseignant</option>
                <option value="autre">autre</option>
            </select>
        </div>
        <div class="filterYears">
            <label for="inputYear1">Année au sein de l'IUT de</label>
            <input id="monInput" type="number" name="monInput" min="2000" max="2023" v-model="minNumber">
            <label for="inputYear1">à</label>
            <input id="monInput" type="number" name="monInput" min="2000" max="2023" v-model="maxNumber">
        </div>
    </div>
    <div class="users">
        <ul class="users">
            <li v-for="user in filteredUsers" :key="user.id" class="user" onclick="">
                <img src="https://media.istockphoto.com/id/1200677760/fr/photo/verticale-de-jeune-homme-de-sourire-beau-avec-des-bras-crois%C3%A9s.jpg?s=612x612&w=0&k=20&c=0TDS1aTXZzWLzI_X9eGBhqS_QZAz49zKEDKT8xsHZfU=" width="100" height="100">
                <div class="fullName">
                    <div class="firstName">{{ user.firstName }}</div>
                    <div v-if="user.isPublic" class="lastName">{{ user.lastName }}</div>
                    <div v-else class="lastName">{{ obfuscateName(user.lastName) }}</div>
                </div>
                <div class="activeYears">{{ user.activeYears }}</div>
                <div v-if="user.isPublic" class="function">{{ user.function }}</div>
                <div v-if="user.isParticipated" class="isParticipated" >participe</div>
                <div v-else class="isNotParticipated">participe pas</div>
                
            </li>
        </ul>
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

</style>