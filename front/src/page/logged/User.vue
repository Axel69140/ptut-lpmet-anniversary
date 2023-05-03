<script>
    import axios from 'axios';   
    import Footer from '../../components/Footer.vue';    
    import { userService } from '../../services/user.services'; 

    export default {
        name: 'user',
        data: () => ({ 
            users: [],
            isLoading: true 
        }),       
        computed: {            
        },
        methods: {      
        },
        mounted() {        
            userService.getUserById(1).then(response => {
                this.users = response.data;
                this.isLoading = false;
            });
        },
        components: {
            Footer
        }
    }
</script>


<template>
    <main>
        <h1>Utilisateur</h1>
    </main>
    
    <Footer/>
</template>

<style scoped>

</style>