<script>
  import { mapState } from 'vuex';
  import Footer from './../../components/Footer.vue';

  export default {
    name: 'login',
    data: function () {
      return {
        mode: 'login',
        email: '',
        firstName: '',
        lastName: '',
        maidenName: '',
        password: '',
        password_confirmation: '',
        phone: '',
        activeYears: '',
        activeYears2: '',
        _function: '',
        link: '',
        note: '',
        isParticipated: '',
        isPublic: ''
      }
    },
    mounted() {
      if (this.$store.state.user.userId != -1) {
        this.$router.push('/');
        return ;
      }
    },
    computed: {
      validatedFields: function () {
        if (this.mode == 'create') {
          if (this.email != "" && this.firstName != "" && this.lastName != "" && this.password != "" && this.password_confirmation != "") {
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
        const self = this;
        this.$store.dispatch('login', {
          email: this.email,
          password: this.password,
        }).then(function () {
          self.$router.push('/');
        }, function (error) {
          console.log(error);
        })
      },
      createAccount() {
        const self = this;
        this.$store.dispatch('createAccount', {
          email: this.email,
          nom: this.lastName,
          prenom: this.firstName,
          password: this.password,
        }).then(function () {
          self.login();
        }, function (error) {
          console.log(error);
        })
      },
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

      <!-- Input form -->
      <div class="form-row" v-if="mode == 'create'">
        <input v-model="firstName" class="form-row__input" type="text" placeholder="Prénom*"/>
        <input v-model="lastName" class="form-row__input" type="text" placeholder="Nom*"/>
      </div>

      <div class="form-row" v-if="mode == 'create'">
        <input v-model="maidenName" class="form-row__input" type="text" placeholder="Nom de jeune fille"/>
        <input v-model="phone" class="form-row__input" type="tel" placeholder="Numéro de téléphone"/>
      </div>

      <div class="form-row">
        <input v-model="email" class="form-row__input" type="text" placeholder="Adresse mail*"/>
      </div>

      <div class="form-row">
        <input v-model="password" class="form-row__input" type="password" placeholder="Mot de passe*"/>
      </div>

      <div class="form-row" v-if="mode == 'create'">
        <input v-model="password_confirmation" class="form-row__input" type="password" placeholder="Confirmer mot de passe*"/>
      </div>

      <div class="form-row" v-if="mode == 'create'">
        <label for="activeYears">Année d'activité à l'IUT*</label>
        <select name="activeYears" id="activeYears">
          <option v-for="year in range(1993, 2023)" v-bind:key="year" v-bind:value="year">{{ year }}</option>
        </select>

        <select name="activeYears2" id="activeYears2">
          <option v-for="year in range(1993, 2023)" v-bind:key="year" v-bind:value="year">{{ year }}</option>
        </select>
      </div>

      <div class="form-row" v-if="mode == 'create'">
        <input v-model="_function" class="form-row__input" type="text" placeholder="Fonction"/>
        <input v-model="link" class="form-row__input" type="text" placeholder="Lien linkedIn"/>
      </div>      

      <div class="form-row" v-if="mode == 'create'">
        <textarea v-model="note" class="form-row__input" placeholder="Note"/>
      </div>      

      <div class="form-row" v-if="mode == 'create'">
        <label for="isParticipated">Je participe à l'événement</label>
        <input v-model="isParticipated" type="checkbox"/>
      </div>

      <div class="form-row" v-if="mode == 'create'">
        <label for="isPublic">Je souhaite afficher publiquement mes informations</label>
        <input v-model="isPublic" type="checkbox"/>
      </div>     

      <!-- Error form -->
      <div class="form-row" v-if="mode == 'login' && status == 'error_login'">
        Adresse mail et/ou mot de passe invalide
      </div>    
      <div class="form-row" v-if="mode == 'create' && status == 'error_create'">
        Adresse mail déjà utilisée
      </div>

      <!-- Button form -->
      <div class="form-row">
        <button @click="login()" class="button" :class="{'button--disabled' : !validatedFields}" v-if="mode == 'login'">
          <span v-if="status == 'loading'">Connexion en cours...</span>
          <span v-else>Connexion</span>
        </button>
        <button @click="createAccount()" class="button" :class="{'button--disabled' : !validatedFields}" v-else>
          <span v-if="status == 'loading'">Création en cours...</span>
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
}

.create-footer {
  position: inherit !important;
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

.form-row {
  display: flex;
  margin: 16px 0px;
  gap:16px;
  flex-wrap: wrap;
}

.form-row__input {
  padding:8px;
  border: none;
  border-radius: 8px;
  background: var(--input-bg);
  font-weight: 500;
  font-size: 16px;
  flex:1;
  min-width: 100px;
  color: black;
}

.form-row__input::placeholder {
  color: var(--input-placeholder);
}

h1 {
  margin-top: 0px !important;
}
</style>