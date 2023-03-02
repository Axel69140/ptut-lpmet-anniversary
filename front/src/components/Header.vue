<script>
    export default {
        name: 'header',
        data: () => ({user: null}),       
        computed: {            
        },
        methods: {  
          logout: function () {
            this.$store.commit('logout');
            this.$router.push('/');
            this.checkLog();
          },
          checkLog() {
            if (this.$store.state.user.userId != -1) {
              this.user = this.$store.state.user;
            }   
          } 
        },
        mounted() {  
          if (this.$store.state.user.userId != -1) {
            this.user = this.$store.state.user;
          }     
        }
    }
</script>

<template>
    <header>
    <nav class="navbar navbar-expand-lg navbar-dark">
      <a class="navbar-brand" href="/">LOGO</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbar">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="/event" id="dropdown0" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Événement</a>
            <div class="dropdown-menu" aria-labelledby="dropdown0">
              <a class="dropdown-item" href="/event">Voir l'événement</a>
              <a class="dropdown-item" href="/event/registration">Participer</a>
              <a class="dropdown-item" href="/users">Liste des participants</a>
              <a class="dropdown-item" href="/activity/form">Proposer une activité</a>    
            </div>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="/articles" id="dropdown1" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Articles</a>
            <div class="dropdown-menu" aria-labelledby="dropdown1">
              <a class="dropdown-item" href="/articles">Voir</a>
              <a class="dropdown-item" href="/article/form">Proposer un article</a>
            </div>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="/" id="dropdown2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Anecdotes</a>
            <div class="dropdown-menu" aria-labelledby="dropdown2">
              <a class="dropdown-item" href="/anecdote/form">Proposer une anecdote</a>
            </div>
          </li>
        </ul>

        <div class="form-inline  my-md-0">
          <a v-if="user === null" class="nav-link" href="/login">Connexion</a>
          <div v-if="user !== null" class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="/" id="dropdown3" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{user.name}}</a>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown3">
              <a class="dropdown-item" @click="logout()">Déconnexion</a>
            </div>
          </div>
        </div>
      </div>
    </nav>
  </header>
</template>

<style scoped>
.navbar {
    position: relative;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    background-color: var(--bg-navbar);
    padding: 0.5rem 1rem !important;
}

.navbar .nav-link {
    color: var(--bg-secondary) !important;
}
</style>