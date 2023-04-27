<script>
    import Footer from '../../components/Footer.vue';     
    import Loader from '../../components/Loader.vue';
    import { settingService } from '../../services/setting.service';
    import { ref } from "vue";

    export default {
        name: 'adminManagement',
        data: () => ({ settings: null, isLoading: true, maxNumberGuests: ref(''), newFunction: ref(''), allowedFunctions: [] ,edit: false }),       
        computed: {
        },
        methods: {      
            getSettings() {
                settingService.getSettings().then((response) => { 
                    this.settings = response.data[0];
                    this.maxNumberGuests = this.settings.maxNumberGuests;
                    this.allowedFunctions = [...this.settings.allowedFunctions];
                    this.isLoading = false;   
                });
            },
            resetForm() {
                this.maxNumberGuests = this.settings.maxNumberGuests;
                this.allowedFunctions = [...this.settings.allowedFunctions];
                this.newFunction = "";
                this.edit = false;
            },
            handleChange() {
                this.edit = true;
            },
            addFunction() {
                this.allowedFunctions.push(this.newFunction);
                this.newFunction = "";
            },
            submit() {
                this.isLoading = true;
                settingService.editSettings({
                    allowedFunctions: this.allowedFunctions,
                    maxNumberGuests: this.maxNumberGuests
                }).then(() => {
                    this.getSettings();
                });
            },
            reset() {
                this.isLoading = true;
                settingService.resetSettings().then(() => {
                    this.getSettings();
                })
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
        
        <form class="container w-75">
            <!-- Nombre d'invité autorisé -->
            <div class="form-row form-line">
                <label>Nombre d'invité autorisé</label>
                <input v-model="maxNumberGuests" class="form-row__input num-input" type="number" v-on:input="handleChange"/>
            </div>

            <!-- Fonctions ajoutés allowedFunctions -->
            <div class="form-row form-line">
                <label>Fonctions sélectionnables</label>

                <div id="newFunction">
                    <input v-model="newFunction" class="form-row__input function-input" type="text" placeholder="Nom de la fonction" v-on:input="handleChange"/>
                    

                    <button type="button" class="btn-custom btn-neutre btn-add" @click="addFunction()">Ajouter</button> 

                    <div id="allFunctions">
                        <p v-for="function_ in allowedFunctions" v-bind:key="function_" v-bind:value="function_">{{ function_ }}</p>
                    </div>  
                </div>
            </div>

            <!-- Range des select année -->

            <div class="d-flex justify-content-end">
                <div class="d-flex justify-content-between w-50">
                    <button type="button" class="btn-custom btn-alert marge-button btn-custom-bis" v-bind:disabled="!edit" @click="resetForm()">Annuler les changements</button>
                    <button type="button" class="btn-custom btn-valid marge-button btn-custom-bis" @click="submit()">Enregistrer</button>   
                    <button type="button" class="btn-custom btn-neutre btn-custom-bis" @click="reset()">Reset les paramètres généraux</button>               
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

.form-line {
    display: flex;
    flex-direction: column;
    align-content: flex-start;
}

.form-row label {
    text-align: initial;
}

.num-input {
    width: 100%;
}

.function-input {    
    max-width: 50%;
    height: 40px;
}

#newFunction {
    display: flex;
    justify-content: space-between;
    width:100%;
}

.btn-add {
    height: 70px;
    width: 15%;
}

.marge-button {
    margin-right: 30px;
}

.btn-custom-bis {
    width: 75%;
}
</style>