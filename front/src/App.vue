<script>
    import Loader from './components/Loader.vue'
    export default {
        name: 'app',
        data: () => ({user: null}),       
        computed: {            
        },
        methods: {      
        },
        mounted() {        
          /*this.user = {
            name: "Marie"
          };*/
        },
        components: {
          Loader
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
              <a class="dropdown-item" href="/logout">Déconnexion</a>
            </div>
          </div>
        </div>
      </div>
    </nav>
  </header>
  
  <main>
      <router-view></router-view>
  </main>

  <footer class="py-3 mt-4">
    <ul class="nav justify-content-center border-bottom pb-3 mb-3">
      <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Contact</a></li>
      <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Mentions légales</a></li>
      <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Cookies</a></li>
      <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Plan d'accès</a></li>
    </ul>
    <p class="text-center text-muted text-copy">Copyright © Nathan Cleaux / Charles Rongier / Axel Dufrenay</p>
  </footer>
</template>



<style>
:root {
    --primary : #e75112;
    --secondary: #FDAF17;    
    --third: #9B9B9B;
    --loading-secondary: #ff8d5c;
    --loading-fourth: #ffda8f;
    --bg-primary: #FFFFFF;
    --bg-secondary: #F7F7F7;
    --bg-third: #CCCCCC;
    --bg-navbar: #403E3F;
    --subtitle: #666;
    --button-bg-disable: #cecece;
    --button-color-disable: #ececec;
    --input-bg: #f2f2f2;
    --input-placeholder:#aaaaaa; 
}

* {
  padding: 0;
  margin: 0;
  box-sizing: border-box;
  --color_foeee: #fff;
}

main {
  min-height: 80vh !important;
  padding:32px;
}

#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  min-height: 100%;
  max-width: 100%;
}

body {
	margin: 0;
	min-height: 100vh;
	display: grid;
	grid-template-rows: auto 1fr auto;
  background-image: linear-gradient(12deg, var(--bg-secondary) 0%, var(--bg-third) 100%);
}

main {
    min-height:100%;
    position:relative;
}

footer{
  position: absolute;
  width: 100%;
  z-index: 1;
  border: none;    
  padding-top: 1%;
  padding-bottom: 1%;    
  margin-top: 2%;
  bottom: 0;
}

.text-copy {
  font-size: 13px;
}

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

.link{
  text-decoration: none;
}

.footer-text {
  color: #e0dcdc;
}

h1 {
  margin-top: 40px !important;
}
</style>
