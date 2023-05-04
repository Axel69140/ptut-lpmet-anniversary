<script>
    import axios from 'axios';   
    import Footer from '../../components/Footer.vue';    
    import { accountService } from '../../services/account.services';
    import { userService } from '../../services/user.services'; 
    import { settingService } from '../../services/setting.service';
import { ref } from 'vue';

    export default {
        name: 'profil',
        data: () => ({ 
            user: [],
            idUser: 1,
            isLoading: true,
            settings: [],
            firstName: '',
            lastName: '',
            phone: '',
            email: '',
            activeYears:'',
            activeYears2:'',
            selectedOption: '',
            maidenName: '',
            note: '',
            link: '',
            isPublicProfil: '',
        }),       
        computed: {            
        },
        methods: {
            range(start, end){
                return Array(end - start + 1).fill().map((_, index) => start + index);
            },
            save(){
                userService.editUser(this.idUser,{
                    email: this.email,
                    firstName: this.firstName,
                    lastName: this.lastName,
                    maidenName: this.maidenName,
                    phoneNumber: this.phone,
                    note: this.note,
                    isPublicProfil: this.isPublicProfil == true ? this.isPublicProfil : false,
                    activeYears: [this.activeYears, this.activeYears2],
                    function: this.selectedOption,
                    link: this.link
                }).then(async (response) => {
                    this.$router.push('../event');
                }); 
            },

        },
        async mounted() {
            this.idUser = await accountService.getId();
            await userService.getUserById(this.idUser).then(response => {
                this.user = response.data;
                this.firstName = response.data.firstName;
                this.lastName = response.data.lastName;
                this.phone = response.data.phoneNumber;
                this.email = response.data.email;
                this.selectedOption = response.data.function;
                this.maidenName = response.data.maidenName;
                this.note = response.data.note;
                this.link = response.data.link;
                this.activeYears = response.data.activeYears[0];
                this.activeYears2 = response.data.activeYears[1];
                this.isPublicProfil = response.data.isPublicProfil;
            });

            await settingService.getSettings().then(response => {
                this.settings = response.data;
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
        <h1 class="profil">Profil</h1>
        <div class="card container">
            <div class="form-row">
            <input v-model="firstName" class="form-row__input" type="text" placeholder="Prénom" @keyup.enter="submitForm"/>
            <input v-model="lastName" class="form-row__input" type="text" placeholder="Nom" @keyup.enter="submitForm"/>
            </div>

            <div class="form-row">
            <input v-model="maidenName" class="form-row__input" type="text" placeholder="Nom de jeune fille" @keyup.enter="submitForm"/>
            <input v-model="phone" class="form-row__input" type="tel" placeholder="Numéro de téléphone" @keyup.enter="submitForm"/>
            </div>

            <div class="form-row">
            <input v-model="email" class="form-row__input" type="text" placeholder="Adresse mail" @keyup.enter="submitForm"/>
            </div>

            <div class="form-row">
            <label for="activeYears">Année d'activité à l'IUT</label>
            <select v-model="activeYears" name="activeYears" id="activeYears" class="form-row__input">
                <option value="">-</option>
                <option v-for="year in range(1993, 2023)" v-bind:key="year" v-bind:value="year">{{ year }}</option>
            </select>

            <span>/</span>

            <select v-model="activeYears2" name="activeYears2" id="activeYears2" class="form-row__input">
                <option value="">-</option>
                <option v-for="year in range(1993, 2023)" v-bind:key="year" v-bind:value="year">{{ year }}</option>
            </select>
            </div>

            <div  v-if="settings.length > 0" class="form-row">
                <label class="form-row__label">Fonction</label>
                <select id="function" class="form-row__input" v-model="selectedOption" name="inputFunction">
                    <option v-for="fct in settings[0].allowedFunctions.slice(1)" :value="fct">{{ fct }}</option>
                </select>
                <input v-model="link" class="form-row__input" type="text" placeholder="Lien linkedIn"/>
            </div>      

            <div class="form-row">
                <textarea v-model="note" class="form-row__input" placeholder="Note"/>
            </div>

            <div class="form-row">
                <label for="isPublic">Je souhaite afficher publiquement mes informations</label>
                <div class="cntr">
                    <input v-model="isPublicProfil" type="checkbox" id="isPublic" class="hidden-xs-up">
                    <label for="isPublic" class="cbx"></label>
                </div>
            </div>
            <div class="form-row">
            <button @click="save()" class="button" >
                <span>Enregistrer</span>
            </button>
            </div>
        </div>

    </main>
    
    <Footer class="footer" />
</template>

<style scoped>
p{
    margin: 0;
}

.footer{
    position: inherit;
}
.profil{
    margin-top: 50px;
}
.button {
    background: var(--primary);
    color:white;
    border-radius: 8px;
    font-weight: 800;
    font-size: 15px;
    border: none;
    width: 100%;
    padding: 16px;
    transition: .4s background-color;
}
.button:hover {
  cursor:pointer;
  background: var(--primary);
}

.button--disabled {
  background: var(--button-bg-disable);
  color: var(--button-color-disable);
}

.button--disabled:hover {
  cursor:not-allowed;
  background:var(--button-bg-disable);
}
.card{
    margin-top: 50px;
}

#isParticipated:checked ~ .cbx, #isPublic:checked ~ .cbx {
  border-color: transparent;
  background: var(--primary);
  animation: jelly 0.6s ease;
}

#isParticipated:checked ~ .cbx:after, #isPublic:checked ~ .cbx:after {
  opacity: 1;
  transform: rotate(45deg) scale(1);
}


@media (max-width: 600px) {
    .form-row{
        flex-direction: column;
    }
}

</style>