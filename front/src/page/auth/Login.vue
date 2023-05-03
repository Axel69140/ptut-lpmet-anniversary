<script>
  import { mapState } from 'vuex';
  import Footer from '../../components/Footer.vue';
  import { accountService } from '../../services/account.services'; 
  import { settingService } from '../../services/setting.service';

  export default {
    name: 'login',
    data: () => ({ mode: 'login', loading: false, email: '', firstName: '', lastName: '', maidenName: '', password: '', password_confirmation: '', phone: '', 
                  activeYears: '', activeYears2: '',_function: '', link: '', note: '', isParticipated: '', isPublic: '', showMessage: false, 
                  invalidMail: false, invalidPassword: false, notSimilarPassword: false, alreadyUseMail: false, invalidYears: false, settings: [],}),       
    mounted() {
      if (accountService.getToken()) {
        this.$router.push('/');
        return ;
      }
      if (this.$route.query.isConnected) {
        this.showMessage = true;
      }
      settingService.getSettings().then(response => {
            this.settings = response.data;
      });
    },
    computed: {
      validatedFields: function () {
        if (this.mode == 'create') {
          if (this.email != "" && this.firstName != "" && this.lastName != "" && this.password != "" && this.password_confirmation != "" 
          && this.activeYears != "" && this.activeYears2 != "") {
            return true;
          } else {
            return false;
          }
        } else {
          if (this.email != "" && this.password != "") {
            return true;
          } else {
            return false;
          }
        }
      },
      ...mapState(['status'])
    },
    methods: {
      submitForm() {
        if (this.mode == 'login') {
          this.login();
        } else {
          this.createAccount();
        }
      },
      range(start, end) {
        return Array(end - start + 1).fill().map((_, index) => start + index);
      },
      switchToCreateAccount() {
        this.mode = 'create';
      },
      switchToLogin() {
        this.mode = 'login';
      },      
      login() {
        this.loading = true;
        const self = this;
        this.$store.dispatch('login', {
          email: this.email,
          password: this.password,
        }).then(function () {
          self.$router.push('/');          
          this.loading = false;
        }, function (error) {
        })
      },
      createAccount() {
        this.loading = true;
        this.validateEmail();
        this.validatePassword();
        this.validateYears();

        if (!this.invalidMail && !this.invalidPassword && !this.notSimilarPassword && !this.invalidYears) {
          const self = this;
          this.$store.dispatch('createAccount', {
            email: this.email,
            lastName: this.lastName,
            firstName: this.firstName,
            password: this.password,
            roles: ["ROLE_USER"],
            maidenName: this.maidenName,
            phoneNumber: this.phone,
            note: this.note,
            isParticipated: this.isParticipated === true ? this.isParticipated : false,
            isPublicProfil: this.isPublic === true ? this.isPublic : false,
            activeYears: [this.activeYears, this.activeYears2],
            function: this._function,
            link: this.link
          }).then(function () {
            self.alreadyUseMail = false;
            self.login();
          }, function (error) {
            if(error) {
              self.alreadyUseMail = true;
              this.loading = false;
            }
          })
        } else {
          this.loading = false;
        }
      },
      validateEmail: function() {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (this.email && !emailRegex.test(this.email)) {
          this.invalidMail = true;
        } else {
          this.invalidMail = false;
        }
      },
      validatePassword: function() {
        const regex = /^(?=.*[A-Z])(?=.*\d).{8,}$/;
        if (this.password === this.password_confirmation) {
          this.notSimilarPassword = false;
          if (!regex.test(this.password)) {
            this.invalidPassword = true    
          } else {
            this.invalidPassword = false;
          }          
        } else {
          this.notSimilarPassword = true;
        }
      }, 
      validateYears: function() {
        if (this.activeYears > this.activeYears2) {
          this.invalidYears = true;
        } else {
          this.invalidYears = false;
        }
      }
    },
    components: {
      Footer
    }
  }
</script>

<template>
  <main>
    <div class="card container">
      <h1 class="card__title" v-if="mode == 'login'">Connexion</h1>
      <h1 class="card__title" v-else>Inscription</h1>
      <p class="card__subtitle" v-if="mode == 'login'">Tu n'as pas encore de compte ? <span class="card__action" @click="switchToCreateAccount()">Créer un compte</span></p>
      <p class="card__subtitle" v-else>Tu as déjà un compte ? <span class="card__action" @click="switchToLogin()">Se connecter</span></p>

      <!-- Message d'alerte connexion obligatoire -->
      <div class="alert alert-secondary" role="alert" v-if="showMessage">
        Vous devez être connecté pour accédez à cette page.
      </div>

      <!-- Message d'erreur -->
      <div class="alert alert-danger" role="alert" v-if="mode == 'create' && invalidMail">
          Adresse mail invalide
      </div>

      <div class="alert alert-danger" role="alert" v-if="mode == 'create' && alreadyUseMail">
          Adresse mail déjà utilisée
      </div>

      <div class="alert alert-danger" role="alert" v-if="mode == 'create' && invalidPassword">
          Le mot de passe doit contenir au moins 8 caractères, une majuscule et un caractère spécial.
      </div>

      <div class="alert alert-danger" role="alert" v-if="mode == 'create' && notSimilarPassword">
          Mots de passe différents
      </div>

      <div class="alert alert-danger" role="alert" v-if="mode == 'create' && invalidYears">
          Année d'activité à l'IUT invalide
      </div>  

      <!-- Input form -->
        <div class="form-row" v-if="mode == 'create'">
          <input v-model="firstName" class="form-row__input" type="text" placeholder="Prénom*" @keyup.enter="submitForm"/>
          <input v-model="lastName" class="form-row__input" type="text" placeholder="Nom*" @keyup.enter="submitForm"/>
        </div>

        <div class="form-row" v-if="mode == 'create'">
          <input v-model="maidenName" class="form-row__input" type="text" placeholder="Nom de jeune fille" @keyup.enter="submitForm"/>
          <input v-model="phone" class="form-row__input" type="tel" placeholder="Numéro de téléphone" @keyup.enter="submitForm"/>
        </div>

        <div class="form-row">
          <input v-model="email" class="form-row__input" type="text" placeholder="Adresse mail*" @keyup.enter="submitForm"/>
        </div>

        <div class="form-row">
          <input v-model="password" class="form-row__input" type="password" placeholder="Mot de passe*" @keyup.enter="submitForm"/>
        </div>

        <div class="form-row" v-if="mode == 'create'">
          <input v-model="password_confirmation" class="form-row__input" type="password" placeholder="Confirmer mot de passe*" @keyup.enter="submitForm"/>
        </div>

        <div class="form-row" v-if="mode == 'create'">
          <label for="activeYears">Année d'activité à l'IUT*</label>
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

        <div class="form-row" v-if="mode == 'create'">
          <label class="form-row__label">Fonction</label>
          <select id="function" class="form-row__input" v-model="selectedOption" name="inputFunction">
            <option v-for="fct in settings[0].allowedFunctions.slice(1)" :value="fct">{{ fct }}</option>
          </select>
          <input v-model="link" class="form-row__input" type="text" placeholder="Lien linkedIn"/>
        </div>      

        <div class="form-row" v-if="mode == 'create'">
          <textarea v-model="note" class="form-row__input" placeholder="Note"/>
        </div>      

        <div class="form-row" v-if="mode == 'create'">
          <label for="isParticipated">Je participe à l'événement</label>
          <div class="cntr">
            <input v-model="isParticipated" type="checkbox" id="isParticipated" class="hidden-xs-up">
            <label for="isParticipated" class="cbx"></label>
          </div>
        </div>

        <div class="form-row" v-if="mode == 'create'">
          <label for="isPublic">Je souhaite afficher publiquement mes informations</label>
          <div class="cntr">
            <input v-model="isPublic" type="checkbox" id="isPublic" class="hidden-xs-up">
            <label for="isPublic" class="cbx"></label>
          </div>
        </div>       

        <!-- Button form -->
        <div class="form-row">
          <button class="button" @click="login()" :class="{'button--disabled' : !validatedFields}" :disabled="!validatedFields" v-if="mode == 'login'">
            <span v-if="loading">Connexion en cours...</span>
            <span v-else>Connexion</span>
          </button>
          <button @click="createAccount()" class="button" :class="{'button--disabled' : !validatedFields}" :disabled="!validatedFields" v-else>
            <span v-if="loading">Création en cours...</span>
            <span v-else>Créer mon compte</span>
          </button>
        </div>

    </div>
  </main>

  <Footer :class="{'create-footer' : mode == 'create'}" />
</template>
  
<style scoped>
main {
  display: flex;
  align-items: center;
  justify-content: center;  
  padding-top: 80px;
  background-image: url("/img/bg-login.webp");
}

.create-footer {
  position: inherit;
}
.card {
  max-width: 100%;
  width: 540px;
  border-radius: 16px;
  padding:32px;
}

.card__title {
  text-align:center;
  font-weight: 800;
}

.card__subtitle {
  text-align: center;
  color: var(--subtitle);
  font-weight: 500;
}

.alert {
  font-weight: 600;
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

.card__action {
  color:var(--primary);
  text-decoration: underline;
}

.card__action:hover {
  cursor:pointer;
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

h1 {
  margin-top: 0px !important;
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

span {
    font-size: 20px;
}

@media (max-width: 600px) {
  .card{
    width: auto;
  }
  .form-row{
    flex-direction: column;
  }
}
</style>