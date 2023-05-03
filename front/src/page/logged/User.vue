<script>
    import axios from 'axios';   
    import Footer from '../../components/Footer.vue';    
    import { userService } from '../../services/user.services'; 

    export default {
        name: 'user',
        data: () => ({ 
            user: [],
            isLoading: true,
        }),       
        computed: {            
        },
        methods: {     
            obfuscateName(name) {
                return name.charAt(0) + '*'.repeat(name.length - 1);
            },
        },
        mounted() {
            console.log("test");  
            const url = window.location.pathname;
            const match = url.match(/\/users\/(\d+)/);
            const userId = match && match[1];
            console.log("test");
            userService.getUserById(userId).then(response => {
                this.user = response.data;
                this.isLoading = false;
                console.log(response.data);
            });
        },
        components: {
            Footer
        }
    }
</script>


<template>
    <main>
        <li v-if="user" class="user">
            <img src="https://media.istockphoto.com/id/1200677760/fr/photo/verticale-de-jeune-homme-de-sourire-beau-avec-des-bras-crois%C3%A9s.jpg?s=612x612&w=0&k=20&c=0TDS1aTXZzWLzI_X9eGBhqS_QZAz49zKEDKT8xsHZfU=" width="198" height="198">
            <div class="fullName">
                <div class="firstName">{{ user.firstName }}</div>   
                <div v-if="user.isPublicProfil && user.maidenName != ''">{{ user.maidenName }}</div>
                <div v-if="user.isPublicProfil" class="lastName">{{ user.lastName }}</div>
                <div v-else class="lastName">{{ obfuscateName(user.lastName) }}</div>
            </div>
            <div v-if="user.isPublicProfil" class="email">{{ user.email }}</div>
            <div v-if="user.isPublicProfil" class="phoneNumber">{{ user.phoneNumber }}</div>
            <div v-if="user.activeYears[1]" class="activeYears">Année à l'IUT <strong>{{ user.activeYears[0] }} - {{ user.activeYears[1] }}</strong></div>
            <div v-else class="activeYears">Année à l'IUT <strong>{{ user.activeYears[0] }}</strong></div>
            <div v-if="user.isPublicProfil && user.function != 'autre'" class="function">En tant qu'{{ user.function }}</div>
            <div v-if="user.isPublicProfilProfil" class="note">{{ user.note }}</div>
            <div v-if="user.isParticipated" class="isParticipated" >participe</div>
            <div v-else class="isNotParticipated">participe pas</div>
        </li>
    </main>
    
    <Footer/>
</template>

<style scoped>

</style>