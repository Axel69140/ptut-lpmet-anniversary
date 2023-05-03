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
        <div v-if="user.length != 0" class="user">
            <img src="https://media.istockphoto.com/id/1200677760/fr/photo/verticale-de-jeune-homme-de-sourire-beau-avec-des-bras-crois%C3%A9s.jpg?s=612x612&w=0&k=20&c=0TDS1aTXZzWLzI_X9eGBhqS_QZAz49zKEDKT8xsHZfU=" width="198" height="198" />
            <div class="champ">
                <div class="labelFullName"><p>Prénom Nom</p></div>
                <div class="fullName us">
                    <div class="firstName">{{ user.firstName }}</div>   
                    <div v-if="user.isPublicProfil && user.maidenName != ''" class="maidenName">"{{ user.maidenName }}"</div>
                    <div v-if="user.isPublicProfil" class="lastName">{{ user.lastName }}</div>
                    <div v-else class="lastName">{{ obfuscateName(user.lastName) }}</div>
                </div>
                <div v-if="user.isPublicProfil" class="labelEmail"><p>Email</p></div>
                <div v-if="user.isPublicProfil" class="email us"><p>{{ user.email }}</p></div>
                <div v-if="user.isPublicProfil" class="labelPhoneNumber"><p>Téléphone</p></div>
                <div v-if="user.isPublicProfil" class="phoneNumber us"><p>{{ user.phoneNumber }}</p></div>
                <div class="labelActiveYears"><p>Année à l'IUT</p></div>
                <div v-if="user.activeYears[1]" class="activeYears us"><p><strong>{{ user.activeYears[0] }} - {{ user.activeYears[1] }}</strong></p></div>
                <div v-else class="activeYears us"><p>{{ user.activeYears[0] }}</p></div>
                <div v-if="user.isPublicProfil && user.function != ''" class="labelFunction"><p>Fonction</p></div>
                <div v-if="user.isPublicProfil && user.function != ''" class="function us"><p>{{ user.function }}</p></div>
                <div v-if="user.isPublicProfil && user.note != ''" class="labelNote"><p>Note</p></div>
                <div v-if="user.isPublicProfil && user.note != ''" class="note us"><p>{{ user.note }}</p></div>
                <div v-if="user.isPublicProfil && user.link != '' && user.link != null" class="labelLink"><p>Lien linkedin</p></div>
                <div v-if="user.isPublicProfil && user.link != '' && user.link != null" class="linkedin us"><p>{{ user.link }}</p></div>
                <div class="labelEvent"><p>Evènement</p></div>
                <div v-if="user.isParticipated" class="isParticipated us" >Participe à l'évènement</div>
                <div v-else class="isNotParticipated us">Ne participe pas à l'évènement</div>
            </div>
        </div>
    </main>
    
    <Footer class="footer" />
</template>

<style scoped>
p{
    margin: 0;
}

.champ{
    margin-left: 50px;
}

footer{
    position: inherit;
}
    .user{
        border-radius: 20px;
        margin: 50px 20%;   
        background-color: #fff;
        padding: 30px 0;
    }

    .us:nth-child(odd){
    }

    .us:nth-child(even){
    }

    .labelFullName, .labelEmail, .labelPhoneNumber, .labelActiveYears, .labelFunction, .labelNote, .labelLink, .labelEvent{
        color: var(--secondary);
        font-size: 15px;
        display: flex;
    }


    .isParticipated{
        border-bottom-left-radius: 20px;
        border-bottom-right-radius: 20px;
    }

    img{
        margin: 15px 0;
        border-radius: 100px;
    }

    .fullName,.email,.phoneNumber,.activeYears,.function,.note,.link,.isParticipated,.isNotParticipated{
        margin: 10px 20px;
        display: flex;
    }

    .firstName, .maidenName, .lastName{
        margin: 0 5px;
    }

    @media (max-width: 600px) {
        .user{
            margin: 50px 0 0 0;
        }

        .champ{
            margin-left: 10px;
        }
    }

</style>