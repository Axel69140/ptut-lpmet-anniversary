<script>
  import { mapState } from 'vuex'
  export default {
    name: 'login',
    data: function () {
      return {
        mode: 'login',
        email: '',
        prenom: '',
        nom: '',
        password: '',
      }
    },
    mounted: function () {
      if (this.$store.state.user.userId != -1) {
        this.$router.push('/profile');
        return ;
      }
    },
    computed: {
      validatedFields: function () {
        if (this.mode == 'create') {
          if (this.email != "" && this.prenom != "" && this.nom != "" && this.password != "") {
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
      switchToCreateAccount: function () {
        this.mode = 'create';
      },
      switchToLogin: function () {
        this.mode = 'login';
      },
      login: function () {
        const self = this;
        this.$store.dispatch('login', {
          email: this.email,
          password: this.password,
        }).then(function () {
          self.$router.push('/profile');
        }, function (error) {
          console.log(error);
        })
      },
      createAccount: function () {
        const self = this;
        this.$store.dispatch('createAccount', {
          email: this.email,
          nom: this.nom,
          prenom: this.prenom,
          password: this.password,
        }).then(function () {
          self.login();
        }, function (error) {
          console.log(error);
        })
      },
    }
  }
</script>

<template>
  <div class="card container">
    <h1 class="card__title" v-if="mode == 'login'">Connexion</h1>
    <h1 class="card__title" v-else>Inscription</h1>
    <p class="card__subtitle" v-if="mode == 'login'">Tu n'as pas encore de compte ? <span class="card__action" @click="switchToCreateAccount()">Créer un compte</span></p>
    <p class="card__subtitle" v-else>Tu as déjà un compte ? <span class="card__action" @click="switchToLogin()">Se connecter</span></p>

    <!-- Input form -->
    <div class="form-row">
      <input v-model="email" class="form-row__input" type="text" placeholder="Adresse mail"/>
    </div>

    <div class="form-row" v-if="mode == 'create'">
      <input v-model="prenom" class="form-row__input" type="text" placeholder="Prénom"/>
      <input v-model="nom" class="form-row__input" type="text" placeholder="Nom"/>
    </div>

    <div class="form-row" v-if="mode == 'create'">
      <input v-model="maiden_name" class="form-row__input" type="text" placeholder="Nom de jeune fille"/>
      <input v-model="phone" class="form-row__input" type="phone" placeholder="Numéro de téléphone"/>
    </div>

    <div class="form-row" v-if="mode == 'create'">
      <input v-model="_function" class="form-row__input" type="text" placeholder="Fonction"/>
      <input v-model="link" class="form-row__input" type="text" placeholder="Lien linkedIn"/>
    </div>

    <div class="form-row" v-if="mode == 'create'">
      <input v-model="isParticipated" class="form-row__input" type="checkbox"/>
      <input v-model="isPublic" class="form-row__input" type="checkbox"/>
    </div>

    <div class="form-row">
      <input v-model="password" class="form-row__input" type="password" placeholder="Mot de passe"/>
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
</template>
  
<style scoped>
.card {
  max-width: 100%;
  width: 540px;
  border-radius: 16px;
  padding:32px;
  margin-top: 130px;
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