<script>
    import axios from 'axios';    

    export default {
        name: 'home',
        data: () => ({ users: [] }),       
        computed: {            
        },
        methods: {      
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
    <ul>
        <li v-for="user in users" :key="user.id">
            {{ user.firstName }}
        </li>
    </ul>
</template>

<style scoped>

</style>