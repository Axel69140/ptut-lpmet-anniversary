<script>
    import Footer from '../../components/Footer.vue';     
    import Loader from '../../components/Loader.vue';
    import { settingService } from '../../services/setting.service';
    import { ref } from "vue";

    export default {
        name: 'adminManagement',
        data: () => ({ settings: null, isLoading: true, maxNumberGuests: ref(''), allowedFunctions: ref(''), edit: false }),       
        computed: {
        },
        methods: {      
            getSettings() {
                settingService.getSettings().then((response) => { 
                    this.settings = response.data[0];
                    this.maxNumberGuests = this.settings.maxNumberGuests;
                    this.isLoading = false;   
                });
            },
            resetForm() {
                this.maxNumberGuests = this.settings.maxNumberGuests;
                this.edit = false;
            },
            handleChange() {
                this.edit = true;
            }
        },
        mounted() {  
            this.getSettings();                   
        },
        components: {
            Loader,
            Footer
        }
    }
</script>


<template>
    <Loader :is-loading="isLoading" class="loader-basique"/>

    <main v-if="!isLoading">
        <h1>Gestion générale</h1>
        
        <form class="container">
            <!-- Nombre d'invité autorisé -->
            <div class="form-row">
                <label>Nombre d'invité autorisé</label>
                <input v-model="maxNumberGuests" class="form-row__input" type="number" v-on:input="handleChange"/>
            </div>

            <!-- Fonctions ajoutés allowedFunctions -->
            <div class="form-row">
                <label>Fonctions sélectionnables</label>
                <input v-model="allowedFunctions" class="form-row__input" type="text" placeholder="Nom de la fonction"/>

                <div id="allFunctions">
                    <p v-for="function_ in settings.allowedFunctions" v-bind:key="function_" v-bind:value="function_">{{ function_ }}</p>
                </div>
            </div>

            <!-- Range des select année -->

            <div class="d-flex justify-content-end">
                <div class="d-flex justify-content-between w-50">
                    <button class="btn-custom btn-alert" v-bind:disabled="!edit" @click="resetForm()">Annuler les changements</button>
                    <button type="submit" class="btn-custom btn-valid">Enregistrer</button>   
                    <button type="submit" class="btn-custom btn-neutre">Reset les paramètres généraux</button>               
                </div>
            </div>
        </form>
    </main>
    
    <Footer/>
</template>

<style scoped>
#allFunctions {
    background-color: var(--bg-primary);
    width: 33%;
}

</style>