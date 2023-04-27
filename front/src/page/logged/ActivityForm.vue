<script>
    import axios from 'axios';   
    import Footer from '../../components/Footer.vue';   
    import { accountService } from '../../services/account.services';
    import { activityService } from '../../services/activity.services';

    export default {
        name: 'activityForm',
        data: () => ({ 
            description: '',
            name: '',
            isAllDay: false,
            durationHour: 0,
            durationMinute: 0,
            totalDuration:'',
            totalStartHour: '',
            idUser: 1,
            validInput: false,

         }),       
        computed: {            
        },
        methods: {     
            async parameterActivity() {
                if (this.description !== undefined && this.description !== '' && this.name !== undefined && this.name !== '') {
                    if(this.isAllDay){
                        this.totalDuration = "23:59:00Z";
                        this.validInput = true;
                    }
                    else if(!this.durationHour == 0 || !this.durationMinute == 0){
                        if(this.durationHour<10 && this.durationMinute<10){
                            this.totalDuration = "0" + this.durationHour + ":0" + this.durationMinute + ":00Z";
                        }else if(this.durationHour<10){
                            this.totalDuration = "0" + this.durationHour + ":" + this.durationMinute + ":00Z";
                        }else if(this.durationMinute<10){
                            this.totalDuration = this.durationHour + ":0" + this.durationMinute + ":00Z";
                        }else{
                            this.totalDuration = this.durationHour + ":" + this.durationMinute + ":00Z";
                        }
                        this.validInput = true;
                    }
                    else{
                        alert("Horaire");
                    }
                    if(this.validInput){
                        await activityService.createActivity({
                            description: this.description,
                            name: this.name,
                            duration: this.totalDuration,
                            id_user: this.idUser,
                        });

                        this.description = '';
                        this.name = '';
                        alert("Merci de votre contribution. Votre activité à bien été pris en compte, après sa validation il apparaitra sur le site.");
                        this.validInput = false;
                        this.$router.push('../event');
                    }
                }
            }  
        },
        async mounted() {
            this.idUser = await accountService.getId();
        },
        components: {
            Footer
        }
    }
</script>


<template>
    <main>
        <h1>Proposer une activité</h1>
        <p class="informations"></p>
        <div class="formulaireActivite">
            <div class="divTitleTZ">
                <label class="labelTitle">Titre de l'activité *</label>
                <input type="text" class="titleTextZone" v-model="name">
            </div>
            <div class="divContentTZ">
                <label class="labelContent">Description de l'activité *</label>
                <textarea class="contentTextZone" rows="10" cols="100" v-model="description"></textarea>
            </div>
            <div class="divDurée">
                <h2>Durée de l'activité</h2>
                <div class="allDay">
                    <label>Toute la journée</label>
                    <input type="checkbox" v-model="isAllDay">
                </div>
                <div v-if="!isAllDay" class="duration">
                    <div class="hour">
                        <label>Heures</label>
                        <input type="number" name="monInput" min="0" max="60" class="form-row__input" v-model="durationHour">
                    </div>
                    <div class="minute">
                        <label>Minutes</label>
                        <input type="number" name="monInput" min="0" max="24" class="form-row__input" v-model="durationMinute">
                    </div>
                </div>                
            </div>
            
            <div class="sendButton">
                <button @click="parameterActivity()" class="btn-custom">Envoyer l'anecdote</button>
            </div>
        </div>
    </main>
    
    <Footer class="footer" />
</template>

<style scoped>

    .divDurée{
        border: solid var(--secondary) 3px;
    }
    .formulaireActivite{
        margin: 50px 0;
    }
    .labelContent{
        margin:  20px 15px 0 0;
    }

    .labelTitle{
        margin-right: 68px;
    }

    .labelImage{
        margin-right: 5%;
    }

    .formulaireActivite{
        display: flex;
        flex-direction: column;
    }

    .divImage{
        display: flex;
        justify-content: center;
    }
    .divTitleTZ{
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .divContentTZ{
        display: flex;
        justify-content: center;
    }
    .footer{
        position: inherit;
    }
    .divContentTZ{
        margin: 20px 0;
    }
    .contentTextZone, .titleTextZone{
        width: 60%;
        border-radius: 20px;
        padding: 1%;
        border: solid 4px var(--primary);
    }

    .contentTextZone:focus-visible, .titleTextZone:focus-visible{
        outline: var(--primary);
    }

    .sendButton{
        margin-top: 20px;
    }

    .duration{
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }

    .hour{
        margin-right: 5%;
    }

    @media (max-width: 560px){
        .divTitleTZ, .divContentTZ{
            flex-direction: column;
            align-items: center;
        }

        .labelTitle{
            margin: 0;
        }

        .duration{
            flex-direction: column;
        }

        .hour{
            margin: 20px 0;
        }
    }

</style>