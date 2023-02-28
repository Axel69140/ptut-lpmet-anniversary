<script>
    import axios from 'axios';    

    export default {
        name: 'home',
        data: () => ({ users: [] }),       
        computed: {            
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
    <h1>Home</h1>
    <ul >
        <li v-for="user in users" :key="user.id" class="user">
            <img src="https://media.istockphoto.com/id/1200677760/fr/photo/verticale-de-jeune-homme-de-sourire-beau-avec-des-bras-crois%C3%A9s.jpg?s=612x612&w=0&k=20&c=0TDS1aTXZzWLzI_X9eGBhqS_QZAz49zKEDKT8xsHZfU=" width="100" height="100">
            <div class="firstName">{{ user.firstName }}</div>
            <div v-if="user.isPublic" class="lastName">{{ user.lastName }}</div>
            <div v-else>{{ obfuscateName(user.lastName) }}</div>
            <div class="isParticipated">{{ user.isParticipated }}</div>
            <div v-if="user.isPublic" class="activeYears">{{ user.activeYears }}</div>
        </li>
    </ul>
</template>

<style scoped>
    li{
        list-style: none;
    }

    .user{
        border: solid black 1px;
    }
</style>